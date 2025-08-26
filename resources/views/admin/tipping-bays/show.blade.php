<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">{{ $tippingBay->name }}</h2>
                <div class="text-sm text-gray-600 mt-1">
                    <p>{{ $tippingBay->depot->name }} - Bay Details</p>
                    @php $canTakeAction = $tippingBay->depot_id == $defaultDepotId; @endphp
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
                @if($canTakeAction)
                    <a href="{{ route('app.tipping-bays.edit', $tippingBay) }}" 
                       class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                        Edit Bay
                    </a>
                @else
                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed"
                          title="Actions only available for your default depot">
                        Edit Bay
                    </span>
                @endif
                <a href="{{ route('app.tipping-bays.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Bays
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        {{-- Bay Information --}}
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">🚛 Bay Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Bay Name</p>
                    <p class="font-medium">{{ $tippingBay->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Code</p>
                    <p class="font-medium">{{ $tippingBay->code ?: 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Depot</p>
                    <p class="font-medium">{{ $tippingBay->depot->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <div>{!! $tippingBay->status_badge !!}</div>
                </div>
            </div>
            @if($tippingBay->description)
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Description</p>
                    <p class="text-gray-800">{{ $tippingBay->description }}</p>
                </div>
            @endif
        </div>
        {{-- Equipment --}}
        @if(!empty($tippingBay->equipment))
            <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">⚙️ Available Equipment</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-2">
                        @foreach($tippingBay->equipment as $equipment)
                            <span class="px-3 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm">{{ $equipment }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        {{-- Current Booking --}}
        @if($currentBooking)
            <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">📋 Current Booking</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <p class="text-sm text-gray-600">Customer</p>
                            <p class="font-medium">{{ $currentBooking->customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Booking ID</p>
                            <p class="font-medium">{{ $currentBooking->booking_reference ?? '#' . $currentBooking->id }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <div>{!! $currentBooking->tipping_status_badge !!}</div>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('app.bookings.show', $currentBooking) }}" 
                           class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            View Booking Details
                        </a>
                        <a href="{{ route('app.tipping-workflow.show', $currentBooking) }}" 
                           class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                            Manage Tipping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">📋 Current Status</h3>
                </div>
                <div class="p-6 text-center text-gray-500">
                    <div class="text-4xl mb-4">🚛</div>
                    <p class="text-lg">No current booking</p>
                    <p class="text-sm">This bay is available for use</p>
                </div>
            </div>
        @endif
        {{-- Recent Bookings --}}
        @if($tippingBay->bookings->isNotEmpty())
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">📜 Recent Bookings</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Booking
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Moved to Bay
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tippingBay->bookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('app.bookings.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            #{{ $booking->id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->customer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking->moved_to_bay_at ? $booking->moved_to_bay_at->format('M j, H:i') : '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($booking->actual_tipping_duration)
                                            {{ $booking->actual_tipping_duration }} min
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $booking->tipping_status_badge !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>