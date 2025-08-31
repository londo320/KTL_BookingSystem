<?php $__env->startSection('title', 'Add New Carrier'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">➕ Add New Carrier</h1>
                <p class="mt-2 text-gray-600">Create a new carrier company</p>
            </div>
            <div>
                <a href="<?php echo e(route('app.carriers.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Carriers
                </a>
            </div>
        </div>
    </div>

    <!-- Info Notice -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-800">
                    💡 <strong>Smart Restore:</strong> If you try to create a carrier with a name that was previously deleted, the system will automatically restore the deleted carrier instead of creating a duplicate.
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="<?php echo e(route('app.carriers.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Carrier Information</h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Carrier Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Carrier Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?php echo e(old('name')); ?>"
                               required
                               placeholder="e.g., DHL, FedEx, Knowles Logistics"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $__errorArgs = ['name'];
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

                    <!-- Contact Email -->
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Email
                        </label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="<?php echo e(old('contact_email')); ?>"
                               placeholder="contact@carrier.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $__errorArgs = ['contact_email'];
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

                    <!-- Contact Phone -->
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Phone
                        </label>
                        <input type="tel" 
                               id="contact_phone" 
                               name="contact_phone" 
                               value="<?php echo e(old('contact_phone')); ?>"
                               placeholder="+44 1234 567890"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php $__errorArgs = ['contact_phone'];
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
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            <strong>Active</strong> - Carrier is available for new bookings
                        </span>
                    </label>
                    <?php $__errorArgs = ['is_active'];
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

                <!-- Depot Configuration -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">🏢 Depot Configuration</h3>
                    <p class="text-sm text-blue-700 mb-4">Configure which depots this carrier can operate with and any restrictions.</p>
                    
                    <div id="depot-configs" class="space-y-4">
                        <!-- Depot configurations will be added dynamically -->
                        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white p-4 rounded border">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-900"><?php echo e($depot->name); ?></h4>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="depot_configs[<?php echo e($depot->id); ?>][enabled]"
                                           value="1"
                                           class="rounded">
                                    <span class="ml-2 text-sm">Enable for this depot</span>
                                </label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Auto-disable after (months)
                                    </label>
                                    <input type="number" 
                                           name="depot_configs[<?php echo e($depot->id); ?>][auto_disable_months]"
                                           value="6"
                                           min="1" max="24"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 text-sm">
                                </div>
                                
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="depot_configs[<?php echo e($depot->id); ?>][auto_disable_unused]"
                                               value="1"
                                               checked
                                               class="rounded">
                                        <span class="ml-2 text-sm">Auto-disable when unused</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Restrict to specific customers (optional)
                                </label>
                                <select name="depot_configs[<?php echo e($depot->id); ?>][allowed_customer_ids][]" 
                                        multiple
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 text-sm">
                                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Leave empty to allow all customers</p>
                            </div>
                            
                            <input type="hidden" name="depot_configs[<?php echo e($depot->id); ?>][depot_id]" value="<?php echo e($depot->id); ?>">
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <a href="<?php echo e(route('app.carriers.index')); ?>" 
                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    ➕ Create Carrier
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/carriers/create.blade.php ENDPATH**/ ?>