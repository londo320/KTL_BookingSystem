<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">🚛 Trailer Operations Dashboard</h2>
        <p class="text-sm text-gray-600 mt-1">Complete operational view with workflow priorities and timing</p>
      </div>
      <div class="flex space-x-3">
        <a href="{{ route('app.trailer-location-report') }}"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          📊 Location Report
        </a>
        <a href="{{ route('app.tipping-workflow.dashboard') }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          🔄 Tipping Dashboard
        </a>
        <a href="{{ route('app.bookings.index') }}"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          ← Back to Bookings
        </a>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-full mx-auto px-4">
    <!-- Priority Guide -->
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
      <h3 class="text-sm font-semibold text-blue-800 mb-2">🎯 Workflow Priority Order</h3>
      <div class="grid grid-cols-1 md:grid-cols-6 gap-2 text-xs">
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">1</span>
          <span>Currently Tipping</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">2</span>
          <span>Ready to Start</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold">3</span>
          <span>Clear Bay/Location</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">4</span>
          <span>Move to Bay</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-purple-500 text-white flex items-center justify-center font-bold">5</span>
          <span>Trailer Detached</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-gray-500 text-white flex items-center justify-center font-bold">6</span>
          <span>Just Arrived</span>
        </div>
      </div>
    </div>
    <!-- Summary Stats -->
    @php
      $stats = [
        'total_on_site' => $movementsOnSite->count(),
        'currently_tipping' => $movementsOnSite->where('current_status', 'unloading')->count(),
        'ready_to_tip' => $movementsOnSite->where('current_status', 'at_bay')->count(),
        'need_clearing' => $movementsOnSite->where('current_status', 'empty')->count(),
        'loaded_attached' => $movementsOnSite->where('calculated_data.is_loaded', true)->where('calculated_data.is_attached', true)->count(),
        'empty_trailers' => $movementsOnSite->where('calculated_data.is_loaded', false)->count(),
      ];
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-gray-600">{{ $stats['total_on_site'] }}</div>
        <div class="text-sm text-gray-600">Total On Site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-red-600">{{ $stats['currently_tipping'] }}</div>
        <div class="text-sm text-gray-600">Currently Tipping</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-orange-600">{{ $stats['ready_to_tip'] }}</div>
        <div class="text-sm text-gray-600">Ready to Start</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['need_clearing'] }}</div>
        <div class="text-sm text-gray-600">Need Clearing</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-blue-600">{{ $stats['loaded_attached'] }}</div>
        <div class="text-sm text-gray-600">Loaded & Attached</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-green-600">{{ $stats['empty_trailers'] }}</div>
        <div class="text-sm text-gray-600">Empty Trailers</div>
      </div>
    </div>
    <!-- Main Operations Table -->
    @if($movementsOnSite->count() > 0)
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-medium text-gray-900">🎯 Trailer Operations Queue (Priority Order)</h3>
        <p class="text-sm text-gray-600 mt-1">Sorted by workflow priority and time in current status</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle/Container</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Load State</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrived</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time on Site</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">In Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipping Duration</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($movementsOnSite as $movement)
            @php 
              $booking = $movement->booking;
              $data = $movement->calculated_data;
              // Priority styling
              $priorityColors = [
                1 => 'bg-red-500 text-white',
                2 => 'bg-orange-500 text-white', 
                3 => 'bg-yellow-500 text-white',
                4 => 'bg-blue-500 text-white',
                5 => 'bg-purple-500 text-white',
                6 => 'bg-gray-500 text-white',
                7 => 'bg-gray-400 text-white'
              ];
              // Status display
              $statusDisplay = match($movement->current_status) {
                'unloading' => '⚡ Tipping Active',
                'at_bay' => '🚛 At Tipping Bay',
                'empty' => '✅ Tipped - Empty',
                'in_location' => '🚛 Parked - Waiting',
                'trailer_dropped' => '🔄 Trailer Detached',
                'trailer_collected' => '🚚 Being Collected',
                'arrived' => '🚐 Just Arrived',
                'in_waiting' => '⏳ In Waiting Area',
                default => ucwords(str_replace('_', ' ', $movement->current_status))
              };
              // Status colors
              $statusColors = [
                'unloading' => 'bg-red-100 text-red-800',
                'at_bay' => 'bg-orange-100 text-orange-800',
                'empty' => 'bg-green-100 text-green-800',
                'in_location' => 'bg-blue-100 text-blue-800',
                'trailer_dropped' => 'bg-purple-100 text-purple-800',
                'trailer_collected' => 'bg-indigo-100 text-indigo-800',
                'arrived' => 'bg-gray-100 text-gray-800',
                'in_waiting' => 'bg-yellow-100 text-yellow-800',
              ];
              // Helper function to format minutes
              $formatMinutes = function($minutes) {
                if (!$minutes || $minutes < 0) return '-';
                $minutes = abs($minutes); // Ensure positive
                if ($minutes < 60) return round($minutes) . 'm';
                $hours = floor($minutes / 60);
                $mins = round($minutes % 60);
                if ($mins == 0) return $hours . 'h';
                return $hours . 'h ' . $mins . 'm';
              };
            @endphp
            <tr class="hover:bg-gray-50 {{ $data['workflow_priority'] <= 2 ? 'bg-red-50' : '' }}">
              <!-- Priority -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="w-8 h-8 rounded-full {{ $priorityColors[$data['workflow_priority']] }} flex items-center justify-center font-bold text-sm">
                  {{ $data['workflow_priority'] }}
                </span>
              </td>
              <!-- Booking -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <a href="{{ route('app.bookings.show', $booking) }}" class="hover:underline">
                  {{ $booking->booking_reference }}
                </a>
                @if($booking->booked_at)
                  <br><span class="text-xs text-gray-500">{{ $booking->booked_at->format('M j H:i') }}</span>
                @endif
              </td>
              <!-- Customer -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $booking->customer->name ?? 'Unknown' }}
                @if($booking->poNumbers && $booking->poNumbers->count() > 0)
                  <br><span class="text-xs text-blue-600">📦 {{ $booking->poNumbers->pluck('po_number')->join(', ') }}</span>
                @endif
              </td>
              <!-- Vehicle/Container -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->vehicle_registration ?? 'Not specified' }}
                @if($booking->container_number)
                  <br><span class="text-xs font-mono">{{ $booking->container_number }}</span>
                @endif
              </td>
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$movement->current_status] ?? 'bg-gray-100 text-gray-800' }}">
                  {{ $statusDisplay }}
                </span>
              </td>
              <!-- Load State -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <div class="flex flex-col space-y-1">
                  <span class="px-2 py-1 text-xs rounded-full {{ $data['is_loaded'] ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                    {{ $data['is_loaded'] ? '📦 Loaded' : '📭 Empty' }}
                  </span>
                  <span class="px-2 py-1 text-xs rounded-full {{ $data['is_attached'] ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                    {{ $data['is_attached'] ? '🔗 Attached' : '🔄 Detached' }}
                  </span>
                </div>
              </td>
              <!-- Location -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($movement->tippingBay)
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    🏗️ {{ $movement->tippingBay->name }}
                  </span>
                @elseif($movement->tippingLocation)
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    📍 {{ $movement->tippingLocation->name }}
                  </span>
                @else
                  <span class="text-gray-400">No location</span>
                @endif
              </td>
              <!-- Arrived -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($data['arrival_time'])
                  <div class="font-medium">{{ $data['arrival_time']->format('M j H:i') }}</div>
                  <div class="text-xs text-gray-400">{{ $data['arrival_time']->diffForHumans() }}</div>
                  @if($movement->unit_departed_at)
                    <div class="text-xs text-orange-600 mt-1">
                      🚛 Unit left: {{ $movement->unit_departed_at->format('H:i') }}
                    </div>
                  @endif
                  @if($movement->collection_unit_arrived_at)
                    <div class="text-xs text-green-600 mt-1">
                      🚚 Collection: {{ $movement->collection_unit_arrived_at->format('H:i') }}
                    </div>
                  @endif
                @else
                  <span class="text-gray-400">Unknown</span>
                @endif
              </td>
              <!-- Time on Site -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span class="font-mono {{ $data['time_on_site_minutes'] > 240 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                  {{ $formatMinutes($data['time_on_site_minutes']) }}
                </span>
                @if($data['time_on_site_minutes'] > 240)
                  <br><span class="text-xs text-red-500">⚠️ Long wait</span>
                @endif
              </td>
              <!-- In Current Status -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span class="font-mono text-gray-900">
                  {{ $formatMinutes($data['time_in_current_status_minutes']) }}
                </span>
                @if($data['status_start_time'])
                  <br><span class="text-xs text-gray-400">Since {{ $data['status_start_time']->format('H:i') }}</span>
                @endif
              </td>
              <!-- Tipping Duration -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                @if($data['tipping_duration_minutes'])
                  <span class="font-mono text-green-600">{{ $formatMinutes($data['tipping_duration_minutes']) }}</span>
                  <br><span class="text-xs text-green-500">✅ Completed</span>
                @elseif($movement->current_status === 'unloading' && $movement->unloading_started_at)
                  @php
                    $currentTippingDuration = $movement->unloading_started_at->diffInMinutes(now());
                  @endphp
                  <span class="font-mono text-orange-600">{{ $formatMinutes($currentTippingDuration) }}</span>
                  <br><span class="text-xs text-orange-500">⏱️ In progress</span>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <div class="flex flex-col space-y-1">
                  <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                     class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 text-center">
                    🔧 Manage
                  </a>
                  <a href="{{ route('app.bookings.show', $booking) }}" 
                     class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 text-center">
                    👁️ View
                  </a>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow">
      <div class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">🚛</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers Currently on Site</h3>
        <p class="text-gray-600">All trailers have been processed and collected.</p>
      </div>
    </div>
    @endif
  </div>
</x-warehouse-layout>