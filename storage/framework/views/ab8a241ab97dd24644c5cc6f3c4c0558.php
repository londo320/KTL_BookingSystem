<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
  <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">🎯 Site Operations Control</h2>
        <p class="text-sm text-gray-600 mt-1">Main operational dashboard - manage all site activities</p>
      </div>
      <div class="flex items-center space-x-4">
        
        <?php if($allDepots->count() > 1): ?>
        <form method="GET" class="flex items-center space-x-2">
          <label for="depot_id" class="text-sm font-medium text-gray-700">View:</label>
          <select name="depot_id" onchange="this.form.submit()" class="px-3 py-1 border border-gray-300 rounded-md text-sm">
            <option value="" <?php echo e(!$currentDepotId ? 'selected' : ''); ?>>All Depots (View Only)</option>
            <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
                <?php echo e($depot->name); ?> <?php echo e($depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)'); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </form>
        <?php elseif($allDepots->count() == 1): ?>
        <div class="text-sm">
          <span class="font-medium text-gray-700">Depot:</span>
          <span class="font-mono bg-blue-100 text-blue-800 px-2 py-1 rounded"><?php echo e($allDepots->first()->name); ?></span>
        </div>
        <?php endif; ?>
        
        
        <div class="text-sm text-gray-600">
          <?php echo e(now()->format('D, M j Y - H:i')); ?> | <span class="font-mono"><?php echo e(now()->format('H:i:s')); ?></span>
        </div>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-full mx-auto px-4">
    
    <!-- Workflow Status Overview -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold">📊 Site Overview</h3>
        <div class="text-sm">
          <?php if(!$currentDepotId): ?>
            <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
          <?php else: ?>
            <?php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); ?>
            <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600"><?php echo e($currentDepot?->name ?? 'Unknown Depot'); ?></span></span>
            <?php if($currentDepotId == $defaultDepotId): ?>
              <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
            <?php else: ?>
              <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="flex flex-row gap-2 overflow-x-auto">
        <div class="text-center p-1 bg-blue-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-blue-600"><?php echo e($stats['on_site']); ?></div>
          <div class="text-xs text-blue-600">🚛 On Site</div>
        </div>
        <div class="text-center p-1 bg-yellow-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-yellow-600"><?php echo e($stats['in_drop_zone']); ?></div>
          <div class="text-xs text-yellow-600">📍 In Drop Zone</div>
        </div>
        <div class="text-center p-1 bg-orange-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-orange-600"><?php echo e($stats['at_bay']); ?></div>
          <div class="text-xs text-orange-600">🚛 At Bay</div>
        </div>
        <div class="text-center p-1 bg-red-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-red-600"><?php echo e($stats['tipping']); ?></div>
          <div class="text-xs text-red-600">⚡ Tipping</div>
        </div>
        <div class="text-center p-1 bg-green-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-green-600"><?php echo e($stats['empty']); ?></div>
          <div class="text-xs text-green-600">✅ Empty</div>
        </div>
        <div class="text-center p-1 bg-purple-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-purple-600"><?php echo e($stats['awaiting_collection']); ?></div>
          <div class="text-xs text-purple-600">🔄 Awaiting Collection</div>
        </div>
        <div class="text-center p-1 bg-gray-100 rounded flex-1 min-w-0">
          <div class="text-sm font-bold text-gray-600"><?php echo e($stats['being_collected']); ?></div>
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
            <?php $__empty_1 = true; $__currentLoopData = $activeMovements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
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
            ?>
            
            <tr class="<?php echo e($config['row']); ?>">
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-medium">
                  <a href="<?php echo e(route('admin.bookings.show', $booking)); ?>" class="text-blue-600 hover:text-blue-800 hover:underline">
                    <?php echo e($booking->booking_reference ?: '#' . $booking->id); ?>

                  </a>
                </div>
                <div class="text-xs text-gray-500"><?php echo e($customer->name ?? 'Unknown Customer'); ?></div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"><?php echo e($customer->name ?? 'Unknown Customer'); ?></div>
                <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                  <div class="text-xs text-blue-600">📦 <?php echo e($booking->poNumbers->pluck('po_number')->implode(', ')); ?></div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="font-mono text-sm"><?php echo e($booking->vehicle_registration ?? 'N/A'); ?></div>
                <div class="font-mono text-xs text-gray-500"><?php echo e($booking->container_number ?? 'N/A'); ?></div>
                <div class="text-xs <?php echo e($isTrailerDropped ? 'text-red-600' : 'text-green-600'); ?>">
                  <?php echo e($isTrailerDropped ? '🔄 Dropped' : '🔗 Attached'); ?> • <?php echo e($isEmpty ? '📭 Empty' : '📦 Loaded'); ?>

                </div>
                <?php if($movement->unit_departed_at): ?>
                  <div class="text-xs text-orange-600 mt-1">📤 Unit Departed: <?php echo e($movement->unit_departed_at->format('H:i')); ?></div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full <?php echo e($config['color']); ?>">
                  <?php echo e($config['icon']); ?> <?php echo e($config['label']); ?>

                </span>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900"><?php echo e($location); ?></div>
                <div class="text-xs text-gray-400"><?php echo e($locationDetail); ?></div>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <div class="text-sm font-mono"><?php echo e($arrivalTime->format('H:i')); ?></div>
                <div class="text-xs text-gray-500"><?php echo e(round($timeOnSite/60, 1)); ?>h ago</div>
                <?php if($movement->moved_to_location_at): ?>
                  <div class="text-xs text-yellow-600">In zone: <?php echo e(round($movement->moved_to_location_at->diffInMinutes(now())/60, 1)); ?>h</div>
                <?php endif; ?>
                <?php if($movement->unloading_started_at): ?>
                  <div class="text-xs text-red-600">Tipping: <?php echo e(round($movement->unloading_started_at->diffInMinutes(now()))); ?>m</div>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4 whitespace-nowrap">
                <?php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; ?>
                
                <?php if($movement->current_status === 'arrived'): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600">
                      📍 Assign Drop Zone
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      📍 Assign Drop Zone
                    </span>
                  <?php endif; ?>
                <?php elseif(in_array($movement->current_status, ['in_location']) && !$movement->unloading_started_at): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-orange-500 text-white text-sm rounded hover:bg-orange-600">
                      🚛 Shunt to Bay
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🚛 Shunt to Bay
                    </span>
                  <?php endif; ?>
                <?php elseif($movement->current_status === 'trailer_dropped' && !$movement->unloading_started_at): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-orange-500 text-white text-sm rounded hover:bg-orange-600">
                      🚛 Shunt to Bay
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🚛 Shunt to Bay
                    </span>
                  <?php endif; ?>
                <?php elseif($movement->current_status === 'trailer_dropped' && $movement->unloading_started_at): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                      ✅ Complete Tipping
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      ✅ Complete Tipping
                    </span>
                  <?php endif; ?>
                <?php elseif($movement->current_status === 'unloading'): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-green-500 text-white text-sm rounded hover:bg-green-600">
                      ✅ Complete Tipping
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      ✅ Complete Tipping
                    </span>
                  <?php endif; ?>
                <?php elseif($movement->current_status === 'empty'): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-purple-500 text-white text-sm rounded hover:bg-purple-600">
                      🔄 Move to Collection Zone
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🔄 Move to Collection Zone
                    </span>
                  <?php endif; ?>
                <?php elseif($movement->current_status === 'trailer_collected'): ?>
                  <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                       class="inline-block px-3 py-1 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">
                      🚚 Record Collection
                    </a>
                  <?php else: ?>
                    <span class="inline-block px-3 py-1 bg-gray-300 text-gray-500 text-sm rounded cursor-not-allowed" 
                          title="Actions only available for your default depot">
                      🚚 Record Collection
                    </span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-xs text-gray-500">No action available</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                <div class="text-4xl mb-2">🎉</div>
                <div>No active operations - all trailers processed!</div>
              </td>
            </tr>
            <?php endif; ?>
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/operations-control.blade.php ENDPATH**/ ?>