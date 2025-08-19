<?php echo csrf_field(); ?>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    
    <div>
        <label for="customer_id" class="block text-sm font-medium">Customer</label>
        <select name="customer_id" id="customer_id" class="mt-1 block w-full border rounded p-2">
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($c->id); ?>"
                    <?php echo e(old('customer_id', $rule->customer_id ?? '') == $c->id ? 'selected' : ''); ?>>
                    <?php echo e($c->name); ?>

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
        <label for="depot_id" class="block text-sm font-medium">Depot</label>
        <select name="depot_id" id="depot_id" class="mt-1 block w-full border rounded p-2">
            <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($d->id); ?>"
                    <?php echo e(old('depot_id', $rule->depot_id ?? '') == $d->id ? 'selected' : ''); ?>>
                    <?php echo e($d->name); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
        <label for="product_id" class="block text-sm font-medium">Product</label>
        <select name="product_id" id="product_id" class="mt-1 block w-full border rounded p-2">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p->id); ?>"
                    <?php echo e(old('product_id', $rule->product_id ?? '') == $p->id ? 'selected' : ''); ?>>
                    <?php echo e($p->sku); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
        <label for="min_cases" class="block text-sm font-medium">Minimum Cases</label>
        <input type="number" name="min_cases" id="min_cases"
               value="<?php echo e(old('min_cases', $rule->min_cases ?? '')); ?>"
               class="mt-1 block w-full border rounded p-2" />
        <?php $__errorArgs = ['min_cases'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
        <label for="max_cases" class="block text-sm font-medium">Maximum Cases</label>
        <input type="number" name="max_cases" id="max_cases"
               value="<?php echo e(old('max_cases', $rule->max_cases ?? '')); ?>"
               class="mt-1 block w-full border rounded p-2" />
        <?php $__errorArgs = ['max_cases'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
        <label for="override_duration_minutes" class="block text-sm font-medium">
            Override Duration (minutes)
        </label>
        <input type="number" name="override_duration_minutes" id="override_duration_minutes"
               value="<?php echo e(old('override_duration_minutes', $rule->override_duration_minutes ?? '')); ?>"
               class="mt-1 block w-full border rounded p-2" />
        <?php $__errorArgs = ['override_duration_minutes'];
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
        Save Rule
    </button>
    <a href="<?php echo e(route('admin.customer-depot-products.index')); ?>"
       class="ml-4 text-gray-600 hover:underline">Cancel</a>
</div>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/customer_depot_products/form.blade.php ENDPATH**/ ?>