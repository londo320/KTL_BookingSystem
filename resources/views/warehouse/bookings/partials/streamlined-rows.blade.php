@php
  // Define route prefix once for the entire partial
  $workflowRoutePrefix = 'app.';
@endphp

@foreach($bookings->groupBy(fn($b) => $b->slot->depot->name) as $depotName => $group)
  {{-- Depot Header --}}
  <tr class="bg-gray-100 border-l-4 border-blue-500">
    <td colspan="5" class="px-4 py-2 font-semibold text-gray-800">
      🏭 {{ $depotName }} 
      <span class="text-sm font-normal text-gray-600">
        ({{ $group->where('arrived_at', '!=', null)->where('departed_at', null)->count() }} on site, 
        {{ $group->where('arrived_at', null)->count() }} expected)
      </span>
    </td>
  </tr>

  @foreach($group->sortBy(fn($b) => $b->slot->start_at) as $booking)
    @php
      // Determine row status for CSS classes and quick identification
      $rowStatus = 'awaiting';
      $statusIcon = '⏳';
      $statusColor = 'gray';
      $rowClass = '';
      
      if ($booking->cancelled_at) {
        $rowStatus = 'cancelled';
        $statusIcon = '❌';
        $statusColor = 'red';
        $rowClass = 'bg-red-50 border-l-4 border-red-500';
      } elseif ($booking->departed_at) {
        $rowStatus = 'completed';
        $statusIcon = '✅';
        $statusColor = 'green';
        $rowClass = 'bg-green-50 border-l-4 border-green-500 completed';
      } elseif ($booking->arrived_at) {
        $rowStatus = 'on-site';
        $statusIcon = '🚛';
        $statusColor = 'blue';
        $rowClass = 'bg-blue-50 border-l-4 border-blue-500 on-site';
      } else {
        $rowClass = 'hover:bg-gray-50 awaiting';
      }
      
      // Get current movement/location for display
      $movement = $booking->movements->first();
      $currentLocation = 'Not assigned';
      if ($movement) {
        if ($movement->tippingBay) {
          $currentLocation = "Bay: {$movement->tippingBay->name}";
        } elseif ($movement->tippingLocation) {
          $currentLocation = $movement->tippingLocation->name;
        } elseif ($movement->current_status === 'arrived') {
          $currentLocation = 'Arrived - needs assignment';
        }
      }
    @endphp
    
    <tr class="border-t transition-colors {{ $rowClass }}" data-booking-id="{{ $booking->id }}">
      {{-- Status & Booking Column --}}
      <td class="px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="text-lg">{{ $statusIcon }}</div>
          <div>
            <div class="font-mono text-sm font-bold text-{{ $statusColor }}-600">
              {{ $booking->booking_reference }}
            </div>
            <div class="text-xs text-gray-500">
              {{ $booking->bookingType->name ?? 'Standard' }}
            </div>
            @if($booking->poNumbers && $booking->poNumbers->count() > 0)
              <div class="text-xs text-blue-600">
                📦 {{ $booking->poNumbers->count() }} PO(s)
              </div>
            @endif
          </div>
        </div>
      </td>

      {{-- Time & Customer Column --}}
      <td class="px-4 py-3">
        <div class="text-sm font-medium text-gray-900">
          {{ $booking->customer->name ?? 'Walk-in' }}
        </div>
        <div class="text-xs text-gray-600">
          📅 {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}
        </div>
        @if($booking->arrived_at)
          <div class="text-xs text-green-600">
            ✅ Arrived: {{ $booking->arrived_at->format('H:i') }}
          </div>
        @elseif($booking->slot->start_at->isPast() && !$booking->arrived_at)
          @php
            $lateMinutes = $booking->slot->start_at->diffInMinutes(now());
          @endphp
          <div class="text-xs text-red-600 font-medium">
            🔴 Late: {{ floor($lateMinutes / 60) }}h {{ $lateMinutes % 60 }}m
          </div>
        @endif
        @if($booking->departed_at)
          <div class="text-xs text-gray-600">
            🏁 Left: {{ $booking->departed_at->format('H:i') }}
          </div>
        @endif
      </td>

      {{-- Vehicle & Location Column --}}
      <td class="px-4 py-3">
        @if($booking->vehicle_registration)
          <div class="text-sm font-medium">🚛 {{ $booking->vehicle_registration }}</div>
        @else
          <div class="text-sm text-gray-400">No vehicle registered</div>
        @endif
        
        @if($booking->container_number)
          <div class="text-xs text-gray-600">📦 {{ $booking->container_number }}</div>
        @endif
        
        @if($booking->carrier_company)
          <div class="text-xs text-gray-600">🏢 {{ $booking->carrier_company }}</div>
        @endif
        
        {{-- Current Location & Trailer Status --}}
        @if($booking->arrived_at && !$booking->departed_at)
          @php
            $movement = $booking->movements->first();
            $detailedLocation = 'Location unknown';
            $trailerStatus = 'attached';
            $statusBadgeClass = 'bg-gray-100 text-gray-800';
            
            if ($movement) {
              // Determine detailed location and trailer status
              if ($movement->tippingBay) {
                $detailedLocation = "Bay {$movement->tippingBay->name}";
                $statusBadgeClass = 'bg-orange-100 text-orange-800';
                
                if ($movement->current_status === 'unloading') {
                  $trailerStatus = 'tipping';
                } elseif ($movement->current_status === 'empty') {
                  $trailerStatus = 'empty';
                } else {
                  $trailerStatus = 'at bay';
                }
              } elseif ($movement->tippingLocation) {
                $detailedLocation = $movement->tippingLocation->name;
                
                if ($movement->current_status === 'trailer_dropped') {
                  $trailerStatus = 'dropped (empty)';
                  $statusBadgeClass = 'bg-purple-100 text-purple-800';
                } elseif ($movement->current_status === 'in_location') {
                  $trailerStatus = 'parked (loaded)';
                  $statusBadgeClass = 'bg-blue-100 text-blue-800';
                } else {
                  $trailerStatus = 'in zone';
                  $statusBadgeClass = 'bg-yellow-100 text-yellow-800';
                }
              } elseif ($movement->current_status === 'arrived') {
                $detailedLocation = 'Site entrance';
                $trailerStatus = 'needs assignment';
                $statusBadgeClass = 'bg-red-100 text-red-800';
              } elseif ($movement->unit_departed_at && !$movement->collection_unit_departed_at) {
                $detailedLocation = $movement->tippingLocation ? $movement->tippingLocation->name : 'parking area';
                $trailerStatus = 'unit departed';
                $statusBadgeClass = 'bg-orange-100 text-orange-800';
              }
              
              // Check if unit has left but trailer still on site
              if ($movement->unit_departed_at && !$movement->collection_unit_departed_at) {
                $trailerStatus = 'trailer only';
                $statusBadgeClass = 'bg-purple-100 text-purple-800';
              }
            }
            
            // Override with actual booking data if available
            if ($booking->trailer_left_on_site && !$booking->trailer_collected_at) {
              $trailerStatus = $booking->dropped_trailer_status ?? 'dropped';
              $detailedLocation = $booking->dropped_trailer_location ?? $detailedLocation;
              $statusBadgeClass = 'bg-purple-100 text-purple-800';
            }
          @endphp
          
          <div class="text-xs mt-1 space-y-1">
            {{-- Location --}}
            <div class="px-2 py-1 rounded bg-blue-50 text-blue-800">
              📍 {{ $detailedLocation }}
            </div>
            
            {{-- Trailer Status --}}
            <div class="px-2 py-1 rounded {{ $statusBadgeClass }}">
              @if($trailerStatus === 'tipping')
                🏗️ Tipping in progress
              @elseif($trailerStatus === 'empty')
                📦 Empty - ready for collection
              @elseif($trailerStatus === 'dropped (empty)')
                🚚 Trailer dropped (empty)
              @elseif($trailerStatus === 'parked (loaded)')
                🚛 Parked - awaiting bay
              @elseif($trailerStatus === 'trailer only')
                🔴 Unit departed - trailer on site
              @elseif($trailerStatus === 'needs assignment')
                ⚠️ Needs location assignment
              @elseif($trailerStatus === 'at bay')
                🏗️ At tipping bay
              @else
                🚚 {{ ucfirst(str_replace('_', ' ', $trailerStatus)) }}
              @endif
            </div>
            
            {{-- Time information --}}
            @if($movement)
              @if($movement->moved_to_bay_at && $movement->current_status === 'unloading')
                <div class="text-xs text-gray-500">
                  ⏱️ Tipping {{ $movement->moved_to_bay_at->diffForHumans(null, true) }}
                </div>
              @elseif($movement->moved_to_location_at)
                <div class="text-xs text-gray-500">
                  ⏱️ In location {{ $movement->moved_to_location_at->diffForHumans(null, true) }}
                </div>
              @elseif($movement->unit_departed_at)
                <div class="text-xs text-gray-500">
                  🚗 Unit left {{ $movement->unit_departed_at->diffForHumans(null, true) }}
                </div>
              @endif
            @endif
            
            {{-- Collection info if trailer dropped --}}
            @if($booking->trailer_collection_scheduled)
              @if($booking->trailer_collection_scheduled->isPast())
                <div class="text-xs text-red-600 font-medium">
                  ⚠️ Collection overdue
                </div>
              @else
                <div class="text-xs text-green-600">
                  📅 Collection due {{ $booking->trailer_collection_scheduled->format('H:i') }}
                </div>
              @endif
            @endif
          </div>
        @endif
      </td>

      {{-- Load Info Column --}}
      <td class="px-4 py-3">
        @php
          $expectedCases = $booking->total_expected_cases;
          $actualCases = $booking->total_actual_cases;
          $expectedPallets = $booking->total_expected_pallets;
          $actualPallets = $booking->total_actual_pallets;
        @endphp
        
        <div class="text-sm">
          <div>📦 {{ $actualCases > 0 ? number_format($actualCases) : '-' }}/{{ number_format($expectedCases) }} cases</div>
          <div>🎯 {{ $actualPallets > 0 ? number_format($actualPallets) : '-' }}/{{ number_format($expectedPallets) }} pallets</div>
        </div>
        
        @if($actualCases > 0 && $expectedCases > 0)
          @php
            $variance = $actualCases - $expectedCases;
          @endphp
          @if($variance != 0)
            <div class="text-xs {{ $variance > 0 ? 'text-blue-600' : 'text-red-600' }} font-medium">
              {{ $variance > 0 ? '↗' : '↘' }} {{ abs($variance) }}
            </div>
          @else
            <div class="text-xs text-green-600 font-medium">✓ Match</div>
          @endif
        @endif
      </td>

      {{-- Streamlined Actions Column --}}
      <td class="px-4 py-3">
        <div class="flex flex-col gap-1">
          {{-- Primary Action Based on Status --}}
          @if(!$booking->cancelled_at && !$booking->arrived_at)
            {{-- Main action: Process Arrival --}}
            <a href="{{ route($workflowRoutePrefix . 'bookings.arrival.form', $booking) }}" 
               class="w-full px-3 py-2 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700 transition-colors text-center block">
              🚛 Process Arrival
            </a>
          @elseif($booking->arrived_at && !$booking->departed_at)
            {{-- Main action: Tipping Workflow --}}
            <a href="{{ route($workflowRoutePrefix . 'tipping-workflow.show', $booking) }}"
               class="w-full px-3 py-2 bg-orange-600 text-white rounded text-xs font-medium hover:bg-orange-700 transition-colors text-center">
              🏗️ Manage Workflow
            </a>
            {{-- Check if unit has already departed --}}
            @php
              $movement = $booking->movements->first();
              $unitHasDeparted = $movement && $movement->unit_departed_at;
            @endphp
            
            @if($unitHasDeparted)
              {{-- Show unit departure time instead of Mark Departed button --}}
              <div class="w-full px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs text-center">
                🚗 Unit Left: {{ $movement->unit_departed_at->format('M j H:i') }}
              </div>
            @else
              {{-- Secondary action: Mark Departed --}}
              <button onclick="openDepartureModal({{ $booking->id }}, '{{ $booking->booking_reference }}', '{{ addslashes($booking->customer->name ?? 'N/A') }}', '{{ $booking->vehicle_registration ?? '' }}', 'CURRENT', 'Current Location')" 
                      class="w-full px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors">
                🏁 Mark Departed
              </button>
            @endif
          @elseif($booking->departed_at)
            {{-- Completed - show view option --}}
            <a href="{{ route($workflowRoutePrefix . 'bookings.show', $booking) }}"
               class="w-full px-3 py-2 bg-gray-600 text-white rounded text-xs font-medium hover:bg-gray-700 transition-colors text-center">
              👁️ View Details
            </a>
          @else
            {{-- Cancelled or other status --}}
            <a href="{{ route($workflowRoutePrefix . 'bookings.show', $booking) }}"
               class="w-full px-3 py-2 bg-gray-400 text-white rounded text-xs font-medium hover:bg-gray-500 transition-colors text-center">
              👁️ View
            </a>
          @endif

          {{-- Quick Secondary Actions --}}
          @if(!$booking->cancelled_at && !$booking->departed_at)
            <div class="flex gap-1">
              <a href="{{ route($workflowRoutePrefix . 'bookings.show', $booking) }}" 
                 class="flex-1 px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 transition-colors text-center"
                 title="View details">
                👁️
              </a>
              <a href="{{ route($workflowRoutePrefix . 'bookings.edit', $booking) }}" 
                 class="flex-1 px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 transition-colors text-center"
                 title="Edit booking">
                ✏️
              </a>
            </div>
          @endif
        </div>
      </td>
    </tr>
  @endforeach
@endforeach

@if($bookings->count() === 0)
  <tr>
    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
      <div class="text-4xl mb-4">📋</div>
      <div class="text-lg font-medium">No bookings found</div>
      <div class="text-sm">Try adjusting your filters or date range</div>
    </td>
  </tr>
@endif