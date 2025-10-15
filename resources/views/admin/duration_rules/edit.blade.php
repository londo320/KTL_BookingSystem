@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Duration Rule</h1>
        <p class="text-gray-600 mt-1">Update booking duration based on case count</p>
    </div>

    <form action="{{ route('app.duration-rules.update', $durationRule) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            {{-- Booking Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type *</label>
                <select name="booking_type_id" required
                        class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">Select Booking Type</option>
                    @foreach($bookingTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('booking_type_id', $durationRule->booking_type_id) == $type->id)>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('booking_type_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Case Range --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Cases *</label>
                    <input type="number" name="min_cases" value="{{ old('min_cases', $durationRule->min_cases) }}" required
                           min="0" step="1"
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    <p class="text-xs text-gray-500 mt-1">Example: 0, 5001</p>
                    @error('min_cases')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Cases</label>
                    <input type="number" name="max_cases" value="{{ old('max_cases', $durationRule->max_cases) }}"
                           min="0" step="1"
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    <p class="text-xs text-gray-500 mt-1">Leave blank for no limit (∞)</p>
                    @error('max_cases')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Duration --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $durationRule->duration_minutes) }}" required
                       min="30" max="1440" step="15"
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">
                    Examples: 180 = 3 hours, 240 = 4 hours, 360 = 6 hours
                </p>
                @error('duration_minutes')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Depot (Optional) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depot (Optional)</label>
                <select name="depot_id" class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">All Depots</option>
                    @foreach($depots as $depot)
                        <option value="{{ $depot->id }}" @selected(old('depot_id', $durationRule->depot_id) == $depot->id)>
                            {{ $depot->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave blank to apply to all depots</p>
                @error('depot_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Customer (Optional) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer (Optional)</label>
                <select name="customer_id" class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $durationRule->customer_id) == $customer->id)>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave blank to apply to all customers</p>
                @error('customer_id')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Priority --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <input type="number" name="priority" value="{{ old('priority', $durationRule->priority) }}"
                       min="0" max="100" step="1"
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">
                    Higher priority rules are checked first. Use 100 for customer-specific rules, 50 for depot-specific, 0 for global.
                </p>
                @error('priority')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit Buttons --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('app.duration-rules.index') }}"
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
