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
        // Get all scheduled events from Laravel
        // The schedule is automatically loaded by the Console Kernel
        $schedule = app(Schedule::class);

        // Ensure the schedule is populated by calling the Kernel's schedule method
        app(\Illuminate\Contracts\Console\Kernel::class);

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
            // Extract command name from full command string
            $commandName = $this->extractCommandName($command);

            if (!$commandName) {
                return back()->with('error', 'Invalid command format');
            }

            // Run the command
            Artisan::call($commandName);
            $output = Artisan::output();

            return back()->with('success', "Command executed successfully!\n\nOutput:\n" . $output);
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
            $phpBinary = PHP_BINARY;
            $artisanPath = base_path('artisan');

            $command = sprintf(
                'nohup "%s" "%s" scheduler:run --daemon --interval=60 >> "%s" 2>&1 & echo $!',
                $phpBinary,
                $artisanPath,
                $logPath
            );

            $pid = exec($command);

            if ($pid) {
                File::put($pidFile, $pid);
                return back()->with('success', "Scheduler daemon started successfully! (PID: $pid)\n\nLogs will be written to: $logPath");
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
            $phpBinary = PHP_BINARY;
            $artisanPath = base_path('artisan');

            $command = sprintf(
                'nohup "%s" "%s" scheduler:run --daemon --interval=60 >> "%s" 2>&1 & echo $!',
                $phpBinary,
                $artisanPath,
                $logPath
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
     * Check if a process is running by PID
     */
    protected function isProcessRunning($pid)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            exec("tasklist /FI \"PID eq $pid\" 2>NUL", $output);
            return count($output) > 1;
        } else {
            exec("ps -p $pid > /dev/null 2>&1", $output, $returnCode);
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
            return [
                'running' => true,
                'message' => "Scheduler daemon is running (PID: $pid)",
                'pid' => $pid,
                'color' => 'green'
            ];
        } else {
            return [
                'running' => false,
                'message' => "Scheduler daemon is not running (stale PID: $pid)",
                'pid' => $pid,
                'color' => 'orange'
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
     * Extract command name from full command string
     */
    protected function extractCommandName($command)
    {
        // Handle "php artisan command:name"
        if (preg_match('/php\s+artisan\s+([^\s]+)/', $command, $matches)) {
            return $matches[1];
        }

        // Handle "'artisan' command:name"
        if (preg_match("/'artisan'\s+([^\s]+)/", $command, $matches)) {
            return $matches[1];
        }

        // Handle direct command name
        if (preg_match('/^[a-z0-9:-]+$/i', $command)) {
            return $command;
        }

        return null;
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
        exec('ps aux 2>/dev/null | grep -E "scheduler:run|schedule:run" | grep -v grep', $processOutput);

        if (!empty($processOutput)) {
            return [
                'running' => true,
                'message' => "Scheduler running in this container",
                'pid' => 'docker-same-container',
                'color' => 'green',
                'deployment_type' => 'docker'
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
}
