@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Bay Capacity Rule</h1>
        <p class="text-gray-600 mt-1">Update maximum concurrent bookings for a booking type at a depot</p>
    </div>

    <form action="{{ route('app.bay-capacity-rules.update', $bayCapacityRule) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            {{-- Depot Selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depot *</label>
                <select name="depot_id" id="depot_id" required
                        class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">Select Depot</option>
                    @foreach($depots as $depot)
                        <option value="{{ $depot->id }}" @selected(old('depot_id', $bayCapacityRule->depot_id) == $depot->id)>
                            {{ $depot->name }}
                        </option>
                    @endforeach
                </select>
                @error('depot_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Booking Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type</label>
                <select name="booking_type_id" class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">All Booking Types</option>
                    @foreach($bookingTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('booking_type_id', $bayCapacityRule->booking_type_id) == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave blank to apply to all booking types</p>
                @error('booking_type_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Time Window --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                    <input type="time" name="time_start" value="{{ old('time_start', $bayCapacityRule->time_start) }}" required
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    @error('time_start')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                    <input type="time" name="time_end" value="{{ old('time_end', $bayCapacityRule->time_end) }}" required
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    @error('time_end')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Days of Week --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Days of Week (optional)</label>
                <div class="grid grid-cols-7 gap-2">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="days_of_week[]" value="{{ $day }}"
                                   @checked(in_array($day, old('days_of_week', $bayCapacityRule->days_of_week ?? [])))
                                   class="mr-1">
                            {{ substr($day, 0, 3) }}
                        </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">Leave unchecked to apply to all days</p>
            </div>

            {{-- Max Concurrent Bookings --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Concurrent Bookings *</label>
                <input type="number" name="max_concurrent_bookings" value="{{ old('max_concurrent_bookings', $bayCapacityRule->max_concurrent_bookings) }}"
                       min="1" max="100" required
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">Example: 3 = Maximum 3 bookings at the same time</p>
                @error('max_concurrent_bookings')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Capacity Weight --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity Weight</label>
                <input type="number" name="capacity_weight" value="{{ old('capacity_weight', $bayCapacityRule->capacity_weight) }}"
                       min="0.1" max="10" step="0.1"
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">1.0 = normal, 2.0 = uses double capacity (e.g., handball)</p>
                @error('capacity_weight')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active Status --}}
            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $bayCapacityRule->is_active))
                           class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Rule is active</span>
                </label>
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('app.bay-capacity-rules.index') }}"
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update Rule
            </button>
        </div>
    </form>
</div>
@endsection
