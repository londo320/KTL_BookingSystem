<?php echo csrf_field(); ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  
  <div>
    <label for="customer_id" class="block text-sm font-medium">Customer <span class="text-red-500">*</span></label>
    <select name="customer_id" id="customer_id" required
            class="mt-1 block w-full border rounded p-2">
      <option value="">– Select Customer –</option>
      <?php $__currentLoopData = \App\Models\Customer::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($customer->id); ?>"
                <?php if(old('customer_id', $product->customer_id ?? '') == $customer->id): echo 'selected'; endif; ?>>
          <?php echo e($customer->name); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div>
    <label for="sku" class="block text-sm font-medium">SKU <span class="text-red-500">*</span></label>
    <input type="text" name="sku" id="sku" required
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

  
  <div class="md:col-span-2">
    <label for="description" class="block text-sm font-medium">Description</label>
    <textarea name="description" id="description" rows="2"
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
    <label for="product_type" class="block text-sm font-medium">Product Type <span class="text-red-500">*</span></label>
    <select name="product_type" id="product_type" required
            class="mt-1 block w-full border rounded p-2">
      <option value="finished_product" <?php if(old('product_type', $product->product_type ?? 'finished_product') == 'finished_product'): echo 'selected'; endif; ?>>
        Finished Product
      </option>
      <option value="raw_material" <?php if(old('product_type', $product->product_type ?? '') == 'raw_material'): echo 'selected'; endif; ?>>
        Raw Material
      </option>
    </select>
    <?php $__errorArgs = ['product_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div>
    <label for="cases_per_pallet" class="block text-sm font-medium">Cases Per Pallet</label>
    <input type="number" name="cases_per_pallet" id="cases_per_pallet"
           value="<?php echo e(old('cases_per_pallet', $product->cases_per_pallet ?? '')); ?>"
           placeholder="e.g., 60"
           class="mt-1 block w-full border rounded p-2" />
    <p class="text-xs text-gray-500 mt-1">Auto-calculates total cases when entering pallets in bookings</p>
    <?php $__errorArgs = ['cases_per_pallet'];
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
  <a href="<?php echo e(route('app.products.index')); ?>"
     class="ml-4 text-gray-600 hover:underline">Cancel</a>
</div>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/products/form.blade.php ENDPATH**/ ?>