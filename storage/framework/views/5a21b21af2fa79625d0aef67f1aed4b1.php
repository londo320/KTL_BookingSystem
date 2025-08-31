<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-7xl mx-auto">
    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <?php if(session('new_password')): ?>
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800"><strong>New password:</strong> <?php echo e(session('new_password')); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Edit User: <?php echo e($user->name); ?></h1>
            <p class="text-sm text-gray-600 mt-1">Configure user access, roles, and specific system functions</p>
        </div>

        <form method="POST" action="<?php echo e(route('app.users.update', $user->id)); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">👤 Basic Information</h2>
                        
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" required
                                   value="<?php echo e(old('name', $user->name)); ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo e(old('email', $user->email)); ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="reset_password" class="border-gray-300 rounded">
                                <span class="ml-2 text-sm">Reset to default password</span>
                            </label>
                        </div>
                    </div>

                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏷️ User Roles</h3>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="role_ids[]" 
                                           value="<?php echo e($role->id); ?>"
                                           class="border-gray-300 rounded"
                                           <?php echo e(in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : ''); ?>

                                           onchange="toggleFunctionSections()">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium capitalize"><?php echo e($role->name); ?></span>
                                        <div class="text-xs text-gray-500">
                                            <?php if($role->name === 'admin'): ?>
                                                Full system access - All functions enabled
                                            <?php elseif($role->name === 'customer'): ?>
                                                Customer portal access only
                                            <?php elseif($role->name === 'warehouse'): ?>
                                                Warehouse operations - Functions configurable below
                                            <?php elseif($role->name === 'depot-admin'): ?>
                                                Depot management + warehouse functions
                                            <?php elseif($role->name === 'site-admin'): ?>
                                                Site operations + warehouse functions
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php $__errorArgs = ['role_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏷️ Custom Roles</h3>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $customRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customRole): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="custom_role_ids[]" 
                                           value="<?php echo e($customRole->id); ?>"
                                           class="border-gray-300 rounded"
                                           <?php echo e(in_array($customRole->id, old('custom_role_ids', $user->customRoles->pluck('id')->toArray())) ? 'checked' : ''); ?>>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium"><?php echo e($customRole->display_name); ?></span>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e(count($customRole->getFunctionKeys())); ?> functions assigned
                                        </div>
                                        <?php if($customRole->description): ?>
                                            <div class="text-xs text-gray-400 mt-1"><?php echo e($customRole->description); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php if($customRoles->count() == 0): ?>
                            <div class="text-center py-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">No custom roles available.</p>
                                <a href="<?php echo e(route('app.custom-roles.create')); ?>" class="text-blue-600 text-xs hover:underline">Create a custom role</a>
                            </div>
                        <?php endif; ?>
                        <?php $__errorArgs = ['custom_role_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="space-y-6">
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏢 Depot Access</h3>
                        
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accessible Depots</label>
                            <div class="max-h-40 overflow-y-auto border rounded-md p-3 space-y-2">
                                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="depot_ids[]" value="<?php echo e($depot->id); ?>"
                                               class="border-gray-300 rounded"
                                               <?php echo e(in_array($depot->id, old('depot_ids', $user->depots->pluck('id')->toArray())) ? 'checked' : ''); ?>>
                                        <span class="ml-2 text-sm"><?php echo e($depot->name); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php $__errorArgs = ['depot_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-4">
                            <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-1">Default Depot</label>
                            <select name="depot_id" id="depot_id" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">No Default Depot</option>
                                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($depot->id); ?>" 
                                            <?php echo e($depot->id == old('depot_id', $user->depot_id) ? 'selected' : ''); ?>>
                                        <?php echo e($depot->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Primary depot shown on dashboards</p>
                            <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏭 Customer Access</h3>
                        
                        
                        <div class="mb-4">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Primary Customer <span class="text-xs text-gray-500">(customer role)</span>
                            </label>
                            <select name="customer_id" id="customer_id" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">No Primary Customer</option>
                                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($customer->id); ?>" 
                                            <?php echo e($customer->id == old('customer_id', $user->customer_id) ? 'selected' : ''); ?>>
                                        <?php echo e($customer->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Multiple Customers <span class="text-xs text-gray-500">(warehouse roles)</span>
                            </label>
                            <div class="max-h-32 overflow-y-auto border rounded-md p-3">
                                <?php $__currentLoopData = $customers->chunk(ceil($customers->count()/2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customerChunk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="space-y-1">
                                        <?php $__currentLoopData = $customerChunk; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="flex items-center text-xs">
                                                <input type="checkbox" name="customer_ids[]" value="<?php echo e($customer->id); ?>"
                                                       class="border-gray-300 rounded"
                                                       <?php echo e(in_array($customer->id, old('customer_ids', $user->customers->pluck('id')->toArray())) ? 'checked' : ''); ?>>
                                                <span class="ml-2"><?php echo e($customer->name); ?></span>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Leave empty to access ALL customers</p>
                        </div>
                    </div>
                </div>

                
                <div>
                    <div class="sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">📊 Function Summary</h3>
                        
                        
                        <div id="function-summary" class="space-y-2">
                            <?php
                                $userFunctions = $user->getFunctionKeys();
                                $totalFunctions = count(\App\Models\UserFunction::getAllFunctionKeys());
                            ?>
                            
                            <?php if($user->hasRole('admin')): ?>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="text-sm font-medium text-blue-900">Admin Access</div>
                                    <div class="text-xs text-blue-600">All <?php echo e($totalFunctions); ?> functions enabled</div>
                                </div>
                            <?php else: ?>
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e(count($userFunctions)); ?> of <?php echo e($totalFunctions); ?> functions assigned
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: <?php echo e($totalFunctions > 0 ? (count($userFunctions) / $totalFunctions * 100) : 0); ?>%"></div>
                                    </div>
                                </div>
                                
                                <?php if(count($userFunctions) > 0): ?>
                                    <div class="max-h-40 overflow-y-auto text-xs space-y-1">
                                        <?php
                                            $allFunctions = \App\Models\UserFunction::getAllFunctions();
                                            $categorizedFunctions = [];
                                            
                                            foreach($userFunctions as $functionKey) {
                                                foreach($allFunctions as $category => $functions) {
                                                    if(array_key_exists($functionKey, $functions)) {
                                                        $categorizedFunctions[$category][] = $functions[$functionKey];
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        
                                        <?php $__currentLoopData = $categorizedFunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $functions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="mb-2">
                                                <div class="font-medium text-gray-700"><?php echo e($category); ?></div>
                                                <?php $__currentLoopData = $functions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $function): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="text-gray-600 ml-2">• <?php echo e($function); ?></div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            
            <div id="function-assignment" class="mt-10 pt-8 border-t border-gray-200" style="display: none;">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">🔧 Individual Function Assignment</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="text-blue-600 mr-2">💡</div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Recommendation: Use Custom Roles Instead</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    Individual functions are only used when <strong>no custom roles</strong> are assigned. 
                                    If you assign custom roles, these individual functions will be ignored.
                                </p>
                                <p class="text-xs text-blue-600 mt-2">
                                    <strong>💡 Tip:</strong> Use the "Sync from Custom Roles" button below to copy functions from your selected custom roles to individual functions (useful for customization or viewing permissions).
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        Select specific functions only if not using custom roles above. 
                        <span class="font-medium">Admin users automatically have access to all functions.</span>
                    </p>
                </div>

                <?php
                    // Show all user functions (direct + from custom roles) for display
                    $directFunctions = $user->functions()->pluck('function_key')->toArray();
                    $allUserFunctions = $user->getFunctionKeys(); // Includes custom role functions
                    $userFunctions = old('function_keys', $directFunctions); // Form submission uses only direct functions
                    $displayFunctions = old('function_keys', $allUserFunctions); // Display shows all functions
                    $allFunctions = \App\Models\UserFunction::getAllFunctions();
                ?>

                
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php $__currentLoopData = $allFunctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $functions): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gray-50 rounded-lg border border-gray-200">
                            
                            <div class="px-4 py-3 bg-gray-100 border-b border-gray-200 rounded-t-lg">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           class="category-checkbox border-gray-300 rounded"
                                           data-category="<?php echo e(Str::slug($category)); ?>"
                                           onchange="toggleCategory('<?php echo e(Str::slug($category)); ?>')">
                                    <span class="ml-2 font-medium text-gray-900 text-sm"><?php echo e($category); ?></span>
                                    <span class="ml-auto text-xs text-gray-500">
                                        <?php echo e(count($functions)); ?> functions
                                    </span>
                                </label>
                            </div>

                            
                            <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                                <?php $__currentLoopData = $functions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-start cursor-pointer hover:bg-white rounded p-1 -m-1">
                                        <input type="checkbox" 
                                               name="function_keys[]" 
                                               value="<?php echo e($key); ?>"
                                               class="function-checkbox border-gray-300 rounded mt-0.5 flex-shrink-0"
                                               data-category="<?php echo e(Str::slug($category)); ?>"
                                               <?php echo e(in_array($key, $displayFunctions) ? 'checked' : ''); ?>

                                               onchange="updateCategoryCheckbox('<?php echo e(Str::slug($category)); ?>')">
                                        <div class="ml-2 flex-1">
                                            <span class="text-sm text-gray-900"><?php echo e($label); ?></span>
                                            <div class="text-xs text-gray-500 font-mono"><?php echo e($key); ?></div>
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
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-sm mt-2"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <a href="<?php echo e(route('app.users.index')); ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    ← Cancel
                </a>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="syncFromCustomRoles()" 
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                        📥 Sync from Custom Roles
                    </button>
                    <button type="button" onclick="selectAllFunctions()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Select All Functions
                    </button>
                    <button type="button" onclick="clearAllFunctions()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Clear All Functions
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        💾 Save User Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.category-checkbox:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.function-checkbox:checked {
    background-color: #10B981;
    border-color: #10B981;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    toggleFunctionSections();
    updateAllCategoryCheckboxes();
});

function toggleFunctionSections() {
    const functionSection = document.getElementById('function-assignment');
    const warehouseRoles = ['warehouse', 'depot-admin', 'site-admin'];
    const roleCheckboxes = document.querySelectorAll('input[name="role_ids[]"]');
    
    let hasWarehouseRole = false;
    let isAdmin = false;
    
    roleCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const roleName = checkbox.parentElement.textContent.trim().toLowerCase();
            if (warehouseRoles.some(role => roleName.includes(role))) {
                hasWarehouseRole = true;
            }
            if (roleName.includes('admin') && !roleName.includes('depot') && !roleName.includes('site')) {
                isAdmin = true;
            }
        }
    });
    
    // Show function section for warehouse roles (but not pure admin)
    functionSection.style.display = (hasWarehouseRole && !isAdmin) ? 'block' : 'none';
}

function toggleCategory(categorySlug) {
    const categoryCheckbox = document.querySelector(`input[data-category="${categorySlug}"].category-checkbox`);
    const functionCheckboxes = document.querySelectorAll(`input[data-category="${categorySlug}"].function-checkbox`);
    
    const isChecked = categoryCheckbox.checked;
    functionCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
}

function updateCategoryCheckbox(categorySlug) {
    const categoryCheckbox = document.querySelector(`input[data-category="${categorySlug}"].category-checkbox`);
    const functionCheckboxes = document.querySelectorAll(`input[data-category="${categorySlug}"].function-checkbox`);
    
    const checkedCount = Array.from(functionCheckboxes).filter(cb => cb.checked).length;
    const totalCount = functionCheckboxes.length;
    
    if (checkedCount === 0) {
        categoryCheckbox.checked = false;
        categoryCheckbox.indeterminate = false;
    } else if (checkedCount === totalCount) {
        categoryCheckbox.checked = true;
        categoryCheckbox.indeterminate = false;
    } else {
        categoryCheckbox.checked = false;
        categoryCheckbox.indeterminate = true;
    }
}

function updateAllCategoryCheckboxes() {
    const categories = new Set();
    document.querySelectorAll('.function-checkbox').forEach(checkbox => {
        categories.add(checkbox.dataset.category);
    });
    
    categories.forEach(category => {
        updateCategoryCheckbox(category);
    });
}

function selectAllFunctions() {
    document.querySelectorAll('.function-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.querySelectorAll('.category-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        checkbox.indeterminate = false;
    });
}

function clearAllFunctions() {
    document.querySelectorAll('.function-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('.category-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.indeterminate = false;
    });
}

function syncFromCustomRoles() {
    // Get all selected custom roles
    const selectedCustomRoles = [];
    document.querySelectorAll('input[name="custom_role_ids[]"]:checked').forEach(checkbox => {
        selectedCustomRoles.push(checkbox.value);
    });

    if (selectedCustomRoles.length === 0) {
        alert('Please select at least one custom role first.');
        return;
    }

    // Clear all current function selections
    clearAllFunctions();

    // Get functions from selected custom roles
    const customRoleFunctions = {
        <?php $__currentLoopData = $customRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        "<?php echo e($role->id); ?>": <?php echo json_encode($role->getFunctionKeys()); ?>,
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    };

    // Collect all functions from selected roles
    const functionsToSelect = new Set();
    selectedCustomRoles.forEach(roleId => {
        if (customRoleFunctions[roleId]) {
            customRoleFunctions[roleId].forEach(functionKey => {
                functionsToSelect.add(functionKey);
            });
        }
    });

    // Select the functions
    functionsToSelect.forEach(functionKey => {
        const checkbox = document.querySelector(`input[name="function_keys[]"][value="${functionKey}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    // Update category checkboxes
    updateAllCategoryCheckboxes();

    // Show confirmation message
    const count = functionsToSelect.size;
    const roleNames = selectedCustomRoles.map(roleId => {
        const roleCheckbox = document.querySelector(`input[name="custom_role_ids[]"][value="${roleId}"]`);
        const label = roleCheckbox ? roleCheckbox.closest('label').querySelector('span').textContent : roleId;
        return label;
    });

    alert(`Synced ${count} functions from custom role(s): ${roleNames.join(', ')}`);
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/users/edit_comprehensive.blade.php ENDPATH**/ ?>