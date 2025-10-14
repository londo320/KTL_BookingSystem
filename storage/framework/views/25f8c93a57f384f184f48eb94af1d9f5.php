<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Booking Configuration</h1>
                <p class="text-gray-600 mt-1">Configure SKU/PO requirements for <strong><?php echo e($customer->name); ?></strong></p>
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
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How This Works</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Global Settings:</strong> Apply to all depots by default</li>
            <li><strong>Depot-Specific Overrides:</strong> Override global settings for specific depots</li>
            <li><strong>SKU Fields Enabled:</strong> Shows/hides PO numbers and product details in booking form</li>
            <li><strong>Require PO Data:</strong> Makes PO data mandatory or optional during booking creation</li>
        </ul>
    </div>

    <form action="<?php echo e(route('app.customers.booking-config.update', $customer)); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">🌐 Global Settings (All Depots)</h2>

            <div class="grid grid-cols-2 gap-6">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Show SKU/Product Fields
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="global[sku_fields_enabled]" value="1"
                                   <?php if($configData['global']['sku_fields_enabled'] === true): echo 'checked'; endif; ?>
                                   class="mr-2">
                            <span class="text-sm">✅ Enabled - Show SKU/PO fields in booking form</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="global[sku_fields_enabled]" value="0"
                                   <?php if($configData['global']['sku_fields_enabled'] === false): echo 'checked'; endif; ?>
                                   class="mr-2">
                            <span class="text-sm">❌ Disabled - Hide SKU/PO fields</span>
                        </label>
                    </div>
                </div>

                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Require PO Data
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="global[require_po_data]" value="1"
                                   <?php if($configData['global']['require_po_data'] === true): echo 'checked'; endif; ?>
                                   class="mr-2">
                            <span class="text-sm">✅ Required - PO data must be provided</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="global[require_po_data]" value="0"
                                   <?php if($configData['global']['require_po_data'] === false): echo 'checked'; endif; ?>
                                   class="mr-2">
                            <span class="text-sm">⚪ Optional - PO data is optional</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">🏭 Depot-Specific Overrides</h2>
            <p class="text-sm text-gray-600 mb-4">Leave empty to use global settings, or set specific values to override.</p>

            <div class="space-y-4">
                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="border rounded-lg p-4 hover:border-blue-300 transition">
                        <h3 class="font-semibold text-gray-800 mb-3"><?php echo e($depot->name); ?></h3>

                        <div class="grid grid-cols-2 gap-4">
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Show SKU Fields</label>
                                <select name="depots[<?php echo e($depot->id); ?>][sku_fields_enabled]" class="block w-full border-gray-300 rounded text-sm py-1">
                                    <option value="">Use Global Setting</option>
                                    <option value="1" <?php if($configData['depots'][$depot->id]['sku_fields_enabled'] === true): echo 'selected'; endif; ?>>✅ Enabled</option>
                                    <option value="0" <?php if($configData['depots'][$depot->id]['sku_fields_enabled'] === false): echo 'selected'; endif; ?>>❌ Disabled</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Require PO Data</label>
                                <select name="depots[<?php echo e($depot->id); ?>][require_po_data]" class="block w-full border-gray-300 rounded text-sm py-1">
                                    <option value="">Use Global Setting</option>
                                    <option value="1" <?php if($configData['depots'][$depot->id]['require_po_data'] === true): echo 'selected'; endif; ?>>✅ Required</option>
                                    <option value="0" <?php if($configData['depots'][$depot->id]['require_po_data'] === false): echo 'selected'; endif; ?>>⚪ Optional</option>
                                </select>
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
                💾 Save Configuration
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customers/booking-config.blade.php ENDPATH**/ ?>