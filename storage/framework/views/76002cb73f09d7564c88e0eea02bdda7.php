<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6 max-w-3xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Bay Capacity Rule</h1>
        <p class="text-gray-600 mt-1">Update maximum concurrent bookings for a booking type at a depot</p>
    </div>

    <form action="<?php echo e(route('app.bay-capacity-rules.update', $bayCapacityRule)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depot *</label>
                <select name="depot_id" id="depot_id" required
                        class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">Select Depot</option>
                    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($depot->id); ?>" <?php if(old('depot_id', $bayCapacityRule->depot_id) == $depot->id): echo 'selected'; endif; ?>>
                            <?php echo e($depot->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type</label>
                <select name="booking_type_id" class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">All Booking Types</option>
                    <?php $__currentLoopData = $bookingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>" <?php if(old('booking_type_id', $bayCapacityRule->booking_type_id) == $type->id): echo 'selected'; endif; ?>>
                            <?php echo e($type->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Leave blank to apply to all booking types</p>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                    <input type="time" name="time_start" value="<?php echo e(old('time_start', \Carbon\Carbon::parse($bayCapacityRule->time_start)->format('H:i'))); ?>" required
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    <?php $__errorArgs = ['time_start'];
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                    <input type="time" name="time_end" value="<?php echo e(old('time_end', \Carbon\Carbon::parse($bayCapacityRule->time_end)->format('H:i'))); ?>" required
                           class="block w-full border-gray-300 rounded text-sm py-2">
                    <?php $__errorArgs = ['time_end'];
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
                <label class="block text-sm font-medium text-gray-700 mb-2">Days of Week (optional)</label>
                <div class="grid grid-cols-7 gap-2">
                    <?php $__currentLoopData = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="flex items-center text-sm">
                            <input type="checkbox" name="days_of_week[]" value="<?php echo e($day); ?>"
                                   <?php if(in_array($day, old('days_of_week', $bayCapacityRule->days_of_week ?? []))): echo 'checked'; endif; ?>
                                   class="mr-1">
                            <?php echo e(substr($day, 0, 3)); ?>

                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-gray-500 mt-1">Leave unchecked to apply to all days</p>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Max Concurrent Bookings *</label>
                <input type="number" name="max_concurrent_bookings" value="<?php echo e(old('max_concurrent_bookings', $bayCapacityRule->max_concurrent_bookings)); ?>"
                       min="1" max="100" required
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">Example: 3 = Maximum 3 bookings at the same time</p>
                <?php $__errorArgs = ['max_concurrent_bookings'];
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity Weight</label>
                <input type="number" name="capacity_weight" value="<?php echo e(old('capacity_weight', $bayCapacityRule->capacity_weight)); ?>"
                       min="0.1" max="10" step="0.1"
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">1.0 = normal, 2.0 = uses double capacity (e.g., handball)</p>
                <?php $__errorArgs = ['capacity_weight'];
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
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" <?php if(old('is_active', $bayCapacityRule->is_active)): echo 'checked'; endif; ?>
                           class="mr-2">
                    <span class="text-sm font-medium text-gray-700">Rule is active</span>
                </label>
            </div>
        </div>

        
        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('app.bay-capacity-rules.index')); ?>"
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bay_capacity_rules/edit.blade.php ENDPATH**/ ?>