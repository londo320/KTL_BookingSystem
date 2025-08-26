<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-7xl mx-auto">
    <?php if(session('success')): ?>
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium"><?php echo e(session('success')); ?></p>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-red-800 font-medium"><?php echo e(session('error')); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Custom Roles</h1>
                <p class="text-sm text-gray-600 mt-1">Create and manage dynamic roles with specific function permissions</p>
            </div>
            <div class="flex gap-2">
                <a href="<?php echo e(route('app.custom-roles.create-predefined')); ?>" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    📋 Create Predefined Roles
                </a>
                <a href="<?php echo e(route('app.custom-roles.create')); ?>" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    ➕ Create Custom Role
                </a>
            </div>
        </div>

        <div class="p-6">
            <?php if($roles->count() > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Role</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Description</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Functions</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Users</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                                <th class="text-left py-3 px-4 font-medium text-gray-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-900"><?php echo e($role->display_name); ?></div>
                                    <div class="text-xs text-gray-500 font-mono"><?php echo e($role->name); ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-gray-700 max-w-xs truncate">
                                        <?php echo e($role->description ?? 'No description'); ?>

                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo e(count($role->getFunctionKeys())); ?> functions
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <?php echo e($role->users_count); ?> users
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if($role->is_active): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Active
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ✗ Inactive
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex gap-2">
                                        <a href="<?php echo e(route('app.custom-roles.show', $role)); ?>" 
                                           class="text-blue-600 hover:text-blue-800 text-xs">
                                            👁️ View
                                        </a>
                                        <a href="<?php echo e(route('app.custom-roles.edit', $role)); ?>" 
                                           class="text-gray-600 hover:text-gray-800 text-xs">
                                            ✏️ Edit
                                        </a>
                                        <form method="POST" action="<?php echo e(route('app.custom-roles.toggle', $role)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-xs">
                                                <?php echo e($role->is_active ? '⏸️ Deactivate' : '▶️ Activate'); ?>

                                            </button>
                                        </form>
                                        <?php if($role->users_count == 0): ?>
                                        <form method="POST" action="<?php echo e(route('app.custom-roles.destroy', $role)); ?>" 
                                              class="inline" onsubmit="return confirm('Delete this role?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-xs">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    <?php echo e($roles->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="text-gray-400 text-6xl mb-4">👥</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Custom Roles</h3>
                    <p class="text-gray-600 mb-4">Create your first custom role to manage user permissions dynamically.</p>
                    <div class="flex justify-center gap-2">
                        <a href="<?php echo e(route('app.custom-roles.create-predefined')); ?>" 
                           class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Create Predefined Roles
                        </a>
                        <a href="<?php echo e(route('app.custom-roles.create')); ?>" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Create Custom Role
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/custom-roles/index.blade.php ENDPATH**/ ?>