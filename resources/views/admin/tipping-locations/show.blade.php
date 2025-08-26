<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Tipping Location Details</h2>
                <div class="text-sm text-gray-600 mt-1">
                    <p>{{ $tippingLocation->name }} - {{ $tippingLocation->depot->name }}</p>
                    @php $canTakeAction = $tippingLocation->depot_id == $defaultDepotId; @endphp
                    <div class="mt-1">
                        @if($canTakeAction)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
                        @else
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only - Actions Restricted</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('app.tipping-locations.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Locations
                </a>
                @if($canTakeAction)
                    <a href="{{ route('app.tipping-locations.edit', $tippingLocation) }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Edit Location
                    </a>
                @else
                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed"
                          title="Actions only available for your default depot">
                        Edit Location
                    </span>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Location Information --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">📍 Location Information</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Name</label>
                        <p class="text-lg font-medium">{{ $tippingLocation->name }}</p>
                    </div>
                    @if($tippingLocation->code)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Code</label>
                            <p class="text-lg font-mono">{{ $tippingLocation->code }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="text-sm font-medium text-gray-600">Depot</label>
                        <p class="text-lg">{{ $tippingLocation->depot->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Capacity</label>
                        <p class="text-lg">{{ $tippingLocation->capacity }} vehicles</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <p class="text-lg">
                            @if($tippingLocation->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded">Inactive</span>
                            @endif
                        </p>
                    </div>
                    @if($tippingLocation->description)
                        <div>
                            <label class="text-sm font-medium text-gray-600">Description</label>
                            <p class="text-lg">{{ $tippingLocation->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Current Status --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">📊 Current Status</h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Occupancy</label>
                        <div class="mt-1 flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-4">
                                @php 
                                    $occupancyPercent = $tippingLocation->capacity > 0 ? ($currentOccupancy / $tippingLocation->capacity) * 100 : 0;
                                @endphp
                                <div class="bg-blue-500 h-4 rounded-full" style="width: {{ $occupancyPercent }}%"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                {{ $currentOccupancy }}/{{ $tippingLocation->capacity }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Available Capacity</label>
                        <p class="text-2xl font-bold {{ $availableCapacity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $availableCapacity }}
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Availability</label>
                        <p class="text-lg">
                            @if($tippingLocation->isAvailable())
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded">Available</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded">Full</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        {{-- Active Bookings --}}
        <div class="mt-6 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Active Bookings</h3>
                <p class="text-sm text-gray-600 mt-1">Vehicles currently using this drop location</p>
            </div>
            <div class="p-6">
                @if($tippingLocation->activeBookings && $tippingLocation->activeBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($tippingLocation->activeBookings as $booking)
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-800">{{ $booking->customer->name }}</h4>
                                        <span class="text-gray-600">-</span>
                                        <span class="text-gray-600">{{ $booking->booking_reference ?: '#' . $booking->id }}</span>
                                    </div>
                                    @if($booking->reference)
                                        <p class="text-sm text-gray-500 mt-1">Customer Ref: {{ $booking->reference }}</p>
                                    @endif
                                    @if($booking->container_number)
                                        <p class="text-sm text-gray-500 mt-1">Container: {{ $booking->container_number }}</p>
                                    @endif
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span>Slot: {{ $booking->slot->start_at->format('D, d M Y - H:i') }}</span>
                                        @if($booking->arrived_at)
                                            <span class="ml-4">Arrived: {{ $booking->arrived_at->format('H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    {!! $booking->tipping_status_badge !!}
                                    @php $canManageBooking = $booking->slot->depot_id == $defaultDepotId; @endphp
                                    @if($canManageBooking)
                                        <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            Manage →
                                        </a>
                                    @else
                                        <span class="text-gray-400 cursor-not-allowed text-sm"
                                              title="Actions only available for your default depot">
                                            Manage →
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-6xl mb-4">🚛</div>
                        <h4 class="text-lg font-medium mb-2">No Active Bookings</h4>
                        <p class="text-sm">This location is currently empty and available for new arrivals.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>