<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Time Window Configuration</h1>
                <p class="text-gray-600 mt-1">Configure allowed booking times for <strong><?php echo e($customer->name); ?></strong></p>
            </div>
            <a href="<?php echo e(route('app.customers.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ← Back to Customers
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Time Windows Work</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Time Windows:</strong> Restrict when this customer can make bookings at each depot</li>
            <li><strong>Example:</strong> Only allow bookings between 08:00-16:00 on weekdays</li>
            <li><strong>Leave blank:</strong> No time restrictions (customer can book anytime)</li>
            <li><strong>Days of Week:</strong> Optionally restrict to specific days</li>
        </ul>
    </div>

    <form action="<?php echo e(route('app.customers.time-windows.update', $customer)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">🕐 Time Windows by Depot</h2>

            <div class="space-y-4">
                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $window = $timeWindows->get($depot->id);
                    ?>
                    <div class="border rounded-lg p-4 hover:border-blue-300 transition">
                        <h3 class="font-semibold text-gray-800 mb-3"><?php echo e($depot->name); ?></h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Start Time</label>
                                <input type="time"
                                       name="depots[<?php echo e($depot->id); ?>][allowed_start_time]"
                                       value="<?php echo e($window?->allowed_start_time ?? ''); ?>"
                                       class="block w-full border-gray-300 rounded text-sm py-1">
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">End Time</label>
                                <input type="time"
                                       name="depots[<?php echo e($depot->id); ?>][allowed_end_time]"
                                       value="<?php echo e($window?->allowed_end_time ?? ''); ?>"
                                       class="block w-full border-gray-300 rounded text-sm py-1">
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                <select name="depots[<?php echo e($depot->id); ?>][is_active]"
                                        class="block w-full border-gray-300 rounded text-sm py-1">
                                    <option value="1" <?php if(($window?->is_active ?? true) === true): echo 'selected'; endif; ?>>✅ Active</option>
                                    <option value="0" <?php if(($window?->is_active ?? true) === false): echo 'selected'; endif; ?>>❌ Inactive</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="mt-3">
                            <label class="block text-xs font-medium text-gray-600 mb-2">Allowed Days (leave unchecked for all days)</label>
                            <div class="grid grid-cols-7 gap-2">
                                <?php $__currentLoopData = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox"
                                               name="depots[<?php echo e($depot->id); ?>][days_of_week][]"
                                               value="<?php echo e($day); ?>"
                                               <?php if(in_array($day, $window?->days_of_week ?? [])): echo 'checked'; endif; ?>
                                               class="mr-1">
                                        <?php echo e(substr($day, 0, 3)); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="flex justify-end gap-3">
            <a href="<?php echo e(route('app.customers.index')); ?>" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                💾 Save Time Windows
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customers/time-windows.blade.php ENDPATH**/ ?>