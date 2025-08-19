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
    <h2 class="text-xl font-semibold">Edit Slot Capacities</h2>
   <?php $__env->endSlot(); ?>

  <div class="max-w-6xl mx-auto py-6">
    <?php if(session('success')): ?>
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.slot-capacity.update')); ?>">
      <?php echo csrf_field(); ?>

      <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h3 class="text-lg font-semibold mt-6"><?php echo e($depot->name); ?></h3>

        <table class="w-full text-sm mb-4 border">
          <thead>
            <tr class="bg-gray-100">
              <th class="p-2 border">Start Time</th>
              <th class="p-2 border">End Time</th>
              <th class="p-2 border">Current Capacity</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $depot->slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td class="p-2 border"><?php echo e($slot->start_at->format('Y-m-d H:i')); ?></td>
                <td class="p-2 border"><?php echo e($slot->end_at->format('H:i')); ?></td>
                <td class="p-2 border">
                  <input type="number"
                         name="capacities[<?php echo e($slot->id); ?>]"
                         value="<?php echo e(old("capacities.{$slot->id}", $slot->capacity)); ?>"
                         class="w-16 border rounded p-1"
                         min="1" max="10">
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <div class="mt-4">
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Save Changes
        </button>
      </div>
    </form>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slot-capacity/index.blade.php ENDPATH**/ ?>