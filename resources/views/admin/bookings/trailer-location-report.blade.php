<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">📍 Trailer Location Report</h2>
      <div class="flex items-center space-x-4">
        {{-- Depot Filter --}}
        <form method="GET" class="flex items-center space-x-2">
          <label for="depot_id" class="text-sm font-medium text-gray-700">View:</label>
          <select name="depot_id" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded-md text-sm">
            <option value="" {{ !$currentDepotId ? 'selected' : '' }}>All Depots (View Only)</option>
            @foreach($allDepots as $depot)
              <option value="{{ $depot->id }}" {{ $currentDepotId == $depot->id ? 'selected' : '' }}>
                {{ $depot->name }} {{ $depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)' }}
              </option>
            @endforeach
          </select>
        </form>
        {{-- Action Buttons --}}
        <div class="flex space-x-3">
          <a href="{{ route('app.empty-unit-collection') }}"
             class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            🚛 Empty Collection
          </a>
          <a href="{{ route('app.bookings.index') }}"
             class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
            ← Back to Bookings
          </a>
        </div>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-7xl mx-auto">
    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_on_site'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Total On Site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-orange-600">{{ $stats['awaiting_collection'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Awaiting Collection</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-green-600">{{ $stats['empty_available'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Empty Available</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-purple-600">{{ $stats['being_tipped'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Being Tipped</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-indigo-600">{{ $stats['in_waiting_areas'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">In Parking Areas</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-red-600">{{ $stats['overdue_collections'] ?? 0 }}</div>
        <div class="text-sm text-gray-600">Overdue Collections</div>
      </div>
    </div>
    <!-- Trailers Waiting to Start Tipping -->
    @if(isset($waitingToTip) && $waitingToTip->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">⏳ Waiting to Start Tipping</h3>
        <p class="text-sm text-gray-600 mt-1">Trailers that have arrived and need to begin tipping process</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              @if(!$currentDepotId)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
              @endif
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waiting Time</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($waitingToTip as $movement)
            @php $booking = $movement->booking @endphp
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <a href="{{ route('app.bookings.show', $booking) }}" class="hover:underline">
                  {{ $booking->booking_reference }}
                </a>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $booking->customer->name ?? 'Unknown' }}
              </td>
              @if(!$currentDepotId)
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ $booking->slot->depot->name ?? 'Unknown' }}
                </td>
              @endif
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->vehicle_registration ?? 'Not specified' }}
                @if($booking->container_number)
                  <br><span class="text-xs text-gray-500">{{ $booking->container_number }}</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->tippingLocation)
                  <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                    {{ $movement->tippingLocation->name }}
                  </span>
                @else
                  <span class="text-gray-400">Not assigned</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @php
                  $statusDisplay = match($movement->current_status) {
                    'trailer_dropped' => '🔄 Trailer Detached',
                    'in_location' => '🚛 Parked - Waiting',
                    'arrived' => '🚐 Just Arrived',
                    'in_waiting' => '⏳ In Parking Area',
                    default => '⏳ ' . ucwords(str_replace('_', ' ', $movement->current_status))
                  };
                  $statusClass = match($movement->current_status) {
                    'trailer_dropped' => 'bg-red-100 text-red-800',
                    'in_location' => 'bg-blue-100 text-blue-800',
                    'arrived' => 'bg-green-100 text-green-800',
                    'in_waiting' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-yellow-100 text-yellow-800'
                  };
                @endphp
                <span class="px-2 py-1 text-xs rounded-full {{ $statusClass }}">
                  {{ $statusDisplay }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->current_status === 'trailer_dropped' && $movement->trailer_dropped_at)
                  {{ $movement->trailer_dropped_at->diffForHumans() }}
                @elseif($movement->current_status === 'in_location' && $movement->moved_to_location_at)
                  {{ $movement->moved_to_location_at->diffForHumans() }}
                @elseif($movement->actual_arrival)
                  {{ $movement->actual_arrival->diffForHumans() }}
                @else
                  <span class="text-gray-400">Unknown</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
    <!-- Currently Being Tipped -->
    @if(isset($currentlyTipping) && $currentlyTipping->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">🏗️ Currently Being Tipped</h3>
        <p class="text-sm text-gray-600 mt-1">Trailers actively being unloaded in tipping bays</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              @if(!$currentDepotId)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
              @endif
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipping Bay</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time in Bay</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($currentlyTipping as $movement)
            @php $booking = $movement->booking @endphp
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <a href="{{ route('app.bookings.show', $booking) }}" class="hover:underline">
                  {{ $booking->booking_reference }}
                </a>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $booking->customer->name ?? 'Unknown' }}
              </td>
              @if(!$currentDepotId)
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  {{ $booking->slot->depot->name ?? 'Unknown' }}
                </td>
              @endif
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->vehicle_registration ?? 'Not specified' }}
                @if($booking->container_number)
                  <br><span class="text-xs text-gray-500">{{ $booking->container_number }}</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->tippingBay)
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    {{ $movement->tippingBay->name }}
                  </span>
                @else
                  <span class="text-gray-400">Not assigned</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full 
                  @if($movement->current_status === 'unloading') bg-orange-100 text-orange-800 
                  @else bg-blue-100 text-blue-800 @endif">
                  @if($movement->current_status === 'unloading') ⚡ Unloading
                  @else 🚛 At Bay @endif
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->moved_to_bay_at)
                  {{ $movement->moved_to_bay_at->diffForHumans() }}
                @elseif($movement->unloading_started_at)
                  {{ $movement->unloading_started_at->diffForHumans() }}
                @elseif($movement->actual_arrival)
                  {{ $movement->actual_arrival->diffForHumans() }}
                @else
                  <span class="text-gray-400">Unknown</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
    <!-- Empty Trailers Ready for Collection -->
    @if(isset($emptyTrailers) && $emptyTrailers->count() > 0)
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">✅ Empty Trailers Ready for Collection</h3>
        <p class="text-sm text-gray-600 mt-1">Trailers that have been tipped and are ready to be collected</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              @if(!$currentDepotId)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
              @endif
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container/Trailer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Collection</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($emptyTrailers as $movement)
            @php $booking = $movement->booking @endphp
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <a href="{{ route('app.bookings.show', $booking) }}" class="hover:underline">
                  {{ $booking->booking_reference }}
                </a>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $booking->customer->name ?? 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->container_number ?? 'Not specified' }}
                @if($booking->trailerType)
                  <br><span class="text-xs text-gray-500">{{ $booking->trailerType->name }}</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->tippingBay)
                  <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    {{ $movement->tippingBay->name }}
                  </span>
                @elseif($movement->tippingLocation)
                  <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    {{ $movement->tippingLocation->name }}
                  </span>
                @else
                  <span class="text-gray-400">Unknown</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->unloading_completed_at)
                  {{ $movement->unloading_completed_at->format('d-M H:i') }}
                  <br><span class="text-xs text-gray-400">{{ $movement->unloading_completed_at->diffForHumans() }}</span>
                @else
                  <span class="text-gray-400">Unknown</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($booking->trailer_collection_scheduled)
                  @if($booking->trailer_collection_scheduled->isPast())
                    <span class="text-red-600 font-medium">
                      {{ $booking->trailer_collection_scheduled->format('d-M-Y H:i') }}
                      <br><span class="text-xs">⚠️ OVERDUE</span>
                    </span>
                  @else
                    <span class="text-gray-600">
                      {{ $booking->trailer_collection_scheduled->format('d-M-Y H:i') }}
                    </span>
                  @endif
                @else
                  <span class="text-gray-400">Not scheduled</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                @if($movement->tippingBay)
                  {{-- If trailer is in bay, offer to clear it --}}
                  <form method="POST" action="{{ route('app.bookings.clear-bay', $booking) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
                            title="Clear bay for next vehicle">
                      🔄 Clear Bay
                    </button>
                  </form>
                @else
                  {{-- If trailer is in parking area, show location --}}
                  <span class="text-xs text-gray-500">In parking area</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
    <!-- No Trailers Message (if none in any category) -->
    @if($movementsOnSite->count() === 0)
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">📦 Trailers on Site</h3>
      </div>
      <div class="text-center py-8">
        <div class="text-gray-400 text-6xl mb-4">📭</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers Currently on Site</h3>
        <p class="text-gray-600">All trailers have been collected or are currently with vehicles.</p>
      </div>
    </div>
    @endif
    <!-- Legend -->
    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
      <h4 class="text-sm font-medium text-gray-800 mb-2">Status Legend:</h4>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
        <div class="flex items-center">
          <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 mr-2">🚛 Parked - Waiting</span>
          Attached, waiting to tip
        </div>
        <div class="flex items-center">
          <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 mr-2">🔄 Trailer Detached</span>
          Unit left, trailer detached
        </div>
        <div class="flex items-center">
          <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 mr-2">🚐 Just Arrived</span>
          Recently arrived
        </div>
        <div class="flex items-center">
          <span class="px-2 py-1 rounded-full bg-orange-100 text-orange-800 mr-2">⚡ Unloading</span>
          Currently being tipped
        </div>
        <div class="flex items-center">
          <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-800 mr-2">🚛 At Bay</span>
          In tipping bay
        </div>
        <div class="flex items-center">
          <span class="px-2 py-1 rounded-full bg-green-100 text-green-800 mr-2">✅ Empty</span>
          Ready for collection
        </div>
      </div>
    </div>
  </div>
</x-app-layout>