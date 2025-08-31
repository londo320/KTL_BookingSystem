
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
  <?php echo $__env->make('layouts.customer-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">My Bookings</h2>
      <a href="<?php echo e(route('customer.bookings.create')); ?>"
         class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
        + New Booking
      </a>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto">
    <?php if(session('success')): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    
    <div class="mb-4 flex flex-wrap gap-2">
      <a href="<?php echo e(route('customer.bookings.index', ['filter' => 'today'])); ?>" 
         class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
        📅 Today
      </a>
      <a href="<?php echo e(route('customer.bookings.index', ['filter' => 'tomorrow'])); ?>" 
         class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
        🗓️ Tomorrow
      </a>
      <a href="<?php echo e(route('customer.bookings.index', ['filter' => 'last_week'])); ?>" 
         class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'last_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
        📉 Last Week
      </a>
      <a href="<?php echo e(route('customer.bookings.index', ['filter' => 'this_week'])); ?>" 
         class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
        📊 This Week
      </a>
      <a href="<?php echo e(route('customer.bookings.index', ['filter' => 'next_week'])); ?>" 
         class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'next_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
        📈 Next Week
      </a>
    </div>

    
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded">
      <div>
        <label class="block text-sm font-medium">Depot</label>
        <select name="depot_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($depot->id); ?>" <?php if(request('depot_id') == $depot->id): echo 'selected'; endif; ?>><?php echo e($depot->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Type</label>
        <select name="booking_type_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type->id); ?>" <?php if(request('booking_type_id') == $type->id): echo 'selected'; endif; ?>><?php echo e($type->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Week Number</label>
        <select name="week_number" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($week['number']); ?>" <?php if(request('week_number') == $week['number']): echo 'selected'; endif; ?>>
              Week <?php echo e($week['number']); ?> (<?php echo e($week['start']); ?> - <?php echo e($week['end']); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">From</label>
        <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">To</label>
        <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">Arrival Status</label>
        <select name="arrival" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <option value="not_arrived" <?php if(request('arrival')=='not_arrived'): echo 'selected'; endif; ?>>📋 Not Arrived</option>
          <option value="late_runners" <?php if(request('arrival')=='late_runners'): echo 'selected'; endif; ?>>⏰ Late Runners</option>
          <option value="arrived" <?php if(request('arrival')=='arrived'): echo 'selected'; endif; ?>>✅ Arrived</option>
          <option value="on_time" <?php if(request('arrival')=='on_time'): echo 'selected'; endif; ?>>🎯 Arrived On Time</option>
          <option value="arrived_late" <?php if(request('arrival')=='arrived_late'): echo 'selected'; endif; ?>>🔶 Arrived Late</option>
          <option value="onsite" <?php if(request('arrival')=='onsite'): echo 'selected'; endif; ?>>🚛 On Site</option>
          <option value="completed" <?php if(request('arrival')=='completed'): echo 'selected'; endif; ?>>✅ Completed</option>
        </select>
      </div>
      <div class="flex space-x-2">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
        <a href="<?php echo e(route('customer.bookings.index')); ?>" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">Clear</a>
      </div>
    </form>

    <?php if($bookings->isEmpty()): ?>
      <p class="text-gray-500">No bookings found matching your criteria.</p>
    <?php else: ?>
      <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Booking Ref</th>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Start → End</th>
            <th class="px-4 py-2 text-left">Type</th>
            <th class="px-4 py-2 text-left">Vehicle/Container</th>
            <th class="px-4 py-2 text-left">Expected / Actual</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
              $isFactory = isset($booking->type) && $booking->type === 'factory';
              $rowBg = $isFactory ? 'bg-purple-25' : '';
            ?>
            <tr class="border-t hover:bg-gray-50 <?php echo e($rowBg); ?>">
              <td class="px-4 py-2">
                <?php if($isFactory): ?>
                  <span class="font-mono text-sm font-semibold text-purple-600">
                    🏭 <?php echo e($booking->booking_reference ?? 'N/A'); ?>

                  </span>
                  <br><span class="text-xs bg-purple-100 px-2 py-1 rounded text-purple-700">Factory</span>
                <?php else: ?>
                  <span class="font-mono text-sm font-semibold text-blue-600">
                    <?php echo e($booking->booking_reference ?? 'N/A'); ?>

                  </span>
                <?php endif; ?>
                <?php if($booking->reference && !$isFactory): ?>
                  <br><span class="text-xs text-gray-500"><?php echo e($booking->reference); ?></span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2"><?php echo e($booking->slot->depot->name); ?></td>
              <td class="px-4 py-2">
                <?php
                  $slotStart = $booking->slot->start_at;
                  $now = now();
                  $arrivedAt = $booking->arrived_at;
                  $isLateNotArrived = $now->greaterThan($slotStart) && !$arrivedAt;
                  $isLateArrived = $arrivedAt && $arrivedAt->greaterThan($slotStart);
                ?>
                
                <?php if($isLateNotArrived): ?>
                  <?php if($booking->estimated_arrival): ?>
                    <div class="text-blue-600 text-xs font-semibold">
                      💬 Updated ETA: <?php echo e(\Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                    </div>
                  <?php endif; ?>
                  <div class="text-red-600 text-xs font-semibold">
                    Original: <?php echo e($slotStart->format('d-M H:i')); ?>

                  </div>
                  <div class="text-red-600 text-xs">
                    Late by: <?php echo e($slotStart->diffForHumans()); ?>

                  </div>
                <?php elseif($isLateArrived): ?>
                  <?php if($booking->estimated_arrival): ?>
                    <div class="text-blue-600 text-xs font-semibold">
                      💬 Updated ETA: <?php echo e(\Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                    </div>
                  <?php endif; ?>
                  <div class="text-orange-600 text-xs font-semibold">
                    Original: <?php echo e($slotStart->format('d-M H:i')); ?>

                  </div>
                <?php endif; ?>
                
                <?php echo e(\Carbon\Carbon::parse($booking->slot->start_at)->format('d-M H:i')); ?> →
                <?php echo e(\Carbon\Carbon::parse($booking->slot->end_at)->format('d-M H:i')); ?>

              </td>
              <td class="px-4 py-2"><?php echo e(optional($booking->bookingType)->name ?? '-'); ?></td>
              <td class="px-4 py-2">
                <?php if($booking->vehicle_registration): ?>
                  🚛 <?php echo e($booking->vehicle_registration); ?><br>
                <?php endif; ?>
                <?php if($isFactory && $booking->trailer_registration): ?>
                  🚚 <?php echo e($booking->trailer_registration); ?><br>
                <?php endif; ?>
                <?php if($booking->container_number): ?>
                  📦 <?php echo e($booking->container_number); ?><br>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2">
                <div class="text-xs">
                  <?php if($isFactory): ?>
                    <div class="text-purple-600">
                      🏭 Factory Delivery
                      <?php if($booking->original_factory_booking->tipping_type): ?>
                        <br><span class="text-gray-600"><?php echo e(ucfirst($booking->original_factory_booking->tipping_type)); ?></span>
                      <?php endif; ?>
                    </div>
                  <?php elseif($booking->poNumbers->count() > 0): ?>
                    <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="mb-2">
                        <strong>PO: <?php echo e($po->po_number); ?></strong><br>
                        <div class="text-gray-600"><?php echo e($po->expected_summary_text); ?></div>
                        <?php if($po->total_actual_units > 0 || $po->total_actual_pallets > 0): ?>
                          <div class="text-green-600 font-medium"><?php echo e($po->actual_summary_text); ?></div>
                        <?php endif; ?>
                      </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php else: ?>
                    <div class="text-gray-500">No PO numbers specified</div>
                  <?php endif; ?>
                </div>
              </td>
              <td class="px-4 py-2">
                <?php if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'))): ?>
                  <span class="inline-block px-2 py-1 bg-black text-white rounded text-xs font-semibold">
                    ❌ Cancelled
                  </span>
                  <br><div class="text-xs text-gray-500 mt-1">
                    <?php echo e($booking->cancelled_at->format('d-M H:i')); ?>

                  </div>
                  <?php if($booking->cancellation_reason): ?>
                    <div class="text-xs text-gray-600 mt-1" title="<?php echo e($booking->cancellation_reason); ?>">
                      <?php echo e(Str::limit($booking->cancellation_reason, 25)); ?>

                    </div>
                  <?php endif; ?>
                <?php else: ?>
                  <?php if($booking->arrived_at): ?>
                    ✅ Arrived: <?php echo e($booking->arrived_at->format('d-M H:i')); ?><br>
                  <?php endif; ?>
                  <?php if($booking->departed_at): ?>
                    🕒 Departed: <?php echo e($booking->departed_at->format('d-M H:i')); ?><br>
                    <?php
                      $arr = \Carbon\Carbon::parse($booking->arrived_at);
                      $dep = \Carbon\Carbon::parse($booking->departed_at);
                      $dur = $arr->diffInMinutes($dep);
                      $slotDur = $booking->slot->start_at->diffInMinutes($booking->slot->end_at);
                      $badge = $dur > $slotDur
                        ? ['Over Time', 'bg-red-600']
                        : ['On Time', 'bg-green-600'];
                      $d = floor($dur / 1440);
                      $h = floor(($dur % 1440) / 60);
                      $m = $dur % 60;
                    ?>
                    <div class="text-xs text-gray-700 mt-1">
                      ⏱ Duration: <?php echo e("$d d $h h $m m"); ?>

                      <span class="ml-2 inline-block px-2 py-0.5 rounded text-white text-xs font-semibold <?php echo e($badge[1]); ?>">
                        <?php echo e($badge[0]); ?>

                      </span>
                    </div>
                  <?php endif; ?>
                  <?php if($booking->status): ?>
                    <span class="inline-block px-2 py-1 mt-1 rounded text-white text-xs font-semibold
                      <?php echo e($booking->status === 'early' ? 'bg-blue-500' : ($booking->status === 'on time' ? 'bg-green-500' : 'bg-red-600')); ?>">
                      <?php echo e(ucfirst($booking->status)); ?>

                    </span>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2">
                <div class="flex space-x-1">
                  <?php if($isFactory): ?>
                    
                    <span class="px-2 py-1 bg-purple-400 text-white rounded text-xs">
                      🏭 Factory Vehicle
                    </span>
                  <?php else: ?>
                    
                    <?php
                      $hasHistory = false;
                      // Check if this booking has history (was rebooked or is part of a rebook chain)
                      try {
                        $hasHistory = $booking->original_booking_id || 
                                     $booking->is_rebooked || 
                                     ($booking->cancellation_reason && str_contains($booking->cancellation_reason, 'Rebooked'));
                      } catch (\Exception $e) {
                        $hasHistory = false;
                      }
                    ?>
                    
                    <?php if($hasHistory): ?>
                      <a href="<?php echo e(route('customer.bookings.history', $booking)); ?>"
                         class="px-2 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-xs" 
                         title="This booking has history - view rebook/cancel history">
                        📋 History
                      </a>
                    <?php endif; ?>
                    
                    
                    <a href="<?php echo e(route('customer.bookings.show', $booking)); ?>"
                       class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">View</a>
                    
                    
                    <?php
                      $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
                      $hasArrived = $booking->arrived_at;
                      $isCancelled = $booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'));
                    ?>
                    
                    <?php if(!$isCancelled && !$hasArrived && !$isLocked && auth()->user()->can('update', $booking)): ?>
                      <a href="<?php echo e(route('customer.bookings.edit', $booking)); ?>"
                         class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                    <?php elseif(!$isCancelled && $isLocked && !$hasArrived): ?>
                      <span class="px-2 py-1 bg-orange-500 text-white rounded text-xs" title="Cut-off time passed">🔒 Locked</span>
                    <?php elseif(!$isCancelled && $hasArrived): ?>
                      <span class="px-2 py-1 bg-green-500 text-white rounded text-xs" title="Vehicle has arrived">⚫ Final</span>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>

      <div class="mt-4">
        <?php echo e($bookings->links()); ?>

      </div>
    <?php endif; ?>
  </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/customer/bookings/index.blade.php ENDPATH**/ ?>