<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Create Tipping Location</h2>
                <p class="text-sm text-gray-600 mt-1">Add a new trailer drop location</p>
            </div>
            <a href="{{ route('app.tipping-locations.index') }}" 
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                ← Back to Locations
            </a>
        </div>
    </x-slot>
    <div class="py-6 max-w-4xl mx-auto">
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
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">📍 New Drop Location</h3>
                <p class="text-sm text-gray-600 mt-1">Configure a location where trailers can be dropped before moving to tipping bays</p>
            </div>
            <form method="POST" action="{{ route('app.tipping-locations.store') }}" class="p-6">
                @csrf
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="depot_id">
                                Depot <span class="text-red-500">*</span>
                            </label>
                            <select name="depot_id" id="depot_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select depot...</option>
                                @foreach($depots as $depot)
                                    <option value="{{ $depot->id }}" {{ old('depot_id') == $depot->id ? 'selected' : '' }}>
                                        {{ $depot->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                                Location Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., Parking Area A, Trailer Park 1" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="code">
                                Location Code
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., DZ-A, TP-1">
                            <p class="text-xs text-gray-500 mt-1">Short code for easy identification (optional)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="location_type">
                                Location Type <span class="text-red-500">*</span>
                            </label>
                            <select name="location_type" id="location_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="parking" {{ old('location_type', 'parking') == 'parking' ? 'selected' : '' }}>Parking Area</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">parking areas for incoming trailers, parking areas for awaiting pickup</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="capacity">
                                Capacity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 5) }}" min="1" max="50"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Maximum number of trailers that can be dropped here</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="description">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Additional details about this location...">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 block text-sm text-gray-700" for="is_active">
                                Active (available for use)
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Inactive locations won't be available for trailer drops</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('app.tipping-locations.index') }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create Location
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>