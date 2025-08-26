<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Customer Behavior Settings</h2>
                <p class="text-sm text-gray-600 mt-1">Customize behavior limits for {{ $customer->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('app.customers.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Customers
                </a>
                <a href="{{ route('app.customer-behavior.show', $customer) }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View Behavior Analytics
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-4xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Please fix the following errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Customer Information --}}
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Customer Name</p>
                    <p class="font-medium">{{ $customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Contact Emails</p>
                    <p class="font-medium">
                        @if(!empty($customer->emails))
                            {{ implode(', ', $customer->emails) }}
                        @else
                            No emails on file
                        @endif
                    </p>
                </div>
            </div>
        </div>
        {{-- Settings Form --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Behavior Limit Settings</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Configure custom limits for this customer. Values that match defaults will not be stored.
                </p>
            </div>
            <form method="POST" action="{{ route('app.customer-behavior.update-settings', $customer) }}" class="p-6">
                @csrf
                @method('PUT')
                <div class="space-y-8">
                    {{-- Booking Limits --}}
                    <div class="border-b border-gray-200 pb-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">📋 Booking Limits</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(['max_rebooks_per_booking', 'max_total_rebooks_30days', 'max_cancellations_30days'] as $key)
                                @php $config = $availableSettings[$key] @endphp
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $config['label'] }}
                                        <span class="text-xs text-gray-500">(Default: {{ $config['default'] }})</span>
                                    </label>
                                    <input type="number" 
                                           name="{{ $key }}" 
                                           value="{{ $currentSettings[$key] }}"
                                           min="{{ $config['min'] ?? 0 }}" 
                                           max="{{ $config['max'] ?? 999 }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">{{ $config['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Time-Based Limits --}}
                    <div class="border-b border-gray-200 pb-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">⏰ Time-Based Limits</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(['max_last_minute_rebooks_30days', 'minimum_hours_notice'] as $key)
                                @php $config = $availableSettings[$key] @endphp
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $config['label'] }}
                                        <span class="text-xs text-gray-500">(Default: {{ $config['default'] }})</span>
                                    </label>
                                    <input type="number" 
                                           name="{{ $key }}" 
                                           value="{{ $currentSettings[$key] }}"
                                           min="{{ $config['min'] ?? 0 }}" 
                                           max="{{ $config['max'] ?? 999 }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">{{ $config['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- Special Permissions --}}
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">🔒 Special Permissions</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach(['allow_weekend_bookings', 'allow_holiday_bookings', 'priority_booking', 'auto_approve_bookings'] as $key)
                                @php $config = $availableSettings[$key] @endphp
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="hidden" name="{{ $key }}" value="0">
                                        <input type="checkbox" 
                                               name="{{ $key }}" 
                                               value="1"
                                               {{ $currentSettings[$key] ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3">
                                        <label class="text-sm font-medium text-gray-700">
                                            {{ $config['label'] }}
                                            <span class="text-xs text-gray-500">(Default: {{ $config['default'] ? 'Yes' : 'No' }})</span>
                                        </label>
                                        <p class="text-xs text-gray-500">{{ $config['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                    <button type="button" 
                            onclick="if(confirm('Reset all settings to default values?')) { window.location.href='{{ route('app.customer-behavior.reset-settings', $customer) }}'; }"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        🔄 Reset to Defaults
                    </button>
                    <div class="flex space-x-3">
                        <a href="{{ route('app.customers.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            💾 Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
        {{-- Current Custom Settings Summary --}}
        @if($customer->behaviorSettings->isNotEmpty())
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h4 class="text-lg font-medium text-yellow-800 mb-4">Current Custom Settings</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($customer->behaviorSettings as $setting)
                        <div class="bg-white p-3 rounded border">
                            <p class="font-medium text-sm">{{ $availableSettings[$setting->setting_key]['label'] ?? $setting->setting_key }}</p>
                            <p class="text-lg font-bold text-yellow-700">{{ $setting->getCastedValue() }}</p>
                            <p class="text-xs text-gray-500">
                                Updated {{ $setting->updated_at->format('M j, Y H:i') }}
                                @if($setting->updatedBy)
                                    by {{ $setting->updatedBy->name }}
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-warehouse-layout>