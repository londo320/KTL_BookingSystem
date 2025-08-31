
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
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Create Custom Role</h1>
            <p class="text-sm text-gray-600 mt-1">Define a new role with specific function permissions</p>
        </div>

        <form method="POST" action="<?php echo e(route('app.custom-roles.store')); ?>" class="p-6">
            <?php echo csrf_field(); ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">📝 Role Information</h2>
                        
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name (Slug)</label>
                            <input type="text" id="name" name="name" required
                                   value="<?php echo e(old('name')); ?>"
                                   placeholder="e.g., warehouse_supervisor"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Used internally (lowercase, underscores only)</p>
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
                            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                            <input type="text" id="display_name" name="display_name" required
                                   value="<?php echo e(old('display_name')); ?>"
                                   placeholder="e.g., Warehouse Supervisor"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Shown to users in interfaces</p>
                            <?php $__errorArgs = ['display_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-red-600 text-xs"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      placeholder="Describe what this role can do..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
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
                                <input type="checkbox" name="is_active" value="1" checked
                                       class="border-gray-300 rounded">
                                <span class="ml-2 text-sm">Role is active</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Inactive roles cannot be assigned to users</p>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">🔧 Function Permissions</h2>
                        <p class="text-sm text-gray-600">
                            Select the functions this role should have access to. Users assigned to this role will inherit all selected permissions.
                        </p>
                    </div>

                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
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
                                                   <?php echo e(in_array($key, old('function_keys', [])) ? 'checked' : ''); ?>

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
            </div>

            
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <a href="<?php echo e(route('app.custom-roles.index')); ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    ← Cancel
                </a>
                
                <div class="flex space-x-3">
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
                        💾 Create Role
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
    updateAllCategoryCheckboxes();
});

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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/custom-roles/create.blade.php ENDPATH**/ ?>