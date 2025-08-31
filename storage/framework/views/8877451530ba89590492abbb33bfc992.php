<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">🚛 Trailer Operations Dashboard</h2>
        <p class="text-sm text-gray-600 mt-1">Complete operational view with workflow priorities and timing</p>
      </div>
      <div class="flex space-x-3">
        <a href="<?php echo e(route('app.trailer-location-report')); ?>"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          📊 Location Report
        </a>
        <a href="<?php echo e(route('app.tipping-workflow.dashboard')); ?>"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          🔄 Tipping Dashboard
        </a>
        <a href="<?php echo e(route('app.bookings.index')); ?>"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          ← Back to Bookings
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
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
    <?php
      $stats = [
        'total_on_site' => $movementsOnSite->count(),
        'currently_tipping' => $movementsOnSite->where('current_status', 'unloading')->count(),
        'ready_to_tip' => $movementsOnSite->where('current_status', 'at_bay')->count(),
        'need_clearing' => $movementsOnSite->where('current_status', 'empty')->count(),
        'loaded_attached' => $movementsOnSite->where('calculated_data.is_loaded', true)->where('calculated_data.is_attached', true)->count(),
        'empty_trailers' => $movementsOnSite->where('calculated_data.is_loaded', false)->count(),
      ];
    ?>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-gray-600"><?php echo e($stats['total_on_site']); ?></div>
        <div class="text-sm text-gray-600">Total On Site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-red-600"><?php echo e($stats['currently_tipping']); ?></div>
        <div class="text-sm text-gray-600">Currently Tipping</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-orange-600"><?php echo e($stats['ready_to_tip']); ?></div>
        <div class="text-sm text-gray-600">Ready to Start</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-yellow-600"><?php echo e($stats['need_clearing']); ?></div>
        <div class="text-sm text-gray-600">Need Clearing</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-blue-600"><?php echo e($stats['loaded_attached']); ?></div>
        <div class="text-sm text-gray-600">Loaded & Attached</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-green-600"><?php echo e($stats['empty_trailers']); ?></div>
        <div class="text-sm text-gray-600">Empty Trailers</div>
      </div>
    </div>
    <!-- Main Operations Table -->
    <?php if($movementsOnSite->count() > 0): ?>
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
            <?php $__currentLoopData = $movementsOnSite; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
              $booking = $movement->booking;
              $factoryBooking = $movement->factoryBooking;
              $isFactory = $factoryBooking !== null;
              $activeBooking = $isFactory ? $factoryBooking : $booking;
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
                'in_waiting' => '⏳ In Parking Area',
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
            ?>
            <tr class="hover:bg-gray-50 <?php echo e($data['workflow_priority'] <= 2 ? 'bg-red-50' : ''); ?>">
              <!-- Priority -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="w-8 h-8 rounded-full <?php echo e($priorityColors[$data['workflow_priority']]); ?> flex items-center justify-center font-bold text-sm">
                  <?php echo e($data['workflow_priority']); ?>

                </span>
              </td>
              <!-- Booking -->
              <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <?php if($isFactory): ?>
                  <a href="<?php echo e(route('app.factory-bookings.show', $activeBooking)); ?>" class="hover:underline">
                    <?php echo e($activeBooking->reference); ?>

                  </a>
                  <br><span class="text-xs text-orange-600">📍 Factory</span>
                <?php else: ?>
                  <a href="<?php echo e(route('app.bookings.show', $activeBooking)); ?>" class="hover:underline">
                    <?php echo e($activeBooking->booking_reference); ?>

                  </a>
                <?php endif; ?>
                <?php if($activeBooking->booked_at ?? $activeBooking->arrived_at): ?>
                  <br><span class="text-xs text-gray-500"><?php echo e(($activeBooking->booked_at ?? $activeBooking->arrived_at)->format('M j H:i')); ?></span>
                <?php endif; ?>
              </td>
              <!-- Customer -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                <?php echo e($activeBooking?->customer?->name ?? 'Unknown'); ?>

                <?php if($activeBooking?->poNumbers && $activeBooking->poNumbers->count() > 0): ?>
                  <br><span class="text-xs text-blue-600">📦 <?php echo e($activeBooking->poNumbers->pluck('po_number')->join(', ')); ?></span>
                <?php endif; ?>
              </td>
              <!-- Vehicle/Container -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php echo e($activeBooking?->vehicle_registration ?? 'Not specified'); ?>

                <?php if($activeBooking?->container_number): ?>
                  <br><span class="text-xs font-mono"><?php echo e($activeBooking->container_number); ?></span>
                <?php endif; ?>
                <?php if($isFactory && $activeBooking?->trailer_registration): ?>
                  <br><span class="text-xs text-gray-500">Trailer: <?php echo e($activeBooking->trailer_registration); ?></span>
                <?php endif; ?>
              </td>
              <!-- Status -->
              <td class="px-4 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full <?php echo e($statusColors[$movement->current_status] ?? 'bg-gray-100 text-gray-800'); ?>">
                  <?php echo e($statusDisplay); ?>

                </span>
              </td>
              <!-- Load State -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <div class="flex flex-col space-y-1">
                  <span class="px-2 py-1 text-xs rounded-full <?php echo e($data['is_loaded'] ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'); ?>">
                    <?php echo e($data['is_loaded'] ? '📦 Loaded' : '📭 Empty'); ?>

                  </span>
                  <span class="px-2 py-1 text-xs rounded-full <?php echo e($data['is_attached'] ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800'); ?>">
                    <?php echo e($data['is_attached'] ? '🔗 Attached' : '🔄 Detached'); ?>

                  </span>
                </div>
              </td>
              <!-- Location -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->tippingBay): ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    🏗️ <?php echo e($movement->tippingBay->name); ?>

                  </span>
                <?php elseif($movement->tippingLocation): ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                    📍 <?php echo e($movement->tippingLocation->name); ?>

                  </span>
                <?php else: ?>
                  <span class="text-gray-400">No location</span>
                <?php endif; ?>
              </td>
              <!-- Arrived -->
              <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($data['arrival_time']): ?>
                  <div class="font-medium"><?php echo e($data['arrival_time']->format('M j H:i')); ?></div>
                  <div class="text-xs text-gray-400"><?php echo e($data['arrival_time']->diffForHumans()); ?></div>
                  <?php if($movement->unit_departed_at): ?>
                    <div class="text-xs text-orange-600 mt-1">
                      🚛 Unit left: <?php echo e($movement->unit_departed_at->format('H:i')); ?>

                    </div>
                  <?php endif; ?>
                  <?php if($movement->collection_unit_arrived_at): ?>
                    <div class="text-xs text-green-600 mt-1">
                      🚚 Collection: <?php echo e($movement->collection_unit_arrived_at->format('H:i')); ?>

                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-400">Unknown</span>
                <?php endif; ?>
              </td>
              <!-- Time on Site -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span class="font-mono <?php echo e($data['time_on_site_minutes'] > 240 ? 'text-red-600 font-bold' : 'text-gray-900'); ?>">
                  <?php echo e($formatMinutes($data['time_on_site_minutes'])); ?>

                </span>
                <?php if($data['time_on_site_minutes'] > 240): ?>
                  <br><span class="text-xs text-red-500">⚠️ Long wait</span>
                <?php endif; ?>
              </td>
              <!-- In Current Status -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <span class="font-mono text-gray-900">
                  <?php echo e($formatMinutes($data['time_in_current_status_minutes'])); ?>

                </span>
                <?php if($data['status_start_time']): ?>
                  <br><span class="text-xs text-gray-400">Since <?php echo e($data['status_start_time']->format('H:i')); ?></span>
                <?php endif; ?>
              </td>
              <!-- Tipping Duration -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <?php if($data['tipping_duration_minutes']): ?>
                  <span class="font-mono text-green-600"><?php echo e($formatMinutes($data['tipping_duration_minutes'])); ?></span>
                  <br><span class="text-xs text-green-500">✅ Completed</span>
                <?php elseif($movement->current_status === 'unloading' && $movement->unloading_started_at): ?>
                  <?php
                    $currentTippingDuration = $movement->unloading_started_at->diffInMinutes(now());
                  ?>
                  <span class="font-mono text-orange-600"><?php echo e($formatMinutes($currentTippingDuration)); ?></span>
                  <br><span class="text-xs text-orange-500">⏱️ In progress</span>
                <?php else: ?>
                  <span class="text-gray-400">-</span>
                <?php endif; ?>
              </td>
              <!-- Actions -->
              <td class="px-4 py-4 whitespace-nowrap text-sm">
                <div class="flex flex-col space-y-1">
                  <?php if($isFactory): ?>
                    <a href="<?php echo e(route('app.factory-bookings.show', $activeBooking)); ?>" 
                       class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 text-center">
                      👁️ View
                    </a>
                  <?php else: ?>
                    <a href="<?php echo e(route('app.tipping-workflow.show', $activeBooking)); ?>" 
                       class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 text-center">
                      🔧 Manage
                    </a>
                    <a href="<?php echo e(route('app.bookings.show', $activeBooking)); ?>" 
                       class="px-2 py-1 bg-gray-600 text-white text-xs rounded hover:bg-gray-700 text-center">
                      👁️ View
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-lg shadow">
      <div class="text-center py-12">
        <div class="text-gray-400 text-6xl mb-4">🚛</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers Currently on Site</h3>
        <p class="text-gray-600">All trailers have been processed and collected.</p>
      </div>
    </div>
    <?php endif; ?>
  </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/trailer-operations-dashboard.blade.php ENDPATH**/ ?>