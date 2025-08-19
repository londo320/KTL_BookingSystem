<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-xl mx-auto">
  <h2 class="text-xl font-semibold mb-4">Create Depot</h2>

  <form method="POST" action="<?php echo e(route('admin.depots.store')); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>

    <div>
      <label class="block font-medium">Name</label>
      <input type="text" name="name" class="w-full border p-2 rounded" value="<?php echo e(old('name')); ?>" required>
      <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
      <label class="block font-medium">Location</label>
      <input type="text" name="location" class="w-full border p-2 rounded" value="<?php echo e(old('location')); ?>">
      <?php $__errorArgs = ['location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div>
      <label class="block font-medium">Cut Off Time</label>
      <input type="time" name="cut_off_time" class="w-full border p-2 rounded" value="<?php echo e(old('cut_off_time')); ?>" required>
      <?php $__errorArgs = ['cut_off_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="flex space-x-4">
      <a href="<?php echo e(route('admin.depots.index')); ?>" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Depot</button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/depots/create.blade.php ENDPATH**/ ?>