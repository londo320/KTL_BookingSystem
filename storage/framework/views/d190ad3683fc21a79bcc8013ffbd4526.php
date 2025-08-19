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
      <h2 class="font-semibold text-xl">Create Pallet Type</h2>
      <a href="<?php echo e(route('admin.pallet-types.index')); ?>"
         class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
        Back to Pallet Types
      </a>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
      <form method="POST" action="<?php echo e(route('admin.pallet-types.store')); ?>">
        <?php echo csrf_field(); ?>

        <div class="grid grid-cols-1 gap-6">
          
          <div>
            <label class="block text-sm font-medium text-gray-700">Name *</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                   class="mt-1 block w-full border-gray-300 rounded-lg">
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          
          <div>
            <label class="block text-sm font-medium text-gray-700">Code *</label>
            <input type="text" name="code" value="<?php echo e(old('code')); ?>" required
                   maxlength="10" style="text-transform: uppercase;"
                   class="mt-1 block w-full border-gray-300 rounded-lg">
            <p class="text-xs text-gray-500 mt-1">Short code (max 10 characters, will be uppercase)</p>
            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" 
                      class="mt-1 block w-full border-gray-300 rounded-lg"><?php echo e(old('description')); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          
          <div>
            <div class="flex items-center">
              <input type="checkbox" name="is_active" value="1" 
                     <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                     class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
              <label class="ml-2 text-sm text-gray-700">Active</label>
            </div>
            <p class="text-xs text-gray-500 mt-1">Inactive pallet types won't appear in selection lists</p>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <a href="<?php echo e(route('admin.pallet-types.index')); ?>"
             class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
          </a>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Create Pallet Type
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Auto-uppercase code field
    document.querySelector('input[name="code"]').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/pallet-types/create.blade.php ENDPATH**/ ?>