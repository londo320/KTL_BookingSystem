<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">⚡ Operational Queue Management</h2>
        <p class="text-sm text-gray-600 mt-1">Efficiency-optimized queue for 30+ containers • Auto-prioritized for maximum throughput</p>
      </div>
      <div class="flex items-center space-x-4">
        {{-- Depot Filter --}}
        <form method="GET" class="flex items-center space-x-2">
          <label for="depot_id" class="text-sm font-medium text-gray-700">Depot:</label>
          <select name="depot_id" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded-md text-sm">
            <option value="" {{ !$currentDepotId ? 'selected' : '' }}>All Depots (View Only)</option>
            @foreach($allDepots as $depot)
              <option value="{{ $depot->id }}" {{ $currentDepotId == $depot->id ? 'selected' : '' }}>
                {{ $depot->name }} {{ $depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)' }}
              </option>
            @endforeach
          </select>
        </form>
        {{-- Efficiency Score --}}
        <div class="text-sm">
          <span class="font-mono bg-green-100 text-green-800 px-2 py-1 rounded">
            {{ now()->format('H:i:s') }} | Efficiency: {{ $stats['efficiency_score'] }}%
          </span>
        </div>
        {{-- Priority Settings Button --}}
        <div class="flex items-center space-x-2">
          <button onclick="openPrioritySettings()" 
                  class="px-3 py-1 bg-purple-600 text-white text-sm rounded hover:bg-purple-700 flex items-center space-x-1">
            <span>⚙️</span>
            <span>Priority Settings</span>
          </button>
        </div>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-full mx-auto px-4">
    <!-- Operational Overview -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $stats['total_on_site'] }}</div>
        <div class="text-xs text-blue-600">Total On Site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-orange-600">{{ $stats['ready_to_tip'] }}</div>
        <div class="text-xs text-orange-600">Ready to Tip</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-red-600">{{ $stats['currently_tipping'] }}</div>
        <div class="text-xs text-red-600">Currently Tipping</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-purple-600">{{ $stats['empty_waiting_collection'] }}</div>
        <div class="text-xs text-purple-600">Awaiting Collection</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-gray-600">{{ $stats['average_wait_time'] }}m</div>
        <div class="text-xs text-gray-600">Avg Wait Time</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold {{ $stats['efficiency_score'] > 80 ? 'text-green-600' : ($stats['efficiency_score'] > 60 ? 'text-yellow-600' : 'text-red-600') }}">
          {{ $stats['efficiency_score'] }}%
        </div>
        <div class="text-xs text-gray-600">Bay Efficiency</div>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- LEFT: TIPPING PRIORITY QUEUE -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 bg-orange-50">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-orange-800">🎯 TIPPING PRIORITY QUEUE</h3>
                <p class="text-sm text-orange-600 mt-1">Live Tips first 🏆, then Drops by slot time ⏳ • Process from top to bottom</p>
              </div>
              <div class="flex items-center space-x-2">
                <!-- Column Toggle Controls -->
                <div class="flex items-center space-x-1 text-xs">
                  <span class="text-orange-700 font-medium">Show:</span>
                  <label class="flex items-center">
                    <input type="checkbox" id="toggle-bay-status" checked class="mr-1 rounded">
                    <span class="text-orange-700">Bay Status</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" id="toggle-priority-details" checked class="mr-1 rounded">
                    <span class="text-orange-700">Priority Details</span>
                  </label>
                  <label class="flex items-center">
                    <input type="checkbox" id="toggle-collection-urgency" checked class="mr-1 rounded">
                    <span class="text-orange-700">Urgency Info</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trailer</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                  @if(!$currentDepotId)
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Depot</th>
                  @endif
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip Type</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wait Time</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase priority-details">Priority Score</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase priority-details">Priority Reason</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @foreach($tippingQueue->take(15) as $index => $movement)
                @php 
                  $booking = $movement->booking;
                  $waitMinutes = 0;
                  if ($movement->current_status === 'trailer_dropped' && $movement->trailer_dropped_at) {
                      $waitMinutes = round($movement->trailer_dropped_at->diffInMinutes(now()));
                  } elseif ($movement->moved_to_location_at) {
                      $waitMinutes = round($movement->moved_to_location_at->diffInMinutes(now()));
                  }
                  $priorityColor = $index < 3 ? 'bg-red-50' : ($index < 6 ? 'bg-orange-50' : 'bg-white');
                @endphp
                <tr class="hover:bg-blue-50 {{ $priorityColor }}">
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <span class="w-6 h-6 rounded-full text-xs font-bold flex items-center justify-center
                        {{ $index < 3 ? 'bg-red-500 text-white' : ($index < 6 ? 'bg-orange-500 text-white' : 'bg-gray-400 text-white') }}">
                        {{ $index + 1 }}
                      </span>
                    </div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="font-medium text-blue-600">{{ $booking->booking_reference }}</div>
                    <div class="text-xs text-gray-500">{{ $booking->vehicle_registration }}</div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">{{ $booking->container_number ?: 'Not specified' }}</div>
                    <div class="text-xs text-gray-500">Container/Trailer #</div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $booking->customer->name ?? 'Unknown' }}</div>
                    @if($booking->poNumbers && $booking->poNumbers->count() > 0)
                      <div class="text-xs text-blue-600">📦 {{ $booking->poNumbers->count() }} PO(s)</div>
                    @endif
                  </td>
                  @if(!$currentDepotId)
                    <td class="px-4 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ $booking->slot->depot->name ?? 'Unknown' }}</div>
                    </td>
                  @endif
                  <td class="px-4 py-4 whitespace-nowrap">
                    @if($movement->tippingLocation)
                      <div class="text-sm text-yellow-700">{{ $movement->tippingLocation->name }}</div>
                    @else
                      <div class="text-sm text-gray-400">No location</div>
                    @endif
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    @php
                      $tippingType = $booking->tipping_type;
                      $typeConfig = [
                        'live_tip' => ['icon' => '🚛📦', 'label' => 'Live Tip', 'color' => 'bg-blue-100 text-blue-800', 'priority' => '🏆'],
                        'drop' => ['icon' => '📦', 'label' => 'Drop', 'color' => 'bg-green-100 text-green-800', 'priority' => '⏳'],
                        null => ['icon' => '❓', 'label' => 'Not Set', 'color' => 'bg-yellow-100 text-yellow-800', 'priority' => '❓']
                      ];
                      $config = $typeConfig[$tippingType] ?? $typeConfig[null];
                    @endphp
                    @if($tippingType)
                      <div class="flex flex-col items-center">
                        <div class="flex items-center space-x-1 mb-1">
                          <span class="text-lg">{{ $config['icon'] }}</span>
                          <span class="text-xs">{{ $config['priority'] }}</span>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $config['color'] }}">
                          {{ $config['label'] }}
                        </span>
                        @if($tippingType === 'live_tip')
                          <div class="text-xs text-blue-600 mt-1 font-medium">Priority</div>
                        @elseif($tippingType === 'drop')
                          <div class="text-xs text-green-600 mt-1">By Slot Time</div>
                        @endif
                      </div>
                    @else
                      <select onchange="setTippingType({{ $booking->id }}, this.value)" 
                              class="px-2 py-1 text-xs border border-gray-300 rounded">
                        <option value="">Select Type</option>
                        <option value="live_tip">🚛📦 Live Tip</option>
                        <option value="drop">📦 Drop</option>
                      </select>
                    @endif
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm font-mono {{ $waitMinutes > 120 ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                      {{ round($waitMinutes/60, 1) }}h
                    </div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap priority-details">
                    <div class="text-center">
                      <div class="text-lg font-bold {{ $index < 3 ? 'text-red-600' : ($index < 6 ? 'text-orange-600' : 'text-gray-700') }}" 
                           title="Queue Position: #{{ $index + 1 }}">
                        {{ $movement->efficiency_priority ?? 0 }}
                      </div>
                      <div class="text-xs text-gray-500">points</div>
                    </div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap priority-details">
                    <div class="text-xs">
                      @foreach($movement->priority_reasons as $reason)
                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded-full mr-1 mb-1">{{ $reason }}</span>
                      @endforeach
                    </div>
                  </td>
                  <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                      @php
                        $tippingType = $booking->tipping_type;
                        $currentStatus = $movement->current_status;
                        $isLiveTip = $tippingType === 'live_tip';
                        $isDrop = $tippingType === 'drop';
                      @endphp
                      @if($isLiveTip)
                        {{-- Live Tip Workflow --}}
                        @if($currentStatus === 'in_parking')
                          <button onclick="shuntToBay({{ $booking->id }})" 
                                  class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                            🚛📦 Move to Bay
                          </button>
                        @elseif($currentStatus === 'at_bay')
                          <button onclick="startTipping({{ $booking->id }})" 
                                  class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                            📦 Start Tipping
                          </button>
                        @elseif($currentStatus === 'unloading')
                          <button onclick="completeTipping({{ $booking->id }})" 
                                  class="px-3 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600">
                            ✅ Complete Tipping
                          </button>
                        @else
                          <div class="text-xs text-gray-500">Status: {{ $currentStatus }}</div>
                        @endif
                      @elseif($isDrop)
                        {{-- Drop Workflow: Start Tipping Process --}}
                        @if(in_array($currentStatus, ['in_parking', 'at_bay']))
                          <button onclick="startTipping({{ $booking->id }})" 
                                  class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                            📦 Start Tipping
                          </button>
                        @elseif($currentStatus === 'unloading')
                          <button onclick="completeTipping({{ $booking->id }})" 
                                  class="px-3 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600">
                            ✅ Complete Tipping
                          </button>
                        @else
                          <div class="text-xs text-gray-500">Status: {{ $currentStatus }}</div>
                        @endif
                      @else
                        {{-- No Tipping Type Set: Show Both Options --}}
                        <div class="text-xs text-yellow-600 mb-1">Set Tip Type First</div>
                      @endif
                      {{-- Quick Priority Boost --}}
                      <select onchange="quickPriorityBoost({{ $booking->id }}, this.value)" 
                              class="px-1 py-1 text-xs border border-gray-300 rounded">
                        <option value="0" {{ ($booking->manual_priority_boost ?? 0) == 0 ? 'selected' : '' }}>Normal</option>
                        <option value="50" {{ ($booking->manual_priority_boost ?? 0) == 50 ? 'selected' : '' }}>+50 High</option>
                        <option value="100" {{ ($booking->manual_priority_boost ?? 0) == 100 ? 'selected' : '' }}>+100 Urgent</option>
                        <option value="200" {{ ($booking->manual_priority_boost ?? 0) == 200 ? 'selected' : '' }}>+200 Emergency</option>
                        <option value="-25" {{ ($booking->manual_priority_boost ?? 0) == -25 ? 'selected' : '' }}>-25 Delay</option>
                      </select>
                    </div>
                  </td>
                </tr>
                @endforeach
                @if($tippingQueue->count() == 0)
                <tr>
                  <td colspan="{{ !$currentDepotId ? '11' : '10' }}" class="px-4 py-8 text-center text-gray-500">
                    <div class="text-4xl mb-2">🎉</div>
                    <div>No trailers waiting to tip - all caught up!</div>
                  </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- RIGHT PANEL: BAY STATUS & URGENCIES -->
      <div class="space-y-6" id="right-panel">
        <!-- BAY EFFICIENCY STATUS -->
        <div class="bg-white rounded-lg shadow bay-status-section">
          <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-lg font-medium text-blue-800">🏗️ BAY STATUS</h3>
                <p class="text-sm text-blue-600 mt-1">Current & upcoming availability</p>
              </div>
              <div class="flex items-center space-x-2">
                <label class="flex items-center text-xs">
                  <input type="checkbox" id="filter-in-use-only" class="mr-1 rounded">
                  <span class="text-blue-700">In Use Only</span>
                </label>
              </div>
            </div>
          </div>
          <div class="p-4 space-y-3">
            @foreach($bayStatus as $bay)
            <div class="border border-gray-200 rounded p-3 bay-card {{ $bay['status'] === 'available' ? 'bay-available' : 'bay-in-use' }}">
              <div class="flex items-center justify-between mb-2">
                <div class="font-medium">{{ $bay['bay']->name }}</div>
                <div>
                  @if($bay['status'] === 'available')
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Available Now</span>
                  @elseif($bay['status'] === 'unloading')
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Tipping</span>
                  @else
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Occupied</span>
                  @endif
                </div>
              </div>
              @if($bay['current_booking'])
                <div class="mt-2">
                  <div class="flex items-center justify-between mb-1">
                    <a href="{{ route('app.bookings.show', $bay['current_booking']) }}" 
                       class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                      {{ $bay['current_booking']->booking_reference }}
                    </a>
                    <a href="{{ route('app.tipping-workflow.show', $bay['current_booking']) }}" 
                       class="px-2 py-1 bg-orange-500 text-white text-xs rounded hover:bg-orange-600 flex items-center">
                      ⚡ Workflow
                    </a>
                  </div>
                  <div class="text-xs text-gray-600 mb-1">
                    📦 {{ $bay['current_booking']->container_number ?: 'No container #' }} • {{ $bay['current_booking']->customer->name ?? 'Unknown' }}
                  </div>
                  <div class="text-xs text-gray-500">
                    Est. free: {{ $bay['estimated_free_at']->format('H:i') }}
                    @if($bay['estimated_duration_remaining'] > 0)
                      ({{ $bay['estimated_duration_remaining'] }}m remaining)
                    @endif
                  </div>
                </div>
              @endif
            </div>
            @endforeach
          </div>
        </div>
        <!-- COLLECTION URGENCY -->
        <div class="bg-white rounded-lg shadow collection-urgency-section">
          <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
            <h3 class="text-lg font-medium text-red-800">⚠️ COLLECTION URGENCY</h3>
            <p class="text-sm text-red-600 mt-1">Empty trailers waiting collection</p>
          </div>
          <div class="p-4 space-y-2">
            @foreach($collectionUrgency->take(5) as $movement)
            @php 
              $urgencyColors = [
                'critical' => 'bg-red-100 text-red-800',
                'high' => 'bg-orange-100 text-orange-800',
                'medium' => 'bg-yellow-100 text-yellow-800',
                'normal' => 'bg-gray-100 text-gray-800'
              ];
            @endphp
            <div class="border border-gray-200 rounded p-2">
              <div class="flex items-center justify-between">
                <div class="text-sm font-medium">{{ $movement->booking->booking_reference }}</div>
                <span class="px-2 py-1 text-xs rounded {{ $urgencyColors[$movement->collection_urgency] }}">
                  {{ round($movement->hours_waiting_collection, 1) }}h
                </span>
              </div>
              <div class="text-xs text-gray-500">{{ $movement->booking->customer->name ?? 'Unknown' }}</div>
            </div>
            @endforeach
            @if($collectionUrgency->count() == 0)
            <div class="text-center text-gray-500 py-4">
              <div class="text-2xl mb-1">✅</div>
              <div class="text-sm">All collections up to date</div>
            </div>
            @endif
          </div>
        </div>
        <!-- NEW ARRIVALS -->
        @if($newArrivals->count() > 0)
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
            <h3 class="text-lg font-medium text-green-800">🚐 NEW ARRIVALS</h3>
            <p class="text-sm text-green-600 mt-1">Need parking area assignment</p>
          </div>
          <div class="p-4 space-y-2">
            @foreach($newArrivals->take(3) as $movement)
            <div class="border border-gray-200 rounded p-2">
              <div class="text-sm font-medium">{{ $movement->booking->booking_reference }}</div>
              <div class="text-xs text-gray-500">{{ $movement->booking->customer->name ?? 'Unknown' }}</div>
              <button onclick="assignParkingArea({{ $movement->booking->id }})" 
                      class="mt-1 px-2 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                📍 Assign Parking Area
              </button>
            </div>
            @endforeach
          </div>
        </div>
        @endif
        <!-- PRIORITY SCORING EXPLANATION -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
            <h3 class="text-lg font-medium text-purple-800">📊 PRIORITY SCORING</h3>
            <p class="text-sm text-purple-600 mt-1">How the tipping queue is prioritized</p>
          </div>
          <div class="p-4 space-y-3">
            <div class="text-xs space-y-2">
              <div class="flex justify-between items-center py-1 border-b border-gray-100">
                <span class="font-medium text-gray-700">Customer Priority</span>
                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">+100 pts</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-gray-100">
                <span class="font-medium text-gray-700">Wait Time</span>
                <span class="bg-green-100 text-green-800 px-2 py-1 rounded">+1 per min</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-gray-100">
                <span class="font-medium text-gray-700">Urgent Collection</span>
                <span class="bg-red-100 text-red-800 px-2 py-1 rounded">+75 pts</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-gray-100">
                <span class="font-medium text-gray-700">Overdue Appointment</span>
                <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded">+50 pts</span>
              </div>
              <div class="flex justify-between items-center py-1 border-b border-gray-100">
                <span class="font-medium text-gray-700">Type Efficiency</span>
                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded">+25 pts</span>
              </div>
              <div class="flex justify-between items-center py-1">
                <span class="font-medium text-gray-700">Manual Boost</span>
                <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded">±200 pts</span>
              </div>
            </div>
            <div class="mt-3 p-2 bg-gray-50 rounded text-xs">
              <p class="text-gray-600">
                <strong>Higher scores = Higher priority.</strong> Trailers are automatically ordered by total score for maximum throughput efficiency.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Bay Selection Modal -->
  <div id="bay-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-medium mb-4">Select Tipping Bay</h3>
        <div id="bay-list" class="space-y-2 mb-4">
          <!-- Dynamic bay options will be loaded here -->
        </div>
        <div class="flex justify-end space-x-2">
          <button onclick="closeBayModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
            Cancel
          </button>
          <button id="confirm-bay" onclick="confirmBaySelection()" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
            Assign Bay
          </button>
        </div>
      </div>
    </div>
  </div>
  <!-- Parking Area Modal -->
  <div id="parking-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-medium mb-4">Select Parking Area</h3>
        <div id="parking-list" class="space-y-2 mb-4">
          <!-- Dynamic zone options will be loaded here -->
        </div>
        <div class="flex justify-end space-x-2">
          <button onclick="closeParkingModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
            Cancel
          </button>
          <button id="confirm-parking" onclick="confirmParkingSelection()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
            Assign Parking Area
          </button>
        </div>
      </div>
    </div>
  </div>
  <script>
    let selectedBookingId = null;
    let selectedBayId = null;
    let selectedParkingId = null;
    function shuntToBay(bookingId) {
      selectedBookingId = bookingId;
      selectedBayId = null;
      // Load available bays - pass current depot_id
      const depotId = new URLSearchParams(window.location.search).get('depot_id');
      const url = depotId ? `/admin/operations/available-bays?depot_id=${depotId}` : '/admin/operations/available-bays';
      fetch(url)
        .then(response => response.json())
        .then(bays => {
          const bayList = document.getElementById('bay-list');
          bayList.innerHTML = '';
          if (bays.length === 0) {
            bayList.innerHTML = '<div class="text-gray-500 text-center py-4">No available bays</div>';
            return;
          }
          bays.forEach(bay => {
            const bayOption = document.createElement('div');
            bayOption.className = 'p-3 border border-gray-200 rounded cursor-pointer hover:bg-blue-50';
            bayOption.innerHTML = `
              <div class="font-medium">${bay.name}</div>
              <div class="text-sm text-gray-500">${bay.code || ''}</div>
            `;
            bayOption.onclick = () => selectBay(bay.id, bayOption);
            bayList.appendChild(bayOption);
          });
          document.getElementById('bay-modal').classList.remove('hidden');
        })
        .catch(error => {
          console.error('Error loading bays:', error);
          alert('Error loading available bays');
        });
    }
    function assignParkingArea(bookingId) {
      selectedBookingId = bookingId;
      selectedParkingId = null;
      // Load available parking areas
      fetch('/admin/operations/available-locations?type=drop')
        .then(response => response.json())
        .then(areas => {
          const parkingList = document.getElementById('parking-list');
          parkingList.innerHTML = '';
          if (areas.length === 0) {
            parkingList.innerHTML = '<div class="text-gray-500 text-center py-4">No available parking areas</div>';
            return;
          }
          areas.forEach(area => {
            const areaOption = document.createElement('div');
            areaOption.className = 'p-3 border border-gray-200 rounded cursor-pointer hover:bg-green-50';
            areaOption.innerHTML = `
              <div class="font-medium">${area.name}</div>
              <div class="text-sm text-gray-500">${area.code || ''}</div>
            `;
            areaOption.onclick = () => selectParkingArea(area.id, areaOption);
            parkingList.appendChild(areaOption);
          });
          document.getElementById('parking-modal').classList.remove('hidden');
        })
        .catch(error => {
          console.error('Error loading parking areas:', error);
          alert('Error loading available parking areas');
        });
    }
    function selectBay(bayId, element) {
      selectedBayId = bayId;
      // Remove previous selections
      document.querySelectorAll('#bay-list > div').forEach(div => {
        div.classList.remove('bg-blue-100', 'border-blue-500');
        div.classList.add('border-gray-200');
      });
      // Highlight selected
      element.classList.add('bg-blue-100', 'border-blue-500');
      element.classList.remove('border-gray-200');
    }
    function selectParkingArea(areaId, element) {
      selectedParkingId = areaId;
      // Remove previous selections
      document.querySelectorAll('#parking-list > div').forEach(div => {
        div.classList.remove('bg-green-100', 'border-green-500');
        div.classList.add('border-gray-200');
      });
      // Highlight selected
      element.classList.add('bg-green-100', 'border-green-500');
      element.classList.remove('border-gray-200');
    }
    function confirmBaySelection() {
      if (!selectedBayId) {
        alert('Please select a bay');
        return;
      }
      fetch(`/admin/operations/${selectedBookingId}/shunt-to-bay`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          bay_id: selectedBayId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          window.location.reload();
        } else {
          alert(data.error || 'Error assigning bay');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error assigning bay');
      });
      closeBayModal();
    }
    function confirmParkingSelection() {
      if (!selectedParkingId) {
        alert('Please select a parking area');
        return;
      }
      fetch(`/admin/operations/${selectedBookingId}/assign-parking-area`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          location_id: selectedParkingId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          window.location.reload();
        } else {
          alert(data.error || 'Error assigning parking area');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error assigning parking area');
      });
      closeParkingModal();
    }
    function closeBayModal() {
      document.getElementById('bay-modal').classList.add('hidden');
      selectedBookingId = null;
      selectedBayId = null;
    }
    function closeParkingModal() {
      document.getElementById('parking-modal').classList.add('hidden');
      selectedBookingId = null;
      selectedParkingId = null;
    }
    // Open priority settings in new window/tab
    function openPrioritySettings() {
      const currentUrl = new URL(window.location);
      const depotId = currentUrl.searchParams.get('depot_id');
      let settingsUrl = '{{ route("admin.operations.priority-settings") }}';
      if (depotId) {
        settingsUrl += '?depot_id=' + depotId;
      }
      window.open(settingsUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    }
    // Quick priority boost function
    async function quickPriorityBoost(bookingId, boost) {
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/priority`, {
          method: 'PUT',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            manual_priority_boost: parseInt(boost),
            priority_notes: boost == 0 ? null : `Quick boost: ${boost > 0 ? '+' : ''}${boost} points`
          })
        });
        const data = await response.json();
        if (data.success) {
          // Show success message briefly
          showToast('Priority updated!', 'success');
          // Reload page after a short delay to show updated queue order
          setTimeout(() => window.location.reload(), 1000);
        } else {
          showToast(data.error || 'Update failed', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Set tipping type for a booking
    async function setTippingType(bookingId, type) {
      if (!type) return;
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/tipping-type`, {
          method: 'PUT',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            tipping_type: type
          })
        });
        const data = await response.json();
        if (data.success) {
          showToast(`Tipping type set to ${type.replace('_', ' ')}!`, 'success');
          setTimeout(() => window.location.reload(), 1000);
        } else {
          showToast(data.error || 'Update failed', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Start tipping process for Drop workflow
    async function startTipping(bookingId) {
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/start-tipping`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
          }
        });
        const data = await response.json();
        if (data.success) {
          showToast('Tipping started!', 'success');
          setTimeout(() => window.location.reload(), 1000);
        } else {
          showToast(data.error || 'Failed to start tipping', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Show toast notification
    function showToast(message, type) {
      const toast = document.createElement('div');
      toast.className = `fixed top-4 right-4 px-4 py-2 rounded text-white text-sm z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
      }`;
      toast.textContent = message;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.remove();
      }, 2000);
    }
    // Auto-refresh every 30 seconds
    setTimeout(() => {
      window.location.reload();
    }, 30000);
    // Column toggle functionality with localStorage persistence
    document.addEventListener('DOMContentLoaded', function() {
      // Storage keys for filter preferences
      const STORAGE_KEYS = {
        bayStatus: 'queue_show_bay_status',
        priorityDetails: 'queue_show_priority_details', 
        collectionUrgency: 'queue_show_collection_urgency',
        inUseOnly: 'queue_bay_in_use_only'
      };
      // Load saved preferences or use defaults
      function loadFilterPreferences() {
        const preferences = {
          bayStatus: localStorage.getItem(STORAGE_KEYS.bayStatus) !== 'false', // Default: true
          priorityDetails: localStorage.getItem(STORAGE_KEYS.priorityDetails) !== 'false', // Default: true
          collectionUrgency: localStorage.getItem(STORAGE_KEYS.collectionUrgency) !== 'false', // Default: true
          inUseOnly: localStorage.getItem(STORAGE_KEYS.inUseOnly) === 'true' // Default: false
        };
        return preferences;
      }
      // Save preference to localStorage
      function savePreference(key, value) {
        localStorage.setItem(STORAGE_KEYS[key], value.toString());
      }
      // Apply preferences to UI elements
      function applyPreferences(preferences) {
        // Bay Status
        const bayToggle = document.getElementById('toggle-bay-status');
        const baySection = document.querySelector('.bay-status-section');
        if (bayToggle && baySection) {
          bayToggle.checked = preferences.bayStatus;
          baySection.style.display = preferences.bayStatus ? 'block' : 'none';
        }
        // Priority Details
        const priorityToggle = document.getElementById('toggle-priority-details');
        const priorityColumns = document.querySelectorAll('.priority-details');
        if (priorityToggle) {
          priorityToggle.checked = preferences.priorityDetails;
          priorityColumns.forEach(col => {
            col.style.display = preferences.priorityDetails ? 'table-cell' : 'none';
          });
        }
        // Collection Urgency
        const urgencyToggle = document.getElementById('toggle-collection-urgency');
        const urgencySection = document.querySelector('.collection-urgency-section');
        if (urgencyToggle && urgencySection) {
          urgencyToggle.checked = preferences.collectionUrgency;
          urgencySection.style.display = preferences.collectionUrgency ? 'block' : 'none';
        }
        // In Use Only Filter
        const inUseOnlyFilter = document.getElementById('filter-in-use-only');
        if (inUseOnlyFilter) {
          inUseOnlyFilter.checked = preferences.inUseOnly;
          const allBayCards = document.querySelectorAll('.bay-card');
          const availableBayCards = document.querySelectorAll('.bay-available');
          if (preferences.inUseOnly) {
            // Hide available bays, show only in-use bays
            availableBayCards.forEach(card => {
              card.style.display = 'none';
            });
          } else {
            // Show all bays
            allBayCards.forEach(card => {
              card.style.display = 'block';
            });
          }
        }
      }
      // Initialize with saved preferences
      const preferences = loadFilterPreferences();
      applyPreferences(preferences);
      // Bay Status Toggle
      const bayToggle = document.getElementById('toggle-bay-status');
      bayToggle?.addEventListener('change', function() {
        const baySection = document.querySelector('.bay-status-section');
        if (baySection) {
          baySection.style.display = this.checked ? 'block' : 'none';
          savePreference('bayStatus', this.checked);
        }
      });
      // In Use Only Filter for Bays
      const inUseOnlyFilter = document.getElementById('filter-in-use-only');
      inUseOnlyFilter?.addEventListener('change', function() {
        const allBayCards = document.querySelectorAll('.bay-card');
        const availableBayCards = document.querySelectorAll('.bay-available');
        if (this.checked) {
          // Hide available bays, show only in-use bays
          availableBayCards.forEach(card => {
            card.style.display = 'none';
          });
        } else {
          // Show all bays
          allBayCards.forEach(card => {
            card.style.display = 'block';
          });
        }
        savePreference('inUseOnly', this.checked);
      });
      // Priority Details Toggle
      const priorityToggle = document.getElementById('toggle-priority-details');
      priorityToggle?.addEventListener('change', function() {
        const priorityColumns = document.querySelectorAll('.priority-details');
        priorityColumns.forEach(col => {
          col.style.display = this.checked ? 'table-cell' : 'none';
        });
        savePreference('priorityDetails', this.checked);
      });
      // Collection Urgency Toggle
      const urgencyToggle = document.getElementById('toggle-collection-urgency');
      urgencyToggle?.addEventListener('change', function() {
        const urgencySection = document.querySelector('.collection-urgency-section');
        if (urgencySection) {
          urgencySection.style.display = this.checked ? 'block' : 'none';
          savePreference('collectionUrgency', this.checked);
        }
      });
      // Make table more responsive with better scroll
      const tableContainer = document.querySelector('.overflow-x-auto');
      if (tableContainer) {
        tableContainer.style.maxHeight = '70vh';
        tableContainer.style.overflowY = 'auto';
      }
      // Show feedback when preferences are loaded
      if (Object.values(preferences).some(val => !val)) {
        console.log('Queue filters restored from previous session');
      }
      // Auto-refresh every 30 seconds to keep data current
      setInterval(() => {
        window.location.reload();
      }, 30000);
    });
  </script>
</x-app-layout>