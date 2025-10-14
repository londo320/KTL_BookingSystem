@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Time Window Configuration</h1>
                <p class="text-gray-600 mt-1">Configure allowed booking times for <strong>{{ $customer->name }}</strong></p>
            </div>
            <a href="{{ route('app.customers.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ← Back to Customers
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Info Box --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Time Windows Work</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Time Windows:</strong> Restrict when this customer can make bookings at each depot</li>
            <li><strong>Example:</strong> Only allow bookings between 08:00-16:00 on weekdays</li>
            <li><strong>Leave blank:</strong> No time restrictions (customer can book anytime)</li>
            <li><strong>Days of Week:</strong> Optionally restrict to specific days</li>
        </ul>
    </div>

    <form action="{{ route('app.customers.time-windows.update', $customer) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Depot-Specific Time Windows --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">🕐 Time Windows by Depot</h2>

            <div class="space-y-4">
                @foreach($depots as $depot)
                    @php
                        $window = $timeWindows->get($depot->id);
                    @endphp
                    <div class="border rounded-lg p-4 hover:border-blue-300 transition">
                        <h3 class="font-semibold text-gray-800 mb-3">{{ $depot->name }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Start Time --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Start Time</label>
                                <input type="time"
                                       name="depots[{{ $depot->id }}][allowed_start_time]"
                                       value="{{ $window?->allowed_start_time ?? '' }}"
                                       class="block w-full border-gray-300 rounded text-sm py-1">
                            </div>

                            {{-- End Time --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">End Time</label>
                                <input type="time"
                                       name="depots[{{ $depot->id }}][allowed_end_time]"
                                       value="{{ $window?->allowed_end_time ?? '' }}"
                                       class="block w-full border-gray-300 rounded text-sm py-1">
                            </div>

                            {{-- Active Status --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <select name="depots[{{ $depot->id }}][is_active]"
                                        class="block w-full border-gray-300 rounded text-sm py-1">
                                    <option value="1" @selected(($window?->is_active ?? true) === true)>✅ Active</option>
                                    <option value="0" @selected(($window?->is_active ?? true) === false)>❌ Inactive</option>
                                </select>
                            </div>
                        </div>

                        {{-- Days of Week --}}
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-2">Allowed Days (leave unchecked for all days)</label>
                            <div class="grid grid-cols-7 gap-2">
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox"
                                               name="depots[{{ $depot->id }}][days_of_week][]"
                                               value="{{ $day }}"
                                               @checked(in_array($day, $window?->days_of_week ?? []))
                                               class="mr-1">
                                        {{ substr($day, 0, 3) }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('app.customers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                💾 Save Time Windows
            </button>
        </div>
    </form>
</div>
@endsection
