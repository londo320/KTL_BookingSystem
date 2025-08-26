<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">🎯 Site Operations Control</h2>
        <p class="text-sm text-gray-600 mt-1">Main operational dashboard - manage all site activities</p>
      </div>
      <div class="flex items-center space-x-4">
        {{-- Depot Filter --}}
        @if($allDepots->count() > 1)
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
        @elseif($allDepots->count() == 1)
        <div class="text-sm">
          <span class="font-medium text-gray-700">Depot:</span>
          <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $allDepots->first()->name }}</span>
        </div>
        @endif
        {{-- Time Display --}}
        <div class="text-sm text-gray-600">
          {{ now()->format('D, M j Y - H:i') }} | <span class="font-mono">{{ now()->format('H:i:s') }}</span>
        </div>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-full mx-auto px-4">
    <!-- Workflow Status Overview -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">📊 Site Overview</h3>
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
      <div class="flex flex-row gap-2 overflow-x-auto">
        <div class="text-center p-1 bg-blue-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-blue-600">{{ $stats['on_site'] }}</div>
          <div class="text-xs text-blue-600">🚛 On Site</div>
        </div>
        <div class="text-center p-1 bg-yellow-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-yellow-600">{{ $stats['in_drop_zone'] }}</div>
          <div class="text-xs text-yellow-600">📍 In Drop Zone</div>
        </div>
        <div class="text-center p-1 bg-orange-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-orange-600">{{ $stats['at_bay'] }}</div>
          <div class="text-xs text-orange-600">🚛 At Bay</div>
        </div>
        <div class="text-center p-1 bg-red-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-red-600">{{ $stats['tipping'] }}</div>
          <div class="text-xs text-red-600">⚡ Tipping</div>
        </div>
        <div class="text-center p-1 bg-green-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-green-600">{{ $stats['empty'] }}</div>
          <div class="text-xs text-green-600">✅ Empty</div>
        </div>
        <div class="text-center p-1 bg-purple-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-purple-600">{{ $stats['awaiting_collection'] }}</div>
          <div class="text-xs text-purple-600">🔄 Awaiting Collection</div>
        </div>
        <div class="text-center p-1 bg-gray-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-gray-600">{{ $stats['being_collected'] }}</div>
          <div class="text-xs text-gray-600">🚚 Being Collected</div>
        </div>
      </div>
    </div>
    <!-- Operational Workflow Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-medium text-gray-900">🎮 Active Operations</h3>
        <p class="text-sm text-gray-600 mt-1">Click actions to progress through workflow • All times automatically recorded</p>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle Info</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Location</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Timing</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Next Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse($activeMovements as $movement)
            @php
              $booking = $movement->booking;
              $customer = $booking->customer;
              $slot = $booking->slot;
              // Calculate timing info
              $arrivalTime = $movement->arrived_at ?? $movement->created_at;
              $timeOnSite = $arrivalTime ? $arrivalTime->diffInMinutes(now()) : 0;
              // Determine status styling and labels
              $statusConfig = [
                'arrived' => ['icon' => '🚛', 'label' => 'Just Arrived', 'color' => 'bg-blue-100 text-blue-800', 'row' => 'hover:bg-blue-50'],
                'in_waiting' => ['icon' => '⏳', 'label' => 'Waiting', 'color' => 'bg-yellow-100 text-yellow-800', 'row' => 'hover:bg-yellow-50'],
                'in_location' => ['icon' => '📍', 'label' => 'In Drop Zone', 'color' => 'bg-yellow-100 text-yellow-800', 'row' => 'hover:bg-yellow-50'],
                'trailer_dropped' => ['icon' => '🔄', 'label' => 'Trailer Dropped', 'color' => 'bg-orange-100 text-orange-800', 'row' => 'hover:bg-orange-50'],
                'at_bay' => ['icon' => '🚛', 'label' => 'At Bay', 'color' => 'bg-orange-100 text-orange-800', 'row' => 'hover:bg-orange-50'],
                'unloading' => ['icon' => '⚡', 'label' => 'Tipping Active', 'color' => 'bg-red-100 text-red-800', 'row' => 'hover:bg-red-50'],
                'empty' => ['icon' => '✅', 'label' => 'Tipped - Empty', 'color' => 'bg-green-100 text-green-800', 'row' => 'hover:bg-green-50'],
                'trailer_collected' => ['icon' => '🚚', 'label' => 'Being Collected', 'color' => 'bg-purple-100 text-purple-800', 'row' => 'hover:bg-purple-50']
              ];
              $config = $statusConfig[$movement->current_status] ?? ['icon' => '❓', 'label' => ucwords(str_replace('_', ' ', $movement->current_status)), 'color' => 'bg-gray-100 text-gray-800', 'row' => 'hover:bg-gray-50'];
              // Determine current location
              $location = 'Unknown';
              $locationDetail = '';
              if ($movement->tippingBay) {
                $location = '🏗️ ' . $movement->tippingBay->name;
                $locationDetail = $movement->current_status === 'unloading' ? 'Currently tipping' : 'At bay';
              } elseif ($movement->tippingLocation) {
                $location = '📍 ' . $movement->tippingLocation->name;
                $locationDetail = 'In drop zone';
              } elseif ($movement->current_status === 'arrived') {
                $location = 'Gate Entry';
                $locationDetail = 'No location assigned';
              }
              // Vehicle status
              $isTrailerDropped = in_array($movement->current_status, ['trailer_dropped', 'empty', 'trailer_collected']);
              $isEmpty = in_array($movement->current_status, ['empty', 'trailer_collected']);
            @endphp
            <tr class="{{ $config['row'] }}">
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-medium">
                  <a href="{{ route('app.bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-800 hover:underline">
                    {{ $booking->booking_reference ?: '#' . $booking->id }}
                  </a>
                </div>
                <div class="text-xs text-gray-500">{{ $customer->name ?? 'Unknown Customer' }}</div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $customer->name ?? 'Unknown Customer' }}</div>
                @if($booking->poNumbers && $booking->poNumbers->count() > 0)
                  <div class="text-xs text-blue-600">📦 {{ $booking->poNumbers->pluck('po_number')->implode(', ') }}</div>
                @endif
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-mono text-sm">{{ $booking->vehicle_registration ?? 'N/A' }}</div>
                <div class="font-mono text-xs text-gray-500">{{ $booking->container_number ?? 'N/A' }}</div>
                <div class="text-xs {{ $isTrailerDropped ? 'text-red-600' : 'text-green-600' }}">
                  {{ $isTrailerDropped ? '🔄 Dropped' : '🔗 Attached' }} • {{ $isEmpty ? '📭 Empty' : '📦 Loaded' }}
                </div>
                @if($movement->unit_departed_at)
                  <div class="text-xs text-orange-600 mt-1">📤 Unit Departed: {{ $movement->unit_departed_at->format('H:i') }}</div>
                @endif
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full {{ $config['color'] }}">
                  {{ $config['icon'] }} {{ $config['label'] }}
                </span>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">{{ $location }}</div>
                <div class="text-xs text-gray-400">{{ $locationDetail }}</div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm font-mono">{{ $arrivalTime->format('H:i') }}</div>
                <div class="text-xs text-gray-500">{{ round($timeOnSite/60, 1) }}h ago</div>
                @if($movement->moved_to_location_at)
                  <div class="text-xs text-yellow-600">In zone: {{ round($movement->moved_to_location_at->diffInMinutes(now())/60, 1) }}h</div>
                @endif
                @if($movement->unloading_started_at)
                  <div class="text-xs text-red-600">Tipping: {{ round($movement->unloading_started_at->diffInMinutes(now())) }}m</div>
                @endif
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                @php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; @endphp
                @if($movement->current_status === 'arrived')
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600">
                      📍 Assign Drop Zone
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      📍 Assign Drop Zone
                    </span>
                  @endif
                @elseif(in_array($movement->current_status, ['in_location']) && !$movement->unloading_started_at)
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-orange-500 text-white text-sm rounded hover:bg-orange-600">
                      🚛 Shunt to Bay
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🚛 Shunt to Bay
                    </span>
                  @endif
                @elseif($movement->current_status === 'trailer_dropped' && !$movement->unloading_started_at)
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-orange-500 text-white text-sm rounded hover:bg-orange-600">
                      🚛 Shunt to Bay
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🚛 Shunt to Bay
                    </span>
                  @endif
                @elseif($movement->current_status === 'trailer_dropped' && $movement->unloading_started_at)
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                      ✅ Complete Tipping
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      ✅ Complete Tipping
                    </span>
                  @endif
                @elseif($movement->current_status === 'unloading')
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                      ✅ Complete Tipping
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      ✅ Complete Tipping
                    </span>
                  @endif
                @elseif($movement->current_status === 'empty')
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600">
                      🔄 Move to Collection Zone
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🔄 Move to Collection Zone
                    </span>
                  @endif
                @elseif($movement->current_status === 'trailer_collected')
                  @if($canTakeAction)
                    <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                       class="inline-block px-3 py-1 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">
                      🚚 Record Collection
                    </a>
                  @else
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🚚 Record Collection
                    </span>
                  @endif
                @else
                  <span class="text-xs text-gray-500">No action available</span>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                <div class="text-4xl mb-2">🎉</div>
                <div>No active operations - all trailers processed!</div>
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <!-- Quick Actions Panel -->
    <div class="mt-6 bg-white rounded-lg shadow p-4">
      <h3 class="text-lg font-semibold mb-4">⚡ Quick Actions</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <button class="p-3 bg-blue-100 hover:bg-blue-200 rounded-lg text-center">
          <div class="text-2xl mb-1">📋</div>
          <div class="text-sm font-medium">New Arrival</div>
        </button>
        <button class="p-3 bg-yellow-100 hover:bg-yellow-200 rounded-lg text-center">
          <div class="text-2xl mb-1">🗺️</div>
          <div class="text-sm font-medium">Site Map</div>
        </button>
        <button class="p-3 bg-green-100 hover:bg-green-200 rounded-lg text-center">
          <div class="text-2xl mb-1">📊</div>
          <div class="text-sm font-medium">Reports</div>
        </button>
        <button class="p-3 bg-purple-100 hover:bg-purple-200 rounded-lg text-center">
          <div class="text-2xl mb-1">⚙️</div>
          <div class="text-sm font-medium">Settings</div>
        </button>
      </div>
    </div>
  </div>
  <!-- Action Modal (hidden by default) -->
  <div id="actionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Action Required</h3>
        <div id="modalContent">
          <!-- Dynamic content will be loaded here -->
        </div>
        <div class="items-center px-4 py-3">
          <button id="modalCancel" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
  <script>
    let selectedBookingId = null;
    let selectedLocationId = null;
    let selectedBayId = null;
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
      }, 3000);
    }
    // Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
      const modal = document.getElementById('actionModal');
      const modalTitle = document.getElementById('modalTitle');
      const modalContent = document.getElementById('modalContent');
      const modalCancel = document.getElementById('modalCancel');
      // Show modal function
      window.showModal = function(title, content) {
        modalTitle.textContent = title;
        modalContent.innerHTML = content;
        modal.classList.remove('hidden');
      }
      // Hide modal function
      window.hideModal = function() {
        modal.classList.add('hidden');
        selectedBookingId = null;
        selectedLocationId = null;
        selectedBayId = null;
      }
      // Cancel button
      modalCancel.addEventListener('click', hideModal);
      // Click outside modal to close
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          hideModal();
        }
      });
    });
    // Assign Drop Zone
    async function assignDropZone(bookingId) {
      selectedBookingId = bookingId;
      try {
        const response = await fetch('/admin/operations/available-locations?type=drop');
        const locations = await response.json();
        if (locations.length === 0) {
          showToast('No available drop zones', 'error');
          return;
        }
        let content = '<div class="space-y-2">';
        locations.forEach(location => {
          content += `
            <div class="p-3 border border-gray-200 rounded cursor-pointer hover:bg-yellow-50" 
                 onclick="selectLocation(${location.id}, this)">
              <div class="font-medium">${location.name}</div>
              <div class="text-sm text-gray-500">${location.code || ''}</div>
            </div>
          `;
        });
        content += `
          <div class="mt-4 flex justify-end space-x-2">
            <button onclick="hideModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
            <button onclick="confirmDropZoneAssignment()" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">Assign Zone</button>
          </div>
        `;
        showModal('Select Drop Zone', content);
      } catch (error) {
        showToast('Error loading drop zones', 'error');
      }
    }
    function selectLocation(locationId, element) {
      selectedLocationId = locationId;
      // Remove previous selections
      document.querySelectorAll('#modalContent .border-yellow-500').forEach(div => {
        div.classList.remove('bg-yellow-100', 'border-yellow-500');
        div.classList.add('border-gray-200');
      });
      // Highlight selected
      element.classList.add('bg-yellow-100', 'border-yellow-500');
      element.classList.remove('border-gray-200');
    }
    async function confirmDropZoneAssignment() {
      if (!selectedLocationId) {
        showToast('Please select a drop zone', 'error');
        return;
      }
      try {
        const response = await fetch(`/admin/operations/${selectedBookingId}/assign-drop-zone`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            location_id: selectedLocationId
          })
        });
        const data = await response.json();
        if (data.success) {
          showToast('Drop zone assigned successfully!', 'success');
          hideModal();
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showToast(data.error || 'Failed to assign drop zone', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Shunt to Bay
    async function shuntToBay(bookingId) {
      selectedBookingId = bookingId;
      try {
        const response = await fetch('/admin/operations/available-bays');
        const bays = await response.json();
        if (bays.length === 0) {
          showToast('No available bays', 'error');
          return;
        }
        let content = '<div class="space-y-2">';
        bays.forEach(bay => {
          content += `
            <div class="p-3 border border-gray-200 rounded cursor-pointer hover:bg-orange-50" 
                 onclick="selectBay(${bay.id}, this)">
              <div class="font-medium">${bay.name}</div>
              <div class="text-sm text-gray-500">${bay.code || ''}</div>
            </div>
          `;
        });
        content += `
          <div class="mt-4 flex justify-end space-x-2">
            <button onclick="hideModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
            <button onclick="confirmBayAssignment()" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">Assign Bay</button>
          </div>
        `;
        showModal('Select Tipping Bay', content);
      } catch (error) {
        showToast('Error loading available bays', 'error');
      }
    }
    function selectBay(bayId, element) {
      selectedBayId = bayId;
      // Remove previous selections
      document.querySelectorAll('#modalContent .border-orange-500').forEach(div => {
        div.classList.remove('bg-orange-100', 'border-orange-500');
        div.classList.add('border-gray-200');
      });
      // Highlight selected
      element.classList.add('bg-orange-100', 'border-orange-500');
      element.classList.remove('border-gray-200');
    }
    async function confirmBayAssignment() {
      if (!selectedBayId) {
        showToast('Please select a bay', 'error');
        return;
      }
      try {
        const response = await fetch(`/admin/operations/${selectedBookingId}/shunt-to-bay`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({
            bay_id: selectedBayId
          })
        });
        const data = await response.json();
        if (data.success) {
          showToast('Trailer assigned to bay successfully!', 'success');
          hideModal();
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showToast(data.error || 'Failed to assign bay', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Complete Tipping
    async function completeTipping(bookingId) {
      if (!confirm('Are you sure you want to mark tipping as complete?')) {
        return;
      }
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/complete-tipping`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });
        const data = await response.json();
        if (data.success) {
          showToast('Tipping completed successfully!', 'success');
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showToast(data.error || 'Failed to complete tipping', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Move to Collection Zone
    async function moveToCollection(bookingId) {
      if (!confirm('Move empty trailer to collection zone?')) {
        return;
      }
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/move-to-collection`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });
        const data = await response.json();
        if (data.success) {
          showToast('Trailer moved to collection zone!', 'success');
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showToast(data.error || 'Failed to move trailer', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Record Collection
    async function recordCollection(bookingId) {
      if (!confirm('Record that this trailer has been collected?')) {
        return;
      }
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/record-collection`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          }
        });
        const data = await response.json();
        if (data.success) {
          showToast('Collection recorded successfully!', 'success');
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showToast(data.error || 'Failed to record collection', 'error');
        }
      } catch (error) {
        showToast('Network error occurred', 'error');
      }
    }
    // Auto-refresh every 30 seconds
    setTimeout(() => {
      window.location.reload();
    }, 30000);
  </script>
</x-warehouse-layout>