<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">🏗️ Dropped Trailers On-Site</h2>
                <p class="text-sm text-gray-600 mt-1">Manage trailers currently dropped and awaiting tipping or departure</p>
            </div>
            <div class="text-sm">
                @if(!$currentDepotId)
                    <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
                    <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
                @else
                    @php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); @endphp
                    <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600">{{ $currentDepot?->name ?? 'Unknown Depot' }}</span></span>
                    @if($currentDepotId == $defaultDepotId)
                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
                    @else
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
                    @endif
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-6">
        @if (session('success'))
            <div class="mb-6 max-w-7xl mx-auto p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        {{-- Filters --}}
        <div class="max-w-7xl mx-auto mb-6 bg-white p-4 rounded-lg shadow">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                {{-- Depot Filter --}}
                <div class="min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">View</label>
                    <select name="depot_id" class="w-full border-gray-300 rounded-lg">
                        <option value="" {{ !$currentDepotId ? 'selected' : '' }}>All Depots (View Only)</option>
                        @foreach($allDepots as $depot)
                            <option value="{{ $depot->id }}" {{ $currentDepotId == $depot->id ? 'selected' : '' }}>
                                {{ $depot->name }} {{ $depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Status Filter --}}
                <div class="min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') == $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Filter Buttons --}}
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        🔍 Filter
                    </button>
                    <a href="{{ route('app.dropped-trailers.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Clear
                    </a>
                </div>
            </form>
        </div>
        {{-- Summary Statistics --}}
        <div class="max-w-7xl mx-auto mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="text-2xl font-bold text-blue-600">{{ $droppedTrailers->total() }}</div>
                <div class="text-sm text-blue-800">Total Trailers On-Site</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                <div class="text-2xl font-bold text-orange-600">{{ $droppedTrailers->where('tipping_status', 'trailer_dropped')->count() }}</div>
                <div class="text-sm text-orange-800">Awaiting Bay Assignment</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="text-2xl font-bold text-green-600">{{ $droppedTrailers->where('tipping_status', 'tipping_in_progress')->count() }}</div>
                <div class="text-sm text-green-800">Currently Tipping</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="text-2xl font-bold text-purple-600">{{ $droppedTrailers->where('tipping_status', 'tipping_completed')->count() }}</div>
                <div class="text-sm text-purple-800">Ready for Departure</div>
            </div>
        </div>
        {{-- Trailers List --}}
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow overflow-hidden">
            @if($droppedTrailers->isEmpty())
                <div class="p-12 text-center">
                    <div class="text-4xl mb-4">🚛</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers On-Site</h3>
                    <p class="text-gray-500">No trailers are currently dropped and awaiting processing.</p>
                </div>
            @else
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
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dropped
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($droppedTrailers as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="{{ route('app.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900">
                                                        #{{ $booking->id }}
                                                    </a>
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $booking->slot->depot->name }}</div>
                                                @if($booking->vehicle_registration)
                                                    <div class="text-xs text-gray-500 font-mono">{{ $booking->vehicle_registration }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $booking->customer->name ?? 'No Customer' }}</div>
                                        @if($booking->container_number)
                                            <div class="text-xs text-gray-500 font-mono">{{ $booking->container_number }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($booking->tippingBay)
                                            <div class="font-medium">{{ $booking->tippingBay->name }}</div>
                                            <div class="text-xs">Bay</div>
                                        @elseif($booking->tippingLocation)
                                            <div class="font-medium">{{ $booking->tippingLocation->name }}</div>
                                            <div class="text-xs">Drop Location</div>
                                        @else
                                            <span class="text-gray-400">Not assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {!! $booking->tipping_status_badge !!}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($booking->trailer_dropped_at)
                                            {{ $booking->trailer_dropped_at->format('M j, H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($booking->trailer_dropped_at)
                                            {{ $booking->trailer_dropped_at->diffForHumans(null, true) }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('app.bookings.show', $booking) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                        @if(in_array($booking->tipping_status, ['tipping_completed']))
                                            @php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; @endphp
                                            @if($canTakeAction)
                                                <a href="{{ route('app.dropped-trailers.reconnect.form', $booking) }}" 
                                                   class="text-green-600 hover:text-green-900">
                                                    🔗 Reconnect
                                                </a>
                                            @else
                                                <span class="text-gray-400 cursor-not-allowed" 
                                                      title="Actions only available for your default depot">
                                                    🔗 Reconnect
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Pagination --}}
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $droppedTrailers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>