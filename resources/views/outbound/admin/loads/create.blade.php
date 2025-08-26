@extends('layouts.admin')

@section('title', 'Create Outbound Load')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('outbound.loads.index') }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Outbound Load</h1>
                    <p class="text-gray-600 mt-1">Create a new delivery load</p>
                </div>
            </div>
        </div>

        <form action="{{ route('outbound.loads.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="load_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Load Name (Optional)
                            </label>
                            <input type="text" name="load_name" id="load_name" 
                                   value="{{ old('load_name') }}"
                                   class="form-input w-full rounded-md @error('load_name') border-red-500 @enderror"
                                   placeholder="e.g., North Route - Monday">
                            @error('load_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="created_from" class="block text-sm font-medium text-gray-700 mb-2">
                                Source
                            </label>
                            <select name="created_from" id="created_from" 
                                    class="form-select w-full rounded-md @error('created_from') border-red-500 @enderror">
                                <option value="manual" {{ old('created_from', 'manual') === 'manual' ? 'selected' : '' }}>
                                    Manual Creation
                                </option>
                                <option value="booking_completion" {{ old('created_from') === 'booking_completion' ? 'selected' : '' }}>
                                    From Booking Completion
                                </option>
                                <option value="factory_completion" {{ old('created_from') === 'factory_completion' ? 'selected' : '' }}>
                                    From Factory Completion
                                </option>
                            </select>
                            @error('created_from')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-textarea w-full rounded-md @error('notes') border-red-500 @enderror"
                                  placeholder="Any special instructions or notes for this load">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Vehicle Assignment -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Vehicle Assignment</h2>
                    <p class="text-sm text-gray-600">Assign vehicle and driver (optional, can be done later)</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="planned_vehicle_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle
                            </label>
                            <select name="planned_vehicle_id" id="planned_vehicle_id" 
                                    class="form-select w-full rounded-md @error('planned_vehicle_id') border-red-500 @enderror">
                                <option value="">Select Vehicle (Optional)</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('planned_vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->registration }} - {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                            @error('planned_vehicle_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="assigned_driver_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Driver
                            </label>
                            <select name="assigned_driver_id" id="assigned_driver_id" 
                                    class="form-select w-full rounded-md @error('assigned_driver_id') border-red-500 @enderror">
                                <option value="">Select Driver (Optional)</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('assigned_driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_driver_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Load Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Initial Status</h2>
                </div>
                <div class="p-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select name="status" id="status" 
                                class="form-select w-full rounded-md @error('status') border-red-500 @enderror">
                            <option value="planning" {{ old('status', 'planning') === 'planning' ? 'selected' : '' }}>
                                Planning
                            </option>
                            <option value="ready_for_collection" {{ old('status') === 'ready_for_collection' ? 'selected' : '' }}>
                                Ready for Collection
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            Most loads start in "Planning" status. You can add orders and collections after creation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('outbound.loads.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Create Load
                </button>
            </div>
        </form>
    </div>
</div>
@endsection