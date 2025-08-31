
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
<div class="py-6 max-w-7xl mx-auto">
    
    <?php if(session('success')): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <?php if(session('new_password')): ?>
        <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
            <strong>Your new password is:</strong> <?php echo e(session('new_password')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-xl font-semibold mb-6">Edit User: <?php echo e($user->name); ?></h2>

        
        <form method="POST" action="<?php echo e(route('app.users.update', $user->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                    
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" value="<?php echo e(old('name', $user->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" value="<?php echo e(old('email', $user->email)); ?>" required>
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
                        <div class="space-y-2">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        id="role_<?php echo e($role->id); ?>"
                                        name="role_ids[]"
                                        value="<?php echo e($role->id); ?>"
                                        class="border-gray-300 rounded"
                                        <?php echo e(in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : ''); ?>

                                        onchange="toggleFunctionSection()"
                                    >
                                    <span class="ml-2 text-sm capitalize"><?php echo e($role->name); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['role_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="reset_password" id="reset_password" class="border-gray-300 rounded">
                            <span class="ml-2 text-sm">Reset to Default Password</span>
                        </label>
                    </div>
                </div>

                
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Access & Permissions</h3>
                    
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Depot Access</label>
                        <div class="space-y-2 max-h-32 overflow-y-auto border rounded p-2">
                            <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="inline-flex items-center w-full">
                                    <input type="checkbox" id="depot_<?php echo e($depot->id); ?>" name="depot_ids[]" value="<?php echo e($depot->id); ?>" class="border-gray-300 rounded"
                                    <?php echo e(in_array($depot->id, old('depot_ids', $user->depots->pluck('id')->toArray())) ? 'checked' : ''); ?>>
                                    <span class="ml-2 text-sm"><?php echo e($depot->name); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['depot_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label for="depot_id" class="block text-sm font-medium text-gray-700">Default Depot</label>
                        <select name="depot_id" id="depot_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— No Default Depot —</option>
                            <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($depot->id); ?>" 
                                    <?php if($depot->id == old('depot_id', $user->depot_id)): echo 'selected'; endif; ?>>
                                    <?php echo e($depot->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Default depot shown on dashboards (must have access above)
                        </p>
                        <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Access</label>
                        
                        
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600">Primary Customer (customer role)</label>
                            <select name="customer_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">— No Primary Customer —</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>" 
                                        <?php if($customer->id == old('customer_id', $user->customer_id)): echo 'selected'; endif; ?>>
                                        <?php echo e($customer->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Multiple Customers (warehouse roles)</label>
                            <div class="max-h-32 overflow-y-auto border rounded p-2 space-y-1">
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="inline-flex items-center w-full">
                                        <input
                                            type="checkbox"
                                            name="customer_ids[]"
                                            value="<?php echo e($customer->id); ?>"
                                            class="border-gray-300 rounded"
                                            <?php echo e(in_array($customer->id, old('customer_ids', $user->customers->pluck('id')->toArray())) ? 'checked' : ''); ?>

                                        >
                                        <span class="ml-2 text-xs"><?php echo e($customer->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Leave empty to see ALL customers
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div id="function-section" class="mt-8 border-t pt-6" style="display: none;">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔧 Warehouse Functions</h3>
                <p class="text-sm text-gray-600 mb-4">Select specific functions this user can access. Admin users have access to all functions automatically.</p>
                
                <?php
                    $userFunctions = old('function_keys', $user->getFunctionKeys());
                    $allFunctions = \App\Models\UserFunction::getAllFunctions();
                ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $allFunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $functions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3"><?php echo e($category); ?></h4>
                            <div class="space-y-2">
                                <?php $__currentLoopData = $functions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="inline-flex items-start">
                                        <input
                                            type="checkbox"
                                            name="function_keys[]"
                                            value="<?php echo e($key); ?>"
                                            class="border-gray-300 rounded mt-0.5"
                                            <?php echo e(in_array($key, $userFunctions) ? 'checked' : ''); ?>

                                        >
                                        <div class="ml-2">
                                            <span class="text-sm font-medium text-gray-900"><?php echo e($label); ?></span>
                                            <div class="text-xs text-gray-500"><?php echo e($key); ?></div>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <?php $__errorArgs = ['function_keys'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <span class="text-red-600 text-sm"><?php echo e($message); ?></span>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="flex justify-end mt-8 pt-6 border-t">
                <a href="<?php echo e(route('app.users.index')); ?>" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    toggleFunctionSection();
});

function toggleFunctionSection() {
    const functionSection = document.getElementById('function-section');
    const warehouseRoles = ['warehouse', 'depot-admin', 'site-admin'];
    const roleCheckboxes = document.querySelectorAll('input[name="role_ids[]"]');
    
    let hasWarehouseRole = false;
    roleCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const roleName = checkbox.parentElement.textContent.trim().toLowerCase();
            if (warehouseRoles.includes(roleName)) {
                hasWarehouseRole = true;
            }
        }
    });
    
    // Show function section for warehouse roles (but not admin - admin gets all functions automatically)
    const adminCheckbox = document.querySelector('input[name="role_ids[]"][value]');
    let isAdmin = false;
    roleCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const roleName = checkbox.parentElement.textContent.trim().toLowerCase();
            if (roleName === 'admin') {
                isAdmin = true;
            }
        }
    });
    
    if (hasWarehouseRole && !isAdmin) {
        functionSection.style.display = 'block';
    } else {
        functionSection.style.display = 'none';
    }
}
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/users/edit_with_functions.blade.php ENDPATH**/ ?>