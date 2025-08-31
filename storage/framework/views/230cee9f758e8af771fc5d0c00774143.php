
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

    
    <form method="POST" action="<?php echo e(route('app.users.update', $user->id)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="flex flex-col mb-4">
            <label for="name" class="text-sm font-medium">Name</label>
            <input type="text" id="name" name="name" class="border rounded px-3 py-2 mt-1" value="<?php echo e(old('name', $user->name)); ?>" required>
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

        
        <div class="flex flex-col mb-4">
            <label for="email" class="text-sm font-medium">Email</label>
            <input type="email" id="email" name="email" class="border rounded px-3 py-2 mt-1" value="<?php echo e(old('email', $user->email)); ?>" required>
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




<div class="flex flex-col mb-4">
    <label class="text-sm font-medium">Assign Roles</label>
    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center mt-2">
            <input
                type="checkbox"
                id="role_<?php echo e($role->id); ?>"
                name="role_ids[]"
                value="<?php echo e($role->id); ?>"
                class="mr-2"
                <?php echo e(in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : ''); ?>

            >
            <label for="role_<?php echo e($role->id); ?>" class="text-sm"><?php echo e($role->name); ?></label>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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


        
        <div class="flex flex-col mb-4">
            <label for="depot_ids" class="text-sm font-medium">Assign Depots</label>
            <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center mt-2">
                    <input type="checkbox" id="depot_<?php echo e($depot->id); ?>" name="depot_ids[]" value="<?php echo e($depot->id); ?>" class="mr-2"
                    <?php echo e(in_array($depot->id, old('depot_ids', $user->depots->pluck('id')->toArray())) ? 'checked' : ''); ?>>
                    <label for="depot_<?php echo e($depot->id); ?>" class="text-sm"><?php echo e($depot->name); ?></label>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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

        
        <div class="flex flex-col mb-4">
            <label for="depot_id" class="text-sm font-medium">Default Depot</label>
            <select name="depot_id" id="depot_id" class="border rounded px-3 py-2 mt-1 w-full">
                <option value="">— No Default Depot —</option>
                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($depot->id); ?>" 
                        <?php if($depot->id == old('depot_id', $user->depot_id)): echo 'selected'; endif; ?>>
                        <?php echo e($depot->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">
                This depot will be shown by default on operational dashboards. 
                User must also have access to this depot above.
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

        
        <div class="flex flex-col mb-4">
            <label class="text-sm font-medium mb-2">Customer Assignment</label>
            
            
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-600">Legacy Customer (for customer role)</label>
                <select name="customer_id" class="border rounded px-3 py-2 mt-1 w-full">
                    <option value="">— No Legacy Customer —</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($customer->id); ?>" 
                            <?php if($customer->id == old('customer_id', $user->customer_id)): echo 'selected'; endif; ?>>
                            <?php echo e($customer->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">Only used for users with customer role</p>
                <?php $__errorArgs = ['customer_id'];
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

            
            <div class="mb-3">
                <label class="block text-sm font-medium">Multiple Customers (for admin/site roles)</label>
                <div class="mt-2 space-y-2 max-h-40 overflow-y-auto border rounded p-2">
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="inline-flex items-center w-full">
                            <input
                                type="checkbox"
                                name="customer_ids[]"
                                value="<?php echo e($customer->id); ?>"
                                class="border-gray-300 rounded"
                                <?php echo e(in_array($customer->id, old('customer_ids', $user->customers->pluck('id')->toArray())) ? 'checked' : ''); ?>

                            >
                            <span class="ml-2 text-sm"><?php echo e($customer->name); ?></span>
                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    Leave empty for admin/site roles to see ALL customers (including future ones).
                    Select specific customers to limit access.
                </p>
                <?php $__errorArgs = ['customer_ids'];
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
        </div>

        
        <div class="flex items-center mb-4">
            <input type="checkbox" name="reset_password" id="reset_password" class="mr-2">
            <label for="reset_password" class="text-sm">Reset to Default Password</label>
        </div>

        
        <div class="flex justify-end mt-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
        </div>
    </form>
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
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/users/edit.blade.php ENDPATH**/ ?>