  
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
   <?php $__env->slot('header', null, []); ?> 
    <h2 class="text-xl font-semibold">Slot Usage Viewer</h2>
   <?php $__env->endSlot(); ?>
  <div class="max-w-5xl mx-auto py-6">
    <form method="GET" action="<?php echo e(route('app.slot-usage.index')); ?>" class="flex items-end gap-4 mb-6">
      <div>
        <label class="block text-sm mb-1">Depot</label>
        <select name="depot_id" class="border rounded p-2">
          <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($depot->id); ?>" <?php if($depot->id == $selectedDepot): echo 'selected'; endif; ?>>
              <?php echo e($depot->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <label class="block text-sm mb-1">Date</label>
        <input type="date" name="date" value="<?php echo e($date); ?>" class="border rounded p-2">
      </div>
      <div>
        <button class="mt-5 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Filter
        </button>
      </div>
    </form>
    <table class="w-full table-auto text-sm border">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-2">Start</th>
          <th class="border p-2">End</th>
          <th class="border p-2">Type(s)</th>
          <th class="border p-2">Booked</th>
          <th class="border p-2">Capacity</th>
          <th class="border p-2">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            $count = $slot->bookings->count();
            $capacity = $slot->capacity;
            $status = $count >= $capacity ? 'Full' : ($count > 0 ? 'Partial' : 'Free');
          ?>
          <tr>
            <td class="border p-2"><?php echo e(\Carbon\Carbon::parse($slot->start_at)->format('d-M H:i')); ?></td>
            <td class="border p-2"><?php echo e(\Carbon\Carbon::parse($slot->end_at)->format('d-M H:i')); ?></td>
            <td class="border p-2">
              <?php $__currentLoopData = $slot->bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-block bg-gray-200 rounded px-2 text-xs"><?php echo e($booking->bookingType->name); ?></span>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </td>
            <td class="border p-2"><?php echo e($count); ?></td>
            <td class="border p-2"><?php echo e($capacity); ?></td>
            <td class="border p-2">
              <?php if($status === 'Full'): ?>
                <span class="text-red-600 font-semibold">Full</span>
              <?php elseif($status === 'Partial'): ?>
                <span class="text-orange-600 font-semibold">Partial</span>
              <?php else: ?>
                <span class="text-green-600 font-semibold">Free</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="6" class="border p-4 text-center text-gray-500">No slots found for this depot and date.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slot-usage/index.blade.php ENDPATH**/ ?>