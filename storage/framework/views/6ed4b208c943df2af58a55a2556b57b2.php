<?php $__env->startSection('title', 'Trailer Type Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🚛 Trailer Type Management</h1>
                <p class="mt-2 text-gray-600">Manage trailer types and container classifications</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('admin.trailer-types.create')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    ➕ Add Trailer Type
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo e($stats['total']); ?></div>
            <div class="text-sm text-gray-600">📊 Total Types</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-green-600"><?php echo e($stats['active']); ?></div>
            <div class="text-sm text-gray-600">✅ Active</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-red-600"><?php echo e($stats['inactive']); ?></div>
            <div class="text-sm text-gray-600">❌ Inactive</div>
        </div>
        <?php if($showDeleted && isset($stats['deleted'])): ?>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-gray-600"><?php echo e($stats['deleted']); ?></div>
            <div class="text-sm text-gray-600">🗑️ Deleted</div>
        </div>
        <?php endif; ?>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-purple-600"><?php echo e($stats['with_bookings']); ?></div>
            <div class="text-sm text-gray-600">📦 With Bookings</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="p-4 border-b">
            <h3 class="text-lg font-medium text-gray-900">🔍 Filters</h3>
        </div>
        <form method="GET" class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                           placeholder="Search trailer types..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Active</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                        <option value="deleted" <?php echo e(request('status') === 'deleted' ? 'selected' : ''); ?>>Deleted</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" name="show_deleted" value="1" 
                               <?php echo e($showDeleted ? 'checked' : ''); ?>

                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Show deleted</span>
                    </label>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Apply Filters
                    </button>
                    <a href="<?php echo e(route('admin.trailer-types.index')); ?>" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $trailerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailerType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 <?php echo e($trailerType->trashed() ? 'bg-red-50' : ''); ?>">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo e($trailerType->name); ?>

                                    <?php if($trailerType->trashed()): ?>
                                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Deleted
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900"><?php echo e($trailerType->description ?: '-'); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if($trailerType->trashed()): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    🗑️ Deleted
                                </span>
                            <?php elseif($trailerType->is_active): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✅ Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ❌ Inactive
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <a href="<?php echo e(route('admin.trailer-types.show', $trailerType)); ?>" 
                               class="text-blue-600 hover:text-blue-900">
                                <?php echo e($trailerType->bookings_count); ?> bookings
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo e($trailerType->created_at->format('M j, Y')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <?php if($trailerType->trashed()): ?>
                                    <form action="<?php echo e(route('admin.trailer-types.restore', $trailerType->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            ♻️ Restore
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.trailer-types.edit', $trailerType)); ?>" 
                                       class="text-blue-600 hover:text-blue-900">
                                        ✏️ Edit
                                    </a>
                                    
                                    <form action="<?php echo e(route('admin.trailer-types.toggle', $trailerType->id)); ?>" method="POST" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="text-amber-600 hover:text-amber-900">
                                            <?php if($trailerType->is_active): ?>
                                                ⏸️ Deactivate
                                            <?php else: ?>
                                                ▶️ Activate
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                    
                                    <?php if($trailerType->canBeDeleted()): ?>
                                        <form action="<?php echo e(route('admin.trailer-types.destroy', $trailerType)); ?>" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this trailer type?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                🗑️ Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400" title="Cannot delete: has associated bookings">
                                            🔒 Protected
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                <p class="text-lg font-medium text-gray-900 mb-2">No trailer types found</p>
                                <p class="text-gray-500 mb-4">Get started by creating your first trailer type.</p>
                                <a href="<?php echo e(route('admin.trailer-types.create')); ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                    ➕ Add Trailer Type
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if($trailerTypes->hasPages()): ?>
    <div class="mt-6">
        <?php echo e($trailerTypes->withQueryString()->links()); ?>

    </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/trailer-types/index.blade.php ENDPATH**/ ?>