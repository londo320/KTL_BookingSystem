{{-- resources/views/warehouse/settings/factory-tipping-targets.blade.php --}}
<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏭 Factory Vehicle Tipping Time Targets
            </h2>
            <a href="{{ route('app.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto space-y-6">
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Factory Tipping Time Targets -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">🏭 Factory Vehicle Tipping Time Targets</h3>
                <p class="text-gray-600">Configure how long factory vehicles can stay on site before being considered overdue for tipping.</p>
                <div class="mt-2 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>📊 Priority Order:</strong> Customer-specific → Depot-specific → Default<br>
                        <strong>⏱️ Time Range:</strong> 1 minute to 1440 minutes (24 hours)<br>
                        <strong>🎯 Current Default:</strong> {{ $defaultTarget }} minutes ({{ floor($defaultTarget / 60) }}h {{ $defaultTarget % 60 }}m)
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('app.settings.factory-tipping-targets.store') }}">
                @csrf

                <!-- Default Target (Only for users with global settings permission) -->
                @if(auth()->user()->hasFunction('settings.manage.global'))
                <div class="mb-6 p-4 bg-gray-50 rounded">
                    <h4 class="font-medium text-gray-800 mb-2">🌐 Default Target (All Depots & Customers)</h4>
                    <div class="flex items-center space-x-2">
                        <input 
                            type="number" 
                            name="default_target" 
                            value="{{ old('default_target', $defaultTarget) }}" 
                            class="border rounded p-2 w-20"
                            min="1" 
                            max="1440"
                            required
                        >
                        <span class="text-gray-600">minutes</span>
                        <span class="text-gray-500 text-sm">({{ floor($defaultTarget / 60) }}h {{ $defaultTarget % 60 }}m)</span>
                    </div>
                    @error('default_target')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                @endif

                @if($depots->count() > 0)
                <!-- Depot-Specific Targets -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-800 mb-3">🏢 Depot-Specific Targets (Override Default)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($depots as $depot)
                            <div class="p-3 border rounded">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $depot->name }}</label>
                                <div class="flex items-center space-x-2">
                                    <input 
                                        type="number" 
                                        name="depot_targets[{{ $depot->id }}]" 
                                        value="{{ old("depot_targets.{$depot->id}", $depotTargets[$depot->id] ?? '') }}" 
                                        class="border rounded p-2 w-20"
                                        min="1" 
                                        max="1440"
                                        placeholder="{{ $defaultTarget }}"
                                    >
                                    <span class="text-gray-600">minutes</span>
                                    @if($depotTargets[$depot->id] ?? false)
                                        <span class="text-gray-500 text-sm">({{ floor($depotTargets[$depot->id] / 60) }}h {{ $depotTargets[$depot->id] % 60 }}m)</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Customer-Specific Targets -->
                @if($customers->count() > 0)
                <div class="mb-6">
                    <h4 class="font-medium text-gray-800 mb-3">👥 Customer-Specific Targets (Highest Priority)</h4>
                    
                    @foreach($depots as $depot)
                        <div class="mb-4 p-4 border rounded">
                            <h5 class="text-sm font-medium text-gray-800 mb-3">{{ $depot->name }} - Customer Overrides</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($customers as $customer)
                                    @php
                                        $customerTarget = $customerTargets[$depot->id][$customer->id]['target_minutes'] ?? '';
                                    @endphp
                                    <div class="p-2 bg-gray-50 rounded text-sm">
                                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ $customer->name }}</label>
                                        <div class="flex items-center space-x-1">
                                            <input 
                                                type="number" 
                                                name="customer_targets[{{ $depot->id }}][{{ $customer->id }}]" 
                                                value="{{ old("customer_targets.{$depot->id}.{$customer->id}", $customerTarget) }}" 
                                                class="border rounded p-1 w-16 text-xs"
                                                min="1" 
                                                max="1440"
                                                placeholder="{{ $depotTargets[$depot->id] ?? $defaultTarget }}"
                                            >
                                            <span class="text-gray-500 text-xs">min</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
                @endif

                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        <strong>💡 Tip:</strong> Leave fields blank to use the next priority level's value.<br>
                        <strong>📈 Impact:</strong> Factory vehicles exceeding these times will show as <span class="bg-red-100 px-1 rounded text-red-600 font-semibold">OVERDUE</span> in warehouse views.
                    </div>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center space-x-2">
                        <span>💾</span>
                        <span>Save Factory Tipping Targets</span>
                    </button>
                </div>
            </form>
        </div>

        @if($depots->count() === 0)
        <div class="bg-yellow-100 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        You don't have access to any depots. Contact your administrator to assign depot access.
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>
</x-warehouse-layout>