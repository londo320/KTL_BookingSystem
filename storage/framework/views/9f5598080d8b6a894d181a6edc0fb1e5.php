<?php echo csrf_field(); ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  
  <div>
    <label for="sku" class="block text-sm font-medium">SKU</label>
    <input type="text" name="sku" id="sku"
           value="<?php echo e(old('sku', $product->sku ?? '')); ?>"
           class="mt-1 block w-full border rounded p-2" />
    <?php $__errorArgs = ['sku'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div>
    <label for="description" class="block text-sm font-medium">Description</label>
    <textarea name="description" id="description" rows="3"
              class="mt-1 block w-full border rounded p-2"><?php echo e(old('description', $product->description ?? '')); ?></textarea>
    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div>
    <label for="default_case_count" class="block text-sm font-medium">Default Case Count</label>
    <input type="number" name="default_case_count" id="default_case_count"
           value="<?php echo e(old('default_case_count', $product->default_case_count ?? '')); ?>"
           class="mt-1 block w-full border rounded p-2" />
    <?php $__errorArgs = ['default_case_count'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div>
    <label for="default_pallets" class="block text-sm font-medium">Default Pallets</label>
    <input type="number" name="default_pallets" id="default_pallets"
           value="<?php echo e(old('default_pallets', $product->default_pallets ?? '')); ?>"
           class="mt-1 block w-full border rounded p-2" />
    <?php $__errorArgs = ['default_pallets'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
</div>

<div class="mt-6">
  <button type="submit"
          class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
    Save Product
  </button>
  <a href="<?php echo e(route('admin.products.index')); ?>"
     class="ml-4 text-gray-600 hover:underline">Cancel</a>
</div>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/products/form.blade.php ENDPATH**/ ?>