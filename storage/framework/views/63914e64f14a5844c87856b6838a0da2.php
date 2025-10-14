<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Customer Behavior Settings</h2>
                <p class="text-sm text-gray-600 mt-1">Customize behavior limits for <?php echo e($customer->name); ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('app.customers.index')); ?>" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Customers
                </a>
                <a href="<?php echo e(route('app.customer-behavior.show', $customer)); ?>" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View Behavior Analytics
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-4xl mx-auto">
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Please fix the following errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">Customer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Customer Name</p>
                    <p class="font-medium"><?php echo e($customer->name); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Contact Emails</p>
                    <p class="font-medium">
                        <?php if(!empty($customer->emails)): ?>
                            <?php echo e(implode(', ', $customer->emails)); ?>

                        <?php else: ?>
                            No emails on file
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">Behavior Limit Settings</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Configure custom limits for this customer. Values that match defaults will not be stored.
                </p>
            </div>
            <form method="POST" action="<?php echo e(route('app.customer-behavior.update-settings', $customer)); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-8">
                    
                    <div class="border-b border-gray-200 pb-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">📋 Booking Limits</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php $__currentLoopData = ['max_rebooks_per_booking', 'max_total_rebooks_30days', 'max_cancellations_30days']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $config = $availableSettings[$key] ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php echo e($config['label']); ?>

                                        <span class="text-xs text-gray-500">(Default: <?php echo e($config['default']); ?>)</span>
                                    </label>
                                    <input type="number" 
                                           name="<?php echo e($key); ?>" 
                                           value="<?php echo e($currentSettings[$key]); ?>"
                                           min="<?php echo e($config['min'] ?? 0); ?>" 
                                           max="<?php echo e($config['max'] ?? 999); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo e($config['description']); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">⏰ Time-Based Limits</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php $__currentLoopData = ['max_last_minute_rebooks_30days', 'minimum_hours_notice']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $config = $availableSettings[$key] ?>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <?php echo e($config['label']); ?>

                                        <span class="text-xs text-gray-500">(Default: <?php echo e($config['default']); ?>)</span>
                                    </label>
                                    <input type="number" 
                                           name="<?php echo e($key); ?>" 
                                           value="<?php echo e($currentSettings[$key]); ?>"
                                           min="<?php echo e($config['min'] ?? 0); ?>" 
                                           max="<?php echo e($config['max'] ?? 999); ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1"><?php echo e($config['description']); ?></p>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-4">🔒 Special Permissions</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php $__currentLoopData = ['allow_weekend_bookings', 'allow_holiday_bookings', 'priority_booking', 'auto_approve_bookings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $config = $availableSettings[$key] ?>
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input type="hidden" name="<?php echo e($key); ?>" value="0">
                                        <input type="checkbox" 
                                               name="<?php echo e($key); ?>" 
                                               value="1"
                                               <?php echo e($currentSettings[$key] ? 'checked' : ''); ?>

                                               class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    </div>
                                    <div class="ml-3">
                                        <label class="text-sm font-medium text-gray-700">
                                            <?php echo e($config['label']); ?>

                                            <span class="text-xs text-gray-500">(Default: <?php echo e($config['default'] ? 'Yes' : 'No'); ?>)</span>
                                        </label>
                                        <p class="text-xs text-gray-500"><?php echo e($config['description']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                    <button type="button" 
                            onclick="if(confirm('Reset all settings to default values?')) { window.location.href='<?php echo e(route('app.customer-behavior.reset-settings', $customer)); ?>'; }"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        🔄 Reset to Defaults
                    </button>
                    <div class="flex space-x-3">
                        <a href="<?php echo e(route('app.customers.index')); ?>" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            💾 Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <?php if($customer->behaviorSettings->isNotEmpty()): ?>
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h4 class="text-lg font-medium text-yellow-800 mb-4">Current Custom Settings</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php $__currentLoopData = $customer->behaviorSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white p-3 rounded border">
                            <p class="font-medium text-sm"><?php echo e($availableSettings[$setting->setting_key]['label'] ?? $setting->setting_key); ?></p>
                            <p class="text-lg font-bold text-yellow-700"><?php echo e($setting->getCastedValue()); ?></p>
                            <p class="text-xs text-gray-500">
                                Updated <?php echo e($setting->updated_at->format('M j, Y H:i')); ?>

                                <?php if($setting->updatedBy): ?>
                                    by <?php echo e($setting->updatedBy->name); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/customer-behavior/settings.blade.php ENDPATH**/ ?>