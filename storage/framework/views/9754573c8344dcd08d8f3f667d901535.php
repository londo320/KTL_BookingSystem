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
    <h2 class="text-xl font-semibold">📦 Booking Types</h2>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-4xl mx-auto space-y-6">
    
    <?php if(session('success')): ?>
      <div class="bg-green-100 text-green-800 p-4 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    
    <div class="bg-white p-6 shadow rounded">
      <form method="POST" action="<?php echo e(route('app.booking-types.store')); ?>" class="space-y-4">
        <?php echo csrf_field(); ?>
        <div>
          <label class="block font-medium text-sm">Name</label>
          <input name="name" value="<?php echo e(old('name')); ?>" class="border p-2 w-full rounded" />
          <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Add Booking Type
        </button>
      </form>
    </div>
    
    <div class="bg-white p-6 shadow rounded">
      <h3 class="font-semibold mb-4 text-lg">Existing Types</h3>
      <table class="min-w-full text-sm">
        <thead>
          <tr>
            <th class="text-left px-2 py-1">Name</th>
            <th class="text-left px-2 py-1">Duration</th>
            <th class="text-left px-2 py-1">Available Times</th>
            <th class="text-left px-2 py-1">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t">
              <td class="px-2 py-1"><?php echo e($type->name); ?></td>
              <td class="px-2 py-1 text-gray-600"><?php echo e($type->duration_minutes ?? 60); ?> min</td>
              <td class="px-2 py-1">
                <span class="text-xs px-2 py-1 rounded <?php echo e(($type->booking_start_time || $type->booking_end_time) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); ?>">
                  <?php echo e($type->time_availability); ?>

                </span>
              </td>
              <td class="px-2 py-1 space-x-2">
                <a href="<?php echo e(route('app.booking-types.edit', $type)); ?>"
                   class="text-blue-600 hover:underline text-sm">Edit</a>
                <a href="<?php echo e(route('app.booking-types.equipment.edit', $type)); ?>"
                   class="text-green-600 hover:underline text-sm">🛠️ Equipment</a>
                <form method="POST"
                      action="<?php echo e(route('app.booking-types.destroy', $type)); ?>"
                      onsubmit="return confirm('Delete this booking type?');"
                      class="inline-block">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="text-red-600 hover:underline text-sm">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="4" class="text-center py-4 text-gray-500">
                No booking types found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/booking_types/index.blade.php ENDPATH**/ ?>