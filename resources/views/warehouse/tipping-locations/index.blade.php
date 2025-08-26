<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Tipping Locations Management</h2>
                <div class="text-sm text-gray-600 mt-1">
                    <p>Manage trailer drop locations for each depot</p>
                    @if(!$currentDepotId)
                        <div class="mt-1">
                            <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
                            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
                        </div>
                    @else
                        @php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); @endphp
                        <div class="mt-1">
                            <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600">{{ $currentDepot?->name ?? 'Unknown Depot' }}</span></span>
                            @if($currentDepotId == $defaultDepotId)
                                <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
                            @else
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex items-center space-x-2">
                    <label for="depot_id" class="text-sm font-medium text-gray-700">View:</label>
                    <select name="depot_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All Depots (View Only)</option>
                        @foreach($allDepots as $depot)
                            <option value="{{ $depot->id }}" {{ $currentDepotId == $depot->id ? 'selected' : '' }}>
                                {{ $depot->name }} {{ $depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)' }}
                            </option>
                        @endforeach
                    </select>
                </form>
                @php $canTakeAction = !$currentDepotId || $currentDepotId == $defaultDepotId; @endphp
                @if($canTakeAction)
                    <a href="{{ route('app.tipping-locations.create') }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        + New Location
                    </a>
                @else
                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed"
                          title="Actions only available for your default depot">
                        + New Location
                    </span>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📍 Drop Locations ({{ $locations->total() }})</h3>
            </div>
            @if($locations->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-4">📍</div>
                    <p class="text-lg mb-2">No tipping locations found</p>
                    <p class="text-sm mb-4">Create drop locations where trailers can wait before being moved to tipping bays.</p>
                    <a href="{{ route('app.tipping-locations.create') }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create First Location
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Depot
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Capacity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Occupancy
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($locations as $location)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $location->name }}</div>
                                            @if($location->code)
                                                <div class="text-sm text-gray-500">Code: {{ $location->code }}</div>
                                            @endif
                                            @if($location->description)
                                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($location->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $location->depot->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $typeColors = [
                                                'drop_zone' => 'bg-blue-100 text-blue-800',
                                                'collection_zone' => 'bg-green-100 text-green-800',
                                                'general' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $typeLabels = [
                                                'drop_zone' => 'Drop Zone',
                                                'collection_zone' => 'Collection Zone',
                                                'general' => 'General'
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$location->location_type] ?? $typeColors['general'] }}">
                                            {{ $typeLabels[$location->location_type] ?? ucfirst(str_replace('_', ' ', $location->location_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="text-lg font-semibold">{{ $location->capacity }}</div>
                                            <div class="ml-2 text-xs text-gray-500">trailers</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php 
                                            $occupancy = $location->getCurrentOccupancy();
                                            $available = $location->getAvailableCapacity();
                                            $percentage = $location->capacity > 0 ? ($occupancy / $location->capacity) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span>{{ $occupancy }}/{{ $location->capacity }}</span>
                                                    <span>{{ $available }} available</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $percentage > 80 ? 'bg-red-500' : ($percentage > 60 ? 'bg-yellow-500' : 'bg-green-500') }}" 
                                                         style="width: {{ $percentage }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($location->is_active)
                                            @if($location->isAvailable())
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                            @else
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Full</span>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('app.tipping-locations.show', $location) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        @php $canTakeAction = $location->depot_id == $defaultDepotId; @endphp
                                        @if($canTakeAction)
                                            <a href="{{ route('app.tipping-locations.edit', $location) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                            @if(!$location->is_active)
                                                <!-- Reactivate button for inactive locations -->
                                                <form method="POST" action="{{ route('app.tipping-locations.toggle-active', $location) }}" 
                                                      class="inline-block" onsubmit="return confirm('Reactivate this location? It will become available for new bookings.');">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Reactivate</button>
                                                </form>
                                            @elseif($location->activeBookings()->count() === 0)
                                                <!-- Delete/Deactivate button for active locations with no bookings -->
                                                <form method="POST" action="{{ route('app.tipping-locations.destroy', $location) }}" 
                                                      class="inline-block" onsubmit="return confirm('Are you sure you want to delete this location?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400" title="Cannot delete - has active bookings">Delete</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400 cursor-not-allowed" 
                                                  title="Actions only available for your default depot">Edit</span>
                                            @if(!$location->is_active)
                                                <span class="text-gray-400 cursor-not-allowed" 
                                                      title="Actions only available for your default depot">Reactivate</span>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed" 
                                                      title="Actions only available for your default depot">Delete</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $locations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-warehouse-layout>