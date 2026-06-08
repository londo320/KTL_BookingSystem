<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold">⏰ Scheduler Management</h2>
            <div class="flex gap-2">
                <button onclick="checkStatus()" class="px-3 py-1.5 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
                    🔄 Refresh Status
                </button>
                <form action="{{ route('admin.scheduler.run') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        ▶️ Run All Tasks Now
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto space-y-6">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                <pre class="text-sm whitespace-pre-wrap">{{ session('success') }}</pre>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                <strong>Warning:</strong> {{ session('warning') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif

        {{-- Daemon Status --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">
                    @if($daemonStatus['running'])
                        🟢 Scheduler Daemon Status
                    @else
                        🔴 Scheduler Daemon Status
                    @endif
                </h3>
                @if(($daemonStatus['deployment_type'] ?? 'local') === 'local')
                    <div class="flex gap-2">
                        @if($daemonStatus['running'])
                            <form action="{{ route('admin.scheduler.stop') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to stop the scheduler daemon?')">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                    ⏹️ Stop
                                </button>
                            </form>
                            <form action="{{ route('admin.scheduler.restart') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                                    🔄 Restart
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.scheduler.start') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                    ▶️ Start Daemon
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <div id="daemon-status" class="flex items-center gap-3">
                <div class="w-3 h-3 rounded-full {{ $daemonStatus['running'] ? 'bg-green-500' : 'bg-red-500' }}"></div>
                <div>
                    <p class="font-semibold">{{ $daemonStatus['message'] }}</p>
                    @if($daemonStatus['running'])
                        @if(($daemonStatus['deployment_type'] ?? 'local') === 'docker')
                            <p class="text-sm text-gray-600">🐳 Running in Docker container - managed via Docker</p>
                            <p class="text-sm text-gray-500 mt-1">Container: <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $daemonStatus['container'] ?? 'scheduler' }}</code></p>
                        @else
                            <p class="text-sm text-gray-600">The scheduler daemon is checking for tasks every 60 seconds</p>
                        @endif
                    @else
                        @if(($daemonStatus['deployment_type'] ?? 'local') === 'docker')
                            <p class="text-sm text-red-600">🐳 Docker scheduler container not found or not running</p>
                            <p class="text-sm text-gray-500 mt-1">Check Docker container status: <code class="bg-gray-100 px-2 py-0.5 rounded">docker ps | grep scheduler</code></p>
                        @else
                            <p class="text-sm text-red-600">Click the "Start Daemon" button above to start the scheduler</p>
                        @endif
                    @endif
                </div>
            </div>

            @if(!$daemonStatus['running'])
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
                    <p class="text-sm text-yellow-800 font-semibold mb-2">⚠️ Scheduler Not Running</p>
                    <p class="text-sm text-gray-700 mb-2">The scheduler daemon is not running. Your scheduled tasks will not execute automatically.</p>
                    <div class="text-sm space-y-1">
                        <p><strong>Quick Start:</strong> Click the <span class="bg-green-600 text-white px-2 py-0.5 rounded text-xs">▶️ Start Daemon</span> button above</p>
                        <p><strong>Command Line:</strong> Run <code class="bg-gray-100 px-2 py-1 rounded">bash start-scheduler.sh</code></p>
                        <p><strong>Production:</strong> See <code class="bg-gray-100 px-2 py-1 rounded">SCHEDULER-SETUP.md</code> for systemd/Docker setup</p>
                    </div>
                </div>
            @endif
        </div>

        {{-- Scheduled Tasks --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">📅 Scheduled Tasks ({{ count($scheduledTasks) }} tasks)</h3>

            <div class="space-y-4">
                @foreach($scheduledTasks as $task)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $task['description'] }}</h4>
                                <code class="text-sm text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $task['command'] }}</code>
                            </div>
                            <form action="{{ route('admin.scheduler.run-command') }}" method="POST" class="ml-4">
                                @csrf
                                <input type="hidden" name="command" value="{{ $task['command'] }}">
                                <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded hover:bg-green-700 whitespace-nowrap">
                                    ▶️ Run Now
                                </button>
                            </form>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mt-3">
                            <div>
                                <span class="text-gray-500">Schedule:</span>
                                <p class="font-mono">{{ $task['expression'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Next Run:</span>
                                <p class="font-semibold text-blue-600">{{ $task['next_run'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Timezone:</span>
                                <p>{{ $task['timezone'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Overlapping:</span>
                                <p>{{ $task['without_overlapping'] ? '❌ Prevented' : '✅ Allowed' }}</p>
                            </div>
                        </div>

                        {{-- Log file buttons based on task --}}
                        <div class="mt-3 flex gap-2">
                            @if(str_contains($task['command'], 'generate'))
                                <button onclick="viewLog('slots_generate.log')" class="text-sm text-blue-600 hover:underline">
                                    📄 View Log
                                </button>
                            @elseif(str_contains($task['command'], 'auto-release'))
                                <button onclick="viewLog('auto_release_slots.log')" class="text-sm text-blue-600 hover:underline">
                                    📄 View Log
                                </button>
                            @elseif(str_contains($task['command'], 'sync-occupancy'))
                                <button onclick="viewLog('bay_sync.log')" class="text-sm text-blue-600 hover:underline">
                                    📄 View Log
                                </button>
                            @elseif(str_contains($task['command'], 'cleanup'))
                                <button onclick="viewLog('booking_cleanup.log')" class="text-sm text-blue-600 hover:underline">
                                    📄 View Log
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cron Expression Reference --}}
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

        {{-- Recent Logs --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-bold mb-4">📋 Recent Log Activity</h3>

            <div class="space-y-4">
                @foreach($recentLogs as $logFile => $logData)
                    <div class="border border-gray-200 rounded p-3">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="font-semibold">{{ $logFile }}</h4>
                            <div class="flex gap-2">
                                @if($logData['exists'])
                                    <span class="text-xs text-gray-500">
                                        Updated: {{ $logData['last_modified'] }}
                                    </span>
                                    <button onclick="viewLog('{{ $logFile }}')" class="text-sm text-blue-600 hover:underline">
                                        View Full Log
                                    </button>
                                @else
                                    <span class="text-xs text-red-500">Not found</span>
                                @endif
                            </div>
                        </div>
                        @if($logData['exists'])
                            <pre class="text-xs bg-gray-50 p-2 rounded overflow-x-auto max-h-32">{{ $logData['content'] }}</pre>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Log Viewer Modal --}}
    <div id="logModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-4xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-bold" id="logModalTitle">Log Viewer</h3>
                <button onclick="closeLogModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-auto p-4">
                <pre id="logModalContent" class="text-xs bg-gray-50 p-4 rounded overflow-x-auto"></pre>
            </div>
        </div>
    </div>

    <script>
        function checkStatus() {
            fetch('{{ route('admin.scheduler.status') }}')
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

            fetch('{{ route('admin.scheduler.logs') }}?log_file=' + logFile)
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

        // Auto-refresh status every 30 seconds
        setInterval(checkStatus, 30000);
    </script>
</x-app-layout>
