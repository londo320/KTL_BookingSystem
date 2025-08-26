@extends('layouts.admin')

@section('title', 'Edit Load ' . $load->load_reference)

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('outbound.loads.show', $load) }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Load {{ $load->load_reference }}</h1>
                    <p class="text-gray-600 mt-1">Update load information and assignments</p>
                </div>
            </div>
        </div>

        <form action="{{ route('outbound.loads.update', $load) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="load_reference" class="block text-sm font-medium text-gray-700 mb-2">
                                Load Reference
                            </label>
                            <input type="text" name="load_reference" id="load_reference" 
                                   value="{{ old('load_reference', $load->load_reference) }}"
                                   class="form-input w-full rounded-md bg-gray-100 @error('load_reference') border-red-500 @enderror"
                                   readonly>
                            @error('load_reference')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">Load reference cannot be changed</p>
                        </div>

                        <div>
                            <label for="load_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Load Name (Optional)
                            </label>
                            <input type="text" name="load_name" id="load_name" 
                                   value="{{ old('load_name', $load->load_name) }}"
                                   class="form-input w-full rounded-md @error('load_name') border-red-500 @enderror"
                                   placeholder="e.g., North Route - Monday">
                            @error('load_name')
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
                                  placeholder="Any special instructions or notes for this load">{{ old('notes', $load->notes) }}</textarea>
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
                                    <option value="{{ $vehicle->id }}" 
                                            {{ old('planned_vehicle_id', $load->planned_vehicle_id) == $vehicle->id ? 'selected' : '' }}>
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
                                    <option value="{{ $driver->id }}" 
                                            {{ old('assigned_driver_id', $load->assigned_driver_id) == $driver->id ? 'selected' : '' }}>
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
                    <h2 class="text-lg font-semibold text-gray-900">Load Status</h2>
                </div>
                <div class="p-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select name="status" id="status" 
                                class="form-select w-full rounded-md @error('status') border-red-500 @enderror">
                            <option value="planning" {{ old('status', $load->status) === 'planning' ? 'selected' : '' }}>
                                Planning
                            </option>
                            <option value="ready_for_collection" {{ old('status', $load->status) === 'ready_for_collection' ? 'selected' : '' }}>
                                Ready for Collection
                            </option>
                            <option value="collecting" {{ old('status', $load->status) === 'collecting' ? 'selected' : '' }}>
                                Collecting
                            </option>
                            <option value="in_transit" {{ old('status', $load->status) === 'in_transit' ? 'selected' : '' }}>
                                In Transit
                            </option>
                            <option value="delivering" {{ old('status', $load->status) === 'delivering' ? 'selected' : '' }}>
                                Delivering
                            </option>
                            <option value="completed" {{ old('status', $load->status) === 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>
                            <option value="cancelled" {{ old('status', $load->status) === 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            Current status: <strong>{{ ucfirst(str_replace('_', ' ', $load->status)) }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Route Optimization (if applicable) -->
            @if($load->optimized_distance_km || $load->estimated_duration_minutes)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Route Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="optimized_distance_km" class="block text-sm font-medium text-gray-700 mb-2">
                                Optimized Distance (km)
                            </label>
                            <input type="number" step="0.01" name="optimized_distance_km" id="optimized_distance_km" 
                                   value="{{ old('optimized_distance_km', $load->optimized_distance_km) }}"
                                   class="form-input w-full rounded-md @error('optimized_distance_km') border-red-500 @enderror">
                            @error('optimized_distance_km')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="estimated_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                Estimated Duration (minutes)
                            </label>
                            <input type="number" name="estimated_duration_minutes" id="estimated_duration_minutes" 
                                   value="{{ old('estimated_duration_minutes', $load->estimated_duration_minutes) }}"
                                   class="form-input w-full rounded-md @error('estimated_duration_minutes') border-red-500 @enderror">
                            @error('estimated_duration_minutes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="optimization_score" class="block text-sm font-medium text-gray-700 mb-2">
                                Optimization Score
                            </label>
                            <input type="number" step="0.01" name="optimization_score" id="optimization_score" 
                                   value="{{ old('optimization_score', $load->optimization_score) }}"
                                   class="form-input w-full rounded-md @error('optimization_score') border-red-500 @enderror">
                            @error('optimization_score')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Load Statistics (Read-only) -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Load Statistics</h2>
                    <p class="text-sm text-gray-600">These values are automatically calculated from orders and collections</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Orders</label>
                            <div class="text-2xl font-bold text-blue-600">{{ $load->total_orders }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Customers</label>
                            <div class="text-2xl font-bold text-green-600">{{ $load->total_customers }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Collection Points</label>
                            <div class="text-2xl font-bold text-orange-600">{{ $load->total_collection_points }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Delivery Points</label>
                            <div class="text-2xl font-bold text-purple-600">{{ $load->total_delivery_points }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Pallets</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $load->total_pallets }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Cases</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $load->total_cases }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Units</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $load->total_units }}</div>
                        </div>
                        @if($load->total_weight_kg)
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Total Weight</label>
                            <div class="text-lg font-semibold text-gray-900">{{ $load->total_weight_kg }}kg</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <div>
                    @if($load->status === 'planning')
                        <form method="POST" action="{{ route('outbound.loads.destroy', $load) }}" 
                              class="inline" onsubmit="return confirm('Are you sure you want to delete this load? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-medium">
                                Delete Load
                            </button>
                        </form>
                    @endif
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('outbound.loads.show', $load) }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                        Update Load
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection