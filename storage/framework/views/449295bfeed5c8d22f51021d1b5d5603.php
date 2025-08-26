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
      <h2 class="font-semibold text-xl">📍 Trailer Location Report</h2>
      <div class="flex items-center space-x-4">
        
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
        
        <div class="flex space-x-3">
          <a href="<?php echo e(route('app.empty-unit-collection')); ?>"
             class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            🚛 Empty Collection
          </a>
          <a href="<?php echo e(route('app.bookings.index')); ?>"
             class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
            ← Back to Bookings
          </a>
        </div>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-7xl mx-auto">
    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-blue-600"><?php echo e($stats['total_on_site'] ?? 0); ?></div>
        <div class="text-sm text-gray-600">Total On Site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-orange-600"><?php echo e($stats['awaiting_collection'] ?? 0); ?></div>
        <div class="text-sm text-gray-600">Awaiting Collection</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-green-600"><?php echo e($stats['empty_available'] ?? 0); ?></div>
        <div class="text-sm text-gray-600">Empty Available</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-purple-600"><?php echo e($stats['being_tipped'] ?? 0); ?></div>
        <div class="text-sm text-gray-600">Being Tipped</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-indigo-600"><?php echo e($stats['in_waiting_areas'] ?? 0); ?></div>
        <div class="text-sm text-gray-600">In Waiting Areas</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <div class="text-2xl font-bold text-red-600"><?php echo e($stats['overdue_collections'] ?? 0); ?></div>
        <div class="text-sm text-gray-600">Overdue Collections</div>
      </div>
    </div>
    <!-- Trailers Waiting to Start Tipping -->
    <?php if(isset($waitingToTip) && $waitingToTip->count() > 0): ?>
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
              <?php if(!$currentDepotId): ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
              <?php endif; ?>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waiting Time</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $__currentLoopData = $waitingToTip; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $booking = $movement->bookable ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <?php if($booking && $booking->id): ?>
                  <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <a href="<?php echo e(route('app.factory-bookings.show', $booking)); ?>" class="hover:underline">
                      <?php echo e($booking->reference ?? 'Unknown'); ?>

                    </a>
                  <?php else: ?>
                    <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" class="hover:underline">
                      <?php echo e($booking->booking_reference ?? 'Unknown'); ?>

                    </a>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-500">No booking</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <?php echo e($booking && $booking->customer ? $booking->customer->name : 'Unknown'); ?>

              </td>
              <?php if(!$currentDepotId): ?>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <?php echo e($booking && $booking->depot ? $booking->depot->name : 'Unknown'); ?>

                  <?php else: ?>
                    <?php echo e($booking && $booking->slot && $booking->slot->depot ? $booking->slot->depot->name : 'Unknown'); ?>

                  <?php endif; ?>
                </td>
              <?php endif; ?>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php echo e($booking && $booking->vehicle_registration ? $booking->vehicle_registration : 'Not specified'); ?>

                <?php
                  $containerNumber = $booking instanceof \App\Models\FactoryBooking 
                    ? $booking->trailer_registration 
                    : $booking->container_number;
                ?>
                <?php if($booking && $containerNumber): ?>
                  <br><span class="text-xs text-gray-500"><?php echo e($containerNumber); ?></span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->tippingLocation): ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-800">
                    <?php echo e($movement->tippingLocation->name); ?>

                  </span>
                <?php else: ?>
                  <span class="text-gray-400">Not assigned</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <?php
                  $statusDisplay = match($movement->current_status) {
                    'trailer_dropped' => '🔄 Trailer Detached',
                    'in_location' => '🚛 Parked - Waiting',
                    'arrived' => '🚐 Just Arrived',
                    'in_waiting' => '⏳ In Waiting Area',
                    default => '⏳ ' . ucwords(str_replace('_', ' ', $movement->current_status))
                  };
                  $statusClass = match($movement->current_status) {
                    'trailer_dropped' => 'bg-red-100 text-red-800',
                    'in_location' => 'bg-blue-100 text-blue-800',
                    'arrived' => 'bg-green-100 text-green-800',
                    'in_waiting' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-yellow-100 text-yellow-800'
                  };
                ?>
                <span class="px-2 py-1 text-xs rounded-full <?php echo e($statusClass); ?>">
                  <?php echo e($statusDisplay); ?>

                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->current_status === 'trailer_dropped' && $movement->trailer_dropped_at): ?>
                  <?php echo e($movement->trailer_dropped_at->diffForHumans()); ?>

                <?php elseif($movement->current_status === 'in_location' && $movement->moved_to_location_at): ?>
                  <?php echo e($movement->moved_to_location_at->diffForHumans()); ?>

                <?php elseif($movement->actual_arrival): ?>
                  <?php echo e($movement->actual_arrival->diffForHumans()); ?>

                <?php else: ?>
                  <span class="text-gray-400">Unknown</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
    <!-- Currently Being Tipped -->
    <?php if(isset($currentlyTipping) && $currentlyTipping->count() > 0): ?>
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
              <?php if(!$currentDepotId): ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
              <?php endif; ?>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipping Bay</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time in Bay</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $__currentLoopData = $currentlyTipping; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $booking = $movement->bookable ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <?php if($booking && $booking->id): ?>
                  <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <a href="<?php echo e(route('app.factory-bookings.show', $booking)); ?>" class="hover:underline">
                      <?php echo e($booking->reference ?? 'Unknown'); ?>

                    </a>
                  <?php else: ?>
                    <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" class="hover:underline">
                      <?php echo e($booking->booking_reference ?? 'Unknown'); ?>

                    </a>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-500">No booking</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <?php echo e($booking && $booking->customer ? $booking->customer->name : 'Unknown'); ?>

              </td>
              <?php if(!$currentDepotId): ?>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                  <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <?php echo e($booking && $booking->depot ? $booking->depot->name : 'Unknown'); ?>

                  <?php else: ?>
                    <?php echo e($booking && $booking->slot && $booking->slot->depot ? $booking->slot->depot->name : 'Unknown'); ?>

                  <?php endif; ?>
                </td>
              <?php endif; ?>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php echo e($booking && $booking->vehicle_registration ? $booking->vehicle_registration : 'Not specified'); ?>

                <?php
                  $containerNumber = $booking instanceof \App\Models\FactoryBooking 
                    ? $booking->trailer_registration 
                    : $booking->container_number;
                ?>
                <?php if($booking && $containerNumber): ?>
                  <br><span class="text-xs text-gray-500"><?php echo e($containerNumber); ?></span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->tippingBay): ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    <?php echo e($movement->tippingBay->name); ?>

                  </span>
                <?php else: ?>
                  <span class="text-gray-400">Not assigned</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full 
                  <?php if($movement->current_status === 'unloading'): ?> bg-orange-100 text-orange-800 
                  <?php else: ?> bg-blue-100 text-blue-800 <?php endif; ?>">
                  <?php if($movement->current_status === 'unloading'): ?> ⚡ Unloading
                  <?php else: ?> 🚛 At Bay <?php endif; ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->moved_to_bay_at): ?>
                  <?php echo e($movement->moved_to_bay_at->diffForHumans()); ?>

                <?php elseif($movement->unloading_started_at): ?>
                  <?php echo e($movement->unloading_started_at->diffForHumans()); ?>

                <?php elseif($movement->actual_arrival): ?>
                  <?php echo e($movement->actual_arrival->diffForHumans()); ?>

                <?php else: ?>
                  <span class="text-gray-400">Unknown</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
    <!-- Empty Trailers Ready for Collection -->
    <?php if(isset($emptyTrailers) && $emptyTrailers->count() > 0): ?>
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
              <?php if(!$currentDepotId): ?>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
              <?php endif; ?>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container/Trailer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Collection</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $__currentLoopData = $emptyTrailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $booking = $movement->bookable ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                <?php if($booking && $booking->id): ?>
                  <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <a href="<?php echo e(route('app.factory-bookings.show', $booking)); ?>" class="hover:underline">
                      <?php echo e($booking->reference ?? 'Unknown'); ?>

                    </a>
                  <?php else: ?>
                    <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" class="hover:underline">
                      <?php echo e($booking->booking_reference ?? 'Unknown'); ?>

                    </a>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-500">No booking</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                <?php echo e($booking && $booking->customer ? $booking->customer->name : 'Unknown'); ?>

              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php
                  $containerNumber = $booking instanceof \App\Models\FactoryBooking 
                    ? ($booking->trailer_registration ?? 'Not specified')
                    : ($booking->container_number ?? 'Not specified');
                ?>
                <?php echo e($containerNumber); ?>

                <?php if($booking && $booking->trailerType): ?>
                  <br><span class="text-xs text-gray-500"><?php echo e($booking->trailerType->name); ?></span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->tippingBay): ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    <?php echo e($movement->tippingBay->name); ?>

                  </span>
                <?php elseif($movement->tippingLocation): ?>
                  <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    <?php echo e($movement->tippingLocation->name); ?>

                  </span>
                <?php else: ?>
                  <span class="text-gray-400">Unknown</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($movement->unloading_completed_at): ?>
                  <?php echo e($movement->unloading_completed_at->format('d-M H:i')); ?>

                  <br><span class="text-xs text-gray-400"><?php echo e($movement->unloading_completed_at->diffForHumans()); ?></span>
                <?php else: ?>
                  <span class="text-gray-400">Unknown</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                  <span class="text-gray-400">N/A - Factory Delivery</span>
                <?php elseif($booking && $booking->trailer_collection_scheduled): ?>
                  <?php if($booking->trailer_collection_scheduled->isPast()): ?>
                    <span class="text-red-600 font-medium">
                      <?php echo e($booking->trailer_collection_scheduled->format('d-M-Y H:i')); ?>

                      <br><span class="text-xs">⚠️ OVERDUE</span>
                    </span>
                  <?php else: ?>
                    <span class="text-gray-600">
                      <?php echo e($booking->trailer_collection_scheduled->format('d-M-Y H:i')); ?>

                    </span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-400">Not scheduled</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm">
                <?php if($movement->tippingBay): ?>
                  
                  <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <span class="text-xs text-gray-500">Factory Delivery - Use Workflow</span>
                  <?php else: ?>
                    <form method="POST" action="<?php echo e(route('app.bookings.clear-bay', $booking)); ?>" class="inline">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700"
                              title="Clear bay for next vehicle">
                        🔄 Clear Bay
                      </button>
                    </form>
                  <?php endif; ?>
                <?php else: ?>
                  
                  <span class="text-xs text-gray-500">In waiting area</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php endif; ?>
    <!-- No Trailers Message (if none in any category) -->
    <?php if($movementsOnSite->count() === 0): ?>
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
    <?php endif; ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/trailer-location-report.blade.php ENDPATH**/ ?>