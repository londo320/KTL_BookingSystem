<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class SchedulerController extends Controller
{
    /**
     * Display the scheduler management interface
     */
    public function index()
    {
        // In Laravel 12, scheduled tasks are defined in routes/console.php
        // Bootstrap the kernel to load them
        $kernel = app(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        // Get the Schedule instance and all registered events
        $schedule = app(Schedule::class);
        $events = $schedule->events();

        $scheduledTasks = [];

        foreach ($events as $event) {
            $scheduledTasks[] = [
                'command' => $event->command ?? $event->description ?? 'Closure',
                'description' => $event->description ?? 'No description',
                'expression' => $event->expression,
                'timezone' => $event->timezone ?? config('app.timezone'),
                'next_run' => $this->getNextRunDate($event->expression, $event->timezone ?? config('app.timezone')),
                'mutex' => $event->mutex,
                'without_overlapping' => property_exists($event, 'withoutOverlapping') ? (bool) $event->withoutOverlapping : false,
            ];
        }

        // Get scheduler daemon status
        $daemonStatus = $this->getSchedulerDaemonStatus();

        // Get recent logs
        $recentLogs = $this->getRecentLogs();

        return view('admin.scheduler.index', compact('scheduledTasks', 'daemonStatus', 'recentLogs'));
    }

    /**
     * Run a specific scheduled command manually
     */
    public function runCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
        ]);

        $command = $request->input('command');

        try {
            // Check daemon status before running command
            $pidFile = storage_path('scheduler.pid');
            $daemonPidBefore = File::exists($pidFile) ? trim(File::get($pidFile)) : 'none';

            // Extract command name and parameters from full command string
            $parsed = $this->extractCommandAndParams($command);

            if (!$parsed) {
                return back()->with('error', 'Invalid command format: ' . $command);
            }

            // Run the command with parameters
            $startTime = microtime(true);

            // Create output buffer to capture command output
            $outputBuffer = new \Symfony\Component\Console\Output\BufferedOutput();
            Artisan::call($parsed['command'], $parsed['params'], $outputBuffer);

            $output = trim($outputBuffer->fetch());
            $duration = round((microtime(true) - $startTime) * 1000, 2);

            // Check daemon status after running command
            $daemonPidAfter = File::exists($pidFile) ? trim(File::get($pidFile)) : 'none';

            // In Docker/production, trust the daemon status if we can't check processes
            $daemonStatus = $this->getSchedulerDaemonStatus();
            $isDockerSeparateContainer = ($daemonStatus['deployment_type'] ?? 'local') === 'docker';

            if ($isDockerSeparateContainer) {
                // Separate Docker container - trust daemon status (can't check processes across containers)
                $stillRunning = $daemonStatus['running'];
                $processDebug = "Environment: Separate Docker container\nDaemon check: Trusting daemon status (container isolation)\n";
            } else {
                // Local or same container - do detailed process check
                $stillRunning = $daemonPidAfter !== 'none' && $this->isProcessRunning($daemonPidAfter);
                $processDebug = $this->getProcessDebugInfo($daemonPidAfter);
            }

            // Check for command-specific log files if there's no direct output
            $logHint = '';
            if (empty($output)) {
                $logFile = $this->getLogFileForCommand($parsed['command']);
                if ($logFile && File::exists(storage_path("logs/{$logFile}"))) {
                    $logLines = $this->tail(storage_path("logs/{$logFile}"), 10);
                    $logContent = implode("\n", $logLines);
                    if (!empty(trim($logContent))) {
                        $logHint = "\n\n📄 Recent log entries from {$logFile}:\n" .
                                   "─────────────────────────────────────\n" .
                                   $logContent . "\n" .
                                   "─────────────────────────────────────";
                    }
                }
            }

            $debugInfo = "\n\n" .
                "═══════════════════════════════════════\n" .
                "Debug Info:\n" .
                "Command: {$parsed['command']}\n" .
                "Parameters: " . json_encode($parsed['params']) . "\n" .
                "Duration: {$duration}ms\n" .
                "Daemon PID before: {$daemonPidBefore}\n" .
                "Daemon PID after: {$daemonPidAfter}\n" .
                "Daemon still running: " . ($stillRunning ? 'YES ✅' : 'NO ❌') . "\n" .
                $processDebug .
                "═══════════════════════════════════════";

            return back()->with('success', "Command executed successfully!\n\nOutput:\n" . ($output ?: '(command completed silently)') . $logHint . $debugInfo);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to run command: ' . $e->getMessage());
        }
    }

    /**
     * Run the entire scheduler once
     */
    public function runScheduler()
    {
        try {
            Artisan::call('schedule:run');
            $output = Artisan::output();

            return back()->with('success', "Scheduler executed successfully!\n\n" . $output);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to run scheduler: ' . $e->getMessage());
        }
    }

    /**
     * Get the logs for a specific task
     */
    public function getLogs(Request $request)
    {
        $logFile = $request->input('log_file', 'scheduler.log');

        // Sanitize log file name to prevent path traversal
        $logFile = basename($logFile);
        $logPath = storage_path('logs/' . $logFile);

        if (!File::exists($logPath)) {
            return response()->json([
                'error' => 'Log file not found',
                'content' => ''
            ]);
        }

        // Get last 500 lines
        $lines = $this->tail($logPath, 500);

        return response()->json([
            'content' => implode("\n", $lines),
            'file' => $logFile
        ]);
    }

    /**
     * Check scheduler daemon status
     */
    public function checkStatus()
    {
        $status = $this->getSchedulerDaemonStatus();

        return response()->json($status);
    }

    /**
     * Start the scheduler daemon
     */
    public function startDaemon()
    {
        try {
            // Check if running in Docker
            $isInDocker = file_exists('/.dockerenv') || (file_exists('/proc/1/cgroup') && strpos(file_get_contents('/proc/1/cgroup'), 'docker') !== false);

            if ($isInDocker) {
                // In Docker, start scheduler as background process in same container
                exec('nohup php artisan scheduler:run --daemon --interval=60 >> /var/www/html/storage/logs/scheduler.log 2>&1 & echo $!', $output);
                $pid = isset($output[0]) ? trim($output[0]) : null;

                if ($pid) {
                    return back()->with('success', "Scheduler started in Docker container (PID: $pid)\n\nRefresh the page to see the updated status.");
                } else {
                    return back()->with('error', 'Failed to start scheduler in Docker container. Try running: bash start-scheduler-docker.sh');
                }
            }

            $pidFile = storage_path('scheduler.pid');

            // Check if already running
            if (File::exists($pidFile)) {
                $pid = trim(File::get($pidFile));

                // Check if process is actually running
                if ($this->isProcessRunning($pid)) {
                    return back()->with('warning', "Scheduler daemon is already running (PID: $pid)");
                }

                // Remove stale PID file
                File::delete($pidFile);
            }

            // Start the scheduler daemon
            $logPath = storage_path('logs/scheduler.log');
            // Use wrapper script from custom Application class to avoid issues with spaces in PHP path
            $phpBinary = app()->phpBinary();
            $artisanPath = base_path('artisan');

            // Use escapeshellarg to properly handle paths with spaces
            $command = sprintf(
                'nohup %s %s scheduler:run --daemon --interval=60 >> %s 2>&1 & echo $!',
                escapeshellarg($phpBinary),
                escapeshellarg($artisanPath),
                escapeshellarg($logPath)
            );

            $pid = exec($command);

            if ($pid) {
                File::put($pidFile, $pid);
                return back()->with('success',
                    "Scheduler daemon started successfully! (PID: $pid)\n\n" .
                    "PHP Binary: $phpBinary\n" .
                    "Logs: $logPath\n\n" .
                    "Command: $command"
                );
            } else {
                return back()->with('error', 'Failed to start scheduler daemon. Please check permissions.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start scheduler: ' . $e->getMessage());
        }
    }

    /**
     * Stop the scheduler daemon
     */
    public function stopDaemon()
    {
        try {
            $pidFile = storage_path('scheduler.pid');

            if (!File::exists($pidFile)) {
                return back()->with('warning', 'Scheduler daemon is not running (no PID file found)');
            }

            $pid = trim(File::get($pidFile));

            if (!$this->isProcessRunning($pid)) {
                File::delete($pidFile);
                return back()->with('warning', "Scheduler daemon was not running (stale PID: $pid). Cleaned up PID file.");
            }

            // Try graceful kill first
            if (PHP_OS_FAMILY === 'Windows') {
                exec("taskkill /PID $pid /F");
            } else {
                exec("kill $pid");
            }

            sleep(1);

            // Check if still running, force kill if necessary
            if ($this->isProcessRunning($pid)) {
                if (PHP_OS_FAMILY === 'Windows') {
                    exec("taskkill /PID $pid /F");
                } else {
                    exec("kill -9 $pid");
                }
            }

            File::delete($pidFile);

            return back()->with('success', "Scheduler daemon stopped successfully (PID: $pid)");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to stop scheduler: ' . $e->getMessage());
        }
    }

    /**
     * Restart the scheduler daemon
     */
    public function restartDaemon()
    {
        try {
            $pidFile = storage_path('scheduler.pid');

            // Stop if running
            if (File::exists($pidFile)) {
                $pid = trim(File::get($pidFile));

                if ($this->isProcessRunning($pid)) {
                    if (PHP_OS_FAMILY === 'Windows') {
                        exec("taskkill /PID $pid /F");
                    } else {
                        exec("kill $pid");
                        sleep(1);
                        if ($this->isProcessRunning($pid)) {
                            exec("kill -9 $pid");
                        }
                    }
                }

                File::delete($pidFile);
            }

            // Wait a moment
            sleep(1);

            // Start fresh
            $logPath = storage_path('logs/scheduler.log');
            // Use wrapper script from custom Application class to avoid issues with spaces in PHP path
            $phpBinary = app()->phpBinary();
            $artisanPath = base_path('artisan');

            // Use escapeshellarg to properly handle paths with spaces
            $command = sprintf(
                'nohup %s %s scheduler:run --daemon --interval=60 >> %s 2>&1 & echo $!',
                escapeshellarg($phpBinary),
                escapeshellarg($artisanPath),
                escapeshellarg($logPath)
            );

            $pid = exec($command);

            if ($pid) {
                File::put($pidFile, $pid);
                return back()->with('success', "Scheduler daemon restarted successfully! (New PID: $pid)");
            } else {
                return back()->with('error', 'Failed to restart scheduler daemon. Please check permissions.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restart scheduler: ' . $e->getMessage());
        }
    }

    /**
     * Get detailed debug info about process check
     */
    protected function getProcessDebugInfo($pid)
    {
        if (empty($pid) || !is_numeric($pid)) {
            return "Process check: Invalid PID\n";
        }

        if (PHP_OS_FAMILY === 'Windows') {
            return "Process check: Windows (not detailed)\n";
        }

        $checks = [];

        // Check 1: /proc filesystem
        $procExists = file_exists("/proc/{$pid}");
        $checks[] = "/proc/{$pid} exists: " . ($procExists ? 'YES' : 'NO');

        if ($procExists) {
            $cmdline = @file_get_contents("/proc/{$pid}/cmdline");
            $hasScheduler = $cmdline && strpos($cmdline, 'scheduler:run') !== false;
            $checks[] = "Contains 'scheduler:run': " . ($hasScheduler ? 'YES' : 'NO');
        }

        // Check 2: ps command
        exec("ps -p $pid -o comm= 2>/dev/null", $psOutput, $psReturn);
        $checks[] = "ps command works: " . ($psReturn === 0 ? 'YES' : 'NO');

        // Check 3: kill -0
        exec("kill -0 $pid 2>/dev/null", $killOutput, $killReturn);
        $checks[] = "kill -0 works: " . ($killReturn === 0 ? 'YES' : 'NO');

        // Check for any scheduler:run processes
        exec("ps aux 2>/dev/null | grep 'scheduler:run' | grep -v grep | wc -l", $countOutput);
        $schedulerCount = (int) trim(implode('', $countOutput));
        $checks[] = "Total scheduler:run processes: {$schedulerCount}";

        return "Process checks:\n  • " . implode("\n  • ", $checks) . "\n";
    }

    /**
     * Check if a process is running by PID
     */
    protected function isProcessRunning($pid)
    {
        if (empty($pid) || !is_numeric($pid)) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
            return count($output) > 1;
        } else {
            // Try multiple methods for better compatibility

            // Method 1: Check /proc filesystem (most reliable on Linux)
            if (file_exists("/proc/{$pid}")) {
                // Verify it's actually a scheduler process
                $cmdline = @file_get_contents("/proc/{$pid}/cmdline");
                if ($cmdline && strpos($cmdline, 'scheduler:run') !== false) {
                    return true;
                }
                // If we can't read cmdline but /proc exists, assume it's running
                return true;
            }

            // Method 2: Use ps command
            exec("ps -p $pid -o comm= 2>/dev/null", $output, $returnCode);
            if ($returnCode === 0 && !empty($output)) {
                return true;
            }

            // Method 3: Use kill -0 (doesn't actually kill, just checks if process exists)
            exec("kill -0 $pid 2>/dev/null", $output, $returnCode);
            return $returnCode === 0;
        }
    }

    /**
     * Get scheduler daemon status from PID file
     */
    protected function getSchedulerDaemonStatus()
    {
        // First, check if running in Docker with dedicated scheduler container
        $dockerSchedulerStatus = $this->checkDockerSchedulerContainer();
        if ($dockerSchedulerStatus) {
            return $dockerSchedulerStatus;
        }

        // Otherwise, check for local process via PID file
        $pidFile = storage_path('scheduler.pid');

        if (!File::exists($pidFile)) {
            return [
                'running' => false,
                'message' => 'Scheduler daemon is not running',
                'pid' => null,
                'color' => 'red',
                'deployment_type' => 'local'
            ];
        }

        $pid = trim(File::get($pidFile));

        // Check if process is actually running
        if (PHP_OS_FAMILY === 'Windows') {
            // Windows check
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
            $running = count($output) > 1;
        } else {
            // Unix/Linux/Mac check
            exec("ps -p $pid > /dev/null 2>&1", $output, $returnCode);
            $running = $returnCode === 0;
        }

        if ($running) {
            // Double-check by looking at the actual process
            exec("ps -p $pid -o command=", $processCommand);
            $commandLine = implode('', $processCommand);

            return [
                'running' => true,
                'message' => "Scheduler daemon is running (PID: $pid)",
                'pid' => $pid,
                'color' => 'green',
                'debug' => substr($commandLine, 0, 100)
            ];
        } else {
            // Check if any scheduler:run processes are actually running
            exec("ps aux | grep 'scheduler:run' | grep -v grep | wc -l", $count);
            $actualCount = (int) trim(implode('', $count));

            return [
                'running' => false,
                'message' => "Scheduler daemon is not running (stale PID: $pid)",
                'pid' => $pid,
                'color' => 'orange',
                'debug' => "Found $actualCount scheduler:run processes"
            ];
        }
    }

    /**
     * Get recent logs from various scheduler log files
     */
    protected function getRecentLogs()
    {
        $logFiles = [
            'scheduler.log',
            'slots_generate.log',
            'auto_release_slots.log',
            'bay_sync.log',
            'booking_cleanup.log',
        ];

        $logs = [];

        foreach ($logFiles as $logFile) {
            $logPath = storage_path('logs/' . $logFile);

            if (File::exists($logPath)) {
                $lines = $this->tail($logPath, 10);
                $logs[$logFile] = [
                    'exists' => true,
                    'content' => implode("\n", $lines),
                    'last_modified' => date('Y-m-d H:i:s', filemtime($logPath)),
                ];
            } else {
                $logs[$logFile] = [
                    'exists' => false,
                    'content' => 'Log file not found',
                    'last_modified' => null,
                ];
            }
        }

        return $logs;
    }

    /**
     * Get the next run date for a cron expression
     */
    protected function getNextRunDate($expression, $timezone)
    {
        try {
            $cron = new \Cron\CronExpression($expression);
            return $cron->getNextRunDate('now', 0, false, $timezone)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return 'Invalid expression';
        }
    }

    /**
     * Extract command name and parameters from full command string
     */
    protected function extractCommandAndParams($command)
    {
        $params = [];

        // Handle "'/path/to/php' 'artisan' command:name --param=value"
        // This is the format from Laravel's scheduler
        if (preg_match("/'[^']*php[^']*'\\s+'artisan'\\s+([^\\s]+)(.*)$/", $command, $matches)) {
            $commandName = $matches[1];
            $paramString = trim($matches[2] ?? '');

            // Parse parameters
            if ($paramString) {
                // Match --param=value or --param value
                if (preg_match_all('/--([^\s=]+)(?:=([^\s]+))?/', $paramString, $paramMatches, PREG_SET_ORDER)) {
                    foreach ($paramMatches as $match) {
                        $paramName = $match[1];
                        $paramValue = $match[2] ?? true;
                        $params['--' . $paramName] = $paramValue;
                    }
                }
            }

            return ['command' => $commandName, 'params' => $params];
        }

        // Handle "php artisan command:name --param=value"
        if (preg_match('/php\s+artisan\s+([^\s]+)(.*)/', $command, $matches)) {
            $commandName = $matches[1];
            $paramString = trim($matches[2] ?? '');

            // Parse parameters
            if ($paramString) {
                // Match --param=value or --param value
                if (preg_match_all('/--([^\s=]+)(?:=([^\s]+))?/', $paramString, $paramMatches, PREG_SET_ORDER)) {
                    foreach ($paramMatches as $match) {
                        $paramName = $match[1];
                        $paramValue = $match[2] ?? true;
                        $params['--' . $paramName] = $paramValue;
                    }
                }
            }

            return ['command' => $commandName, 'params' => $params];
        }

        // Handle direct command name with params
        if (preg_match('/^([a-z0-9:-]+)(.*)$/i', $command, $matches)) {
            $commandName = $matches[1];
            $paramString = trim($matches[2] ?? '');

            // Parse parameters
            if ($paramString) {
                if (preg_match_all('/--([^\s=]+)(?:=([^\s]+))?/', $paramString, $paramMatches, PREG_SET_ORDER)) {
                    foreach ($paramMatches as $match) {
                        $paramName = $match[1];
                        $paramValue = $match[2] ?? true;
                        $params['--' . $paramName] = $paramValue;
                    }
                }
            }

            return ['command' => $commandName, 'params' => $params];
        }

        return null;
    }

    /**
     * Extract command name from full command string (legacy method)
     */
    protected function extractCommandName($command)
    {
        // Handle "'/usr/local/bin/php' 'artisan' command:name"
        if (preg_match("/'php'\\s+'artisan'\\s+([^\\s]+)/", $command, $matches)) {
            return $matches[1];
        }

        // Handle "'artisan' command:name"
        if (preg_match("/'artisan'\\s+([^\\s]+)/", $command, $matches)) {
            return $matches[1];
        }

        // Handle "php artisan command:name"
        if (preg_match('/php\\s+artisan\\s+([^\\s]+)/', $command, $matches)) {
            return $matches[1];
        }

        // Handle "'/path/to/php' 'artisan' command:name"
        if (preg_match("/'[^']*php'\\s+'artisan'\\s+([^\\s]+)/", $command, $matches)) {
            return $matches[1];
        }

        // Handle direct command name
        if (preg_match('/^[a-z0-9:-]+$/i', $command)) {
            return $command;
        }

        return null;
    }

    /**
     * Get the log file name for a given command
     */
    protected function getLogFileForCommand($command)
    {
        $mapping = [
            'slots:generate-dynamic' => 'slots_generate.log',
            'slots:generate' => 'slots_generate.log',
            'slots:generate-by-bay' => 'slots_generate.log',
            'app:auto-release-slots' => 'auto_release_slots.log',
            'bays:sync-occupancy' => 'bay_sync.log',
            'bookings:cleanup-incomplete' => 'booking_cleanup.log',
        ];

        return $mapping[$command] ?? null;
    }

    /**
     * Get last N lines from a file (like tail command)
     */
    protected function tail($file, $lines = 100)
    {
        $handle = fopen($file, 'r');
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = [];

        while ($linecounter > 0) {
            $t = ' ';
            while ($t != "\n") {
                if (fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos--;
            }
            $linecounter--;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines - $linecounter - 1] = fgets($handle);
            if ($beginning) {
                break;
            }
        }

        fclose($handle);
        return array_reverse($text);
    }

    /**
     * Check if scheduler is running in a Docker container
     */
    protected function checkDockerSchedulerContainer()
    {
        // Check if we're inside a Docker container
        $isInDocker = file_exists('/.dockerenv') || (file_exists('/proc/1/cgroup') && strpos(file_get_contents('/proc/1/cgroup'), 'docker') !== false);

        if (!$isInDocker) {
            return null; // Not in Docker, use regular PID detection
        }

        // Check if scheduler process is running in THIS container
        // Don't just check if ANY scheduler exists - check our PID file matches reality
        $pidFile = storage_path('scheduler.pid');

        if (File::exists($pidFile)) {
            $pid = trim(File::get($pidFile));

            // Check if this specific PID is running
            exec("ps -p $pid -o command= 2>/dev/null | grep scheduler:run", $specificProcess);

            if (!empty($specificProcess)) {
                return [
                    'running' => true,
                    'message' => "Scheduler daemon running (PID: $pid)",
                    'pid' => $pid,
                    'color' => 'green',
                    'deployment_type' => 'local' // Same container = allow control
                ];
            }
        }

        // If no valid PID file, check for any scheduler process
        exec('ps aux 2>/dev/null | grep -E "scheduler:run|schedule:run" | grep -v grep', $processOutput);

        if (!empty($processOutput)) {
            return [
                'running' => false, // Not our daemon, but one exists
                'message' => "Scheduler process found but not managed by admin panel",
                'pid' => null,
                'color' => 'orange',
                'deployment_type' => 'local',
                'warning' => 'A scheduler is running but not controlled by this panel'
            ];
        }

        // If SCHEDULER_MODE is set to 'docker', assume scheduler runs in separate container
        if (env('SCHEDULER_MODE') === 'docker') {
            return [
                'running' => true,
                'message' => "Scheduler running in dedicated Docker container",
                'pid' => 'docker',
                'container' => env('SCHEDULER_CONTAINER_NAME', 'ktl-booking-scheduler'),
                'color' => 'green',
                'deployment_type' => 'docker'
            ];
        }

        // Try to check if scheduler container exists and is running
        // This command checks for a container with "scheduler" in the name
        exec('docker ps --filter "name=scheduler" --format "{{.Names}}" 2>/dev/null', $output, $returnCode);

        // If docker command is not available or fails, fall back to checking local process
        if ($returnCode !== 0) {
            return null;
        }

        $schedulerContainers = array_filter($output, function($name) {
            return stripos($name, 'scheduler') !== false;
        });

        if (!empty($schedulerContainers)) {
            $containerName = reset($schedulerContainers);

            // Check if the scheduler process is running inside the container
            exec("docker exec $containerName ps aux 2>/dev/null | grep -E 'scheduler:run|schedule:run' | grep -v grep", $processOutput);

            if (!empty($processOutput)) {
                return [
                    'running' => true,
                    'message' => "Scheduler running in Docker container: $containerName",
                    'pid' => 'docker',
                    'container' => $containerName,
                    'color' => 'green',
                    'deployment_type' => 'docker'
                ];
            }
        }

        return null; // No Docker scheduler found, fall back to PID detection
    }

    /**
     * Parse the output of schedule:list command
     */
    protected function parseScheduleListOutput($output)
    {
        $tasks = [];
        $lines = explode("\n", $output);

        foreach ($lines as $line) {
            // Skip empty lines
            if (empty(trim($line))) {
                continue;
            }

            // Try to parse lines that contain "Next Due:"
            // Format examples:
            // "  15   0 * * *  Auto-generate slots using configured method (bay or template)  Next Due: in 5 hours"
            // "  */15 * * * *  php artisan app:auto-release-slots .... Next Due: in 4 minutes"
            if (strpos($line, 'Next Due:') !== false) {
                // Extract cron expression (first 5 fields)
                if (preg_match('/^\s*([\d\*\/,-]+\s+[\d\*\/,-]+\s+[\d\*\/,-]+\s+[\d\*\/,-]+\s+[\d\*\/,-]+)\s+(.+?)\s+Next Due:\s*(.+)$/i', $line, $matches)) {
                    $expression = trim($matches[1]);
                    $description = trim($matches[2]);
                    $nextDue = trim($matches[3]);

                    // Clean up description (remove extra dots)
                    $description = preg_replace('/\s*\.{2,}\s*$/', '', $description);

                    $tasks[] = [
                        'command' => $description,
                        'description' => $description,
                        'expression' => $expression,
                        'timezone' => config('app.timezone'),
                        'next_run' => $nextDue,
                        'mutex' => null,
                        'without_overlapping' => strpos($description, 'without overlapping') !== false,
                    ];
                }
            }
        }

        return $tasks;
    }
}
