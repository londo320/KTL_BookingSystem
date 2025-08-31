<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-2xl font-semibold mb-4">Edit Customer</h2>

  <?php if($errors->any()): ?>
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?php echo e(route('app.customers.update', $customer)); ?>" method="POST" class="space-y-4 bg-white shadow rounded p-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>

    <div>
      <label for="name" class="block text-sm font-medium">Name</label>
      <input type="text" name="name" id="name"
             value="<?php echo e(old('name', $customer->name)); ?>"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
      <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
      <label for="user_ids" class="block text-sm font-medium">Assign Users (Optional)</label>
      <select name="user_ids[]"
              id="user_ids"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
              multiple
              size="6">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($user->id); ?>"
            <?php echo e(in_array($user->id, old('user_ids', $customer->users->pluck('id')->toArray())) ? 'selected' : ''); ?>>
            <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <p class="text-sm text-gray-600 mt-1">Hold Ctrl/Cmd to select multiple users. Leave empty if no users need assignment.</p>
      <?php $__errorArgs = ['user_ids'];
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

    <div class="flex justify-end space-x-2">
      <a href="<?php echo e(route('app.customers.index')); ?>"
         class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
        Cancel
      </a>
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Save Changes
      </button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customers/edit.blade.php ENDPATH**/ ?>