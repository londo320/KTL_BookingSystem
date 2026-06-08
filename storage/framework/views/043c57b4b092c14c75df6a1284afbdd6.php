<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">⏰ Scheduler Management</h2>
            <div class="flex gap-2">
                <button onclick="checkStatus()" class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                    🔄 Refresh Status
                </button>
                <form action="<?php echo e(route('admin.scheduler.run')); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        ▶️ Run All Tasks Now
                    </button>
                </form>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 max-w-7xl mx-auto space-y-6">
        
        <?php if(session('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <pre class="text-sm whitespace-pre-wrap"><?php echo e(session('success')); ?></pre>
            </div>
        <?php endif; ?>

        <?php if(session('warning')): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                <strong>Warning:</strong> <?php echo e(session('warning')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong>Error:</strong> <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">
                    <?php if($daemonStatus['running']): ?>
                        🟢 Scheduler Daemon Status
                    <?php else: ?>
                        🔴 Scheduler Daemon Status
                    <?php endif; ?>
                </h3>
                <?php if(($daemonStatus['deployment_type'] ?? 'local') === 'local'): ?>
                    <div class="flex gap-2">
                        <?php if($daemonStatus['running']): ?>
                            <form action="<?php echo e(route('admin.scheduler.stop')); ?>" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to stop the scheduler daemon?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                    ⏹️ Stop
                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.scheduler.restart')); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                    🔄 Restart
                                </button>
                            </form>
                        <?php else: ?>
                            <form action="<?php echo e(route('admin.scheduler.start')); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    ▶️ Start Daemon
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="daemon-status" class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full <?php echo e($daemonStatus['running'] ? 'bg-green-500' : 'bg-red-500'); ?>"></div>
                <div>
                    <p class="font-semibold"><?php echo e($daemonStatus['message']); ?></p>
                    <?php if($daemonStatus['running']): ?>
                        <?php if(($daemonStatus['deployment_type'] ?? 'local') === 'docker'): ?>
                            <p class="text-sm text-gray-600">🐳 Running in Docker container - managed via Docker</p>
                            <p class="text-sm text-gray-500 mt-1">Container: <code class="bg-gray-100 px-2 py-0.5 rounded"><?php echo e($daemonStatus['container'] ?? 'scheduler'); ?></code></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-600">The scheduler daemon is checking for tasks every 60 seconds</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if(($daemonStatus['deployment_type'] ?? 'local') === 'docker'): ?>
                            <p class="text-sm text-red-600">🐳 Docker scheduler container not found or not running</p>
                            <p class="text-sm text-gray-500 mt-1">Check Docker container status: <code class="bg-gray-100 px-2 py-0.5 rounded">docker ps | grep scheduler</code></p>
                        <?php else: ?>
                            <p class="text-sm text-red-600">Click the "Start Daemon" button above to start the scheduler</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if(!$daemonStatus['running']): ?>
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800 font-semibold mb-2">⚠️ Scheduler Not Running</p>
                    <p class="text-sm text-gray-700 mb-2">The scheduler daemon is not running. Your scheduled tasks will not execute automatically.</p>
                    <div class="text-sm space-y-1">
                        <p><strong>Quick Start:</strong> Click the <span class="bg-green-600 text-white px-2 py-0.5 rounded text-xs">▶️ Start Daemon</span> button above</p>
                        <p><strong>Command Line:</strong> Run <code class="bg-gray-100 px-2 py-1 rounded">bash start-scheduler.sh</code></p>
                        <p><strong>Production:</strong> See <code class="bg-gray-100 px-2 py-1 rounded">SCHEDULER-SETUP.md</code> for systemd/Docker setup</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">📅 Scheduled Tasks (<?php echo e(count($scheduledTasks)); ?> tasks)</h3>

            <div class="space-y-2">
                <?php $__currentLoopData = $scheduledTasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border border-gray-200 rounded p-3 hover:bg-gray-50">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-900 text-sm"><?php echo e($task['description']); ?></h4>
                                <div class="flex items-center gap-2 mt-1">
                                    <code class="text-xs text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded"><?php echo e($task['expression']); ?></code>
                                    <span class="text-xs text-gray-500">Next: <?php echo e($task['next_run']); ?></span>
                                    <?php if(str_contains($task['command'], 'generate')): ?>
                                        <button onclick="viewLog('slots_generate.log')" class="text-xs text-blue-600 hover:underline">
                                            📄 slots_generate.log
                                        </button>
                                    <?php elseif(str_contains($task['command'], 'auto-release')): ?>
                                        <button onclick="viewLog('auto_release_slots.log')" class="text-xs text-blue-600 hover:underline">
                                            📄 auto_release_slots.log
                                        </button>
                                    <?php elseif(str_contains($task['command'], 'sync-occupancy')): ?>
                                        <button onclick="viewLog('bay_sync.log')" class="text-xs text-blue-600 hover:underline">
                                            📄 bay_sync.log
                                        </button>
                                    <?php elseif(str_contains($task['command'], 'cleanup')): ?>
                                        <button onclick="viewLog('booking_cleanup.log')" class="text-xs text-blue-600 hover:underline">
                                            📄 booking_cleanup.log
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <form action="<?php echo e(route('admin.scheduler.run-command')); ?>" method="POST" class="flex-shrink-0">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="command" value="<?php echo e($task['command']); ?>">
                                <button type="submit" class="px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 whitespace-nowrap">
                                    ▶️ Run Now
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">📖 Cron Expression Reference</h3>
            <div class="text-sm space-y-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="font-mono bg-gray-100 px-2 py-1 rounded inline">* * * * *</p>
                        <span class="text-gray-600 ml-2">= Every minute</span>
                    </div>
                    <div>
                        <p class="font-mono bg-gray-100 px-2 py-1 rounded inline">*/15 * * * *</p>
                        <span class="text-gray-600 ml-2">= Every 15 minutes</span>
                    </div>
                    <div>
                        <p class="font-mono bg-gray-100 px-2 py-1 rounded inline">*/30 * * * *</p>
                        <span class="text-gray-600 ml-2">= Every 30 minutes</span>
                    </div>
                    <div>
                        <p class="font-mono bg-gray-100 px-2 py-1 rounded inline">0 * * * *</p>
                        <span class="text-gray-600 ml-2">= Every hour</span>
                    </div>
                    <div>
                        <p class="font-mono bg-gray-100 px-2 py-1 rounded inline">0 0 * * *</p>
                        <span class="text-gray-600 ml-2">= Daily at midnight</span>
                    </div>
                    <div>
                        <p class="font-mono bg-gray-100 px-2 py-1 rounded inline">15 0 * * *</p>
                        <span class="text-gray-600 ml-2">= Daily at 00:15</span>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">📋 Recent Log Activity</h3>

            <div class="space-y-2">
                <?php $__currentLoopData = $recentLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $logFile => $logData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $taskName = match($logFile) {
                            'slots_generate.log' => 'Slot Generation',
                            'auto_release_slots.log' => 'Auto-Release Slots',
                            'bay_sync.log' => 'Bay Occupancy Sync',
                            'booking_cleanup.log' => 'Booking Cleanup',
                            default => $logFile
                        };
                    ?>
                    <div class="border border-gray-200 rounded p-2">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <h4 class="font-semibold text-sm"><?php echo e($taskName); ?></h4>
                                <code class="text-xs text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded"><?php echo e($logFile); ?></code>
                            </div>
                            <div class="flex gap-2 items-center">
                                <?php if($logData['exists']): ?>
                                    <span class="text-xs text-gray-500">
                                        <?php echo e($logData['last_modified']); ?>

                                    </span>
                                    <button onclick="viewLog('<?php echo e($logFile); ?>')" class="text-xs text-blue-600 hover:underline">
                                        View Full
                                    </button>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">No log yet</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($logData['exists']): ?>
                            <pre class="text-xs bg-gray-50 p-2 rounded overflow-x-auto max-h-24 mt-1"><?php echo e($logData['content']); ?></pre>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    
    <div id="logModal" onclick="closeLogModalIfBackdrop(event)" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl flex flex-col" style="width: 800px; height: 600px;" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center p-3 border-b flex-shrink-0">
                <h3 class="text-base font-bold" id="logModalTitle">Log Viewer</h3>
                <button onclick="closeLogModal()" class="text-gray-500 hover:text-gray-700 p-1 hover:bg-gray-100 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto overflow-x-auto p-3" style="min-height: 0;">
                <pre id="logModalContent" class="text-xs whitespace-pre font-mono"></pre>
            </div>
            <div class="flex-shrink-0 p-3 border-t bg-gray-50">
                <button onclick="closeLogModal()" class="px-4 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function checkStatus() {
            fetch('<?php echo e(route('admin.scheduler.status')); ?>')
                .then(response => response.json())
                .then(data => {
                    location.reload();
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                });
        }

        function viewLog(logFile) {
            const modal = document.getElementById('logModal');
            const title = document.getElementById('logModalTitle');
            const content = document.getElementById('logModalContent');

            title.textContent = 'Loading...';
            content.textContent = 'Loading log file...';
            modal.classList.remove('hidden');

            fetch('<?php echo e(route('admin.scheduler.logs')); ?>?log_file=' + logFile)
                .then(response => response.json())
                .then(data => {
                    title.textContent = data.file;
                    content.textContent = data.content || 'No content';
                })
                .catch(error => {
                    title.textContent = 'Error';
                    content.textContent = 'Failed to load log file: ' + error.message;
                });
        }

        function closeLogModal() {
            document.getElementById('logModal').classList.add('hidden');
        }

        function closeLogModalIfBackdrop(event) {
            if (event.target.id === 'logModal') {
                closeLogModal();
            }
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeLogModal();
            }
        });

        // Auto-refresh status every 30 seconds
        setInterval(checkStatus, 30000);
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/scheduler/index.blade.php ENDPATH**/ ?>