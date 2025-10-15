<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Duration Rule</h1>
        <p class="text-gray-600 mt-1">Update booking duration based on case count</p>
    </div>

    <form action="<?php echo e(route('app.duration-rules.update', $durationRule)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type *</label>
                <select name="booking_type_id" required
                        class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">Select Booking Type</option>
                    <?php $__currentLoopData = $bookingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>" <?php if(old('booking_type_id', $durationRule->booking_type_id) == $type->id): echo 'selected'; endif; ?>>
                            <?php echo e($type->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['booking_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Cases *</label>
                    <input type="number" name="min_cases" value="<?php echo e(old('min_cases', $durationRule->min_cases)); ?>" required
                           min="0" step="1"
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    <p class="text-xs text-gray-500 mt-1">Example: 0, 5001</p>
                    <?php $__errorArgs = ['min_cases'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maximum Cases</label>
                    <input type="number" name="max_cases" value="<?php echo e(old('max_cases', $durationRule->max_cases)); ?>"
                           min="0" step="1"
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    <p class="text-xs text-gray-500 mt-1">Leave blank for no limit (∞)</p>
                    <?php $__errorArgs = ['max_cases'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                <input type="number" name="duration_minutes" value="<?php echo e(old('duration_minutes', $durationRule->duration_minutes)); ?>" required
                       min="30" max="1440" step="15"
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">
                    Examples: 180 = 3 hours, 240 = 4 hours, 360 = 6 hours
                </p>
                <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depot (Optional)</label>
                <select name="depot_id" class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">All Depots</option>
                    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($depot->id); ?>" <?php if(old('depot_id', $durationRule->depot_id) == $depot->id): echo 'selected'; endif; ?>>
                            <?php echo e($depot->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave blank to apply to all depots</p>
                <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer (Optional)</label>
                <select name="customer_id" class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">All Customers</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($customer->id); ?>" <?php if(old('customer_id', $durationRule->customer_id) == $customer->id): echo 'selected'; endif; ?>>
                            <?php echo e($customer->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave blank to apply to all customers</p>
                <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <input type="number" name="priority" value="<?php echo e(old('priority', $durationRule->priority)); ?>"
                       min="0" max="100" step="1"
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">
                    Higher priority rules are checked first. Use 100 for customer-specific rules, 50 for depot-specific, 0 for global.
                </p>
                <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        
        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('app.duration-rules.index')); ?>"
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update Rule
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/duration_rules/edit.blade.php ENDPATH**/ ?>