

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
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🚛 Carrier Management</h1>
                <p class="mt-2 text-gray-600">Manage carriers, depot configurations, and merge duplicates</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('app.carriers.merge.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    🔄 Merge Carriers
                </a>
                <a href="<?php echo e(route('app.carriers.create')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    ➕ Add Carrier
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo e($stats['total']); ?></div>
            <div class="text-sm text-gray-600">📊 Total Carriers</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-green-600"><?php echo e($stats['active']); ?></div>
            <div class="text-sm text-gray-600">✅ Active</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-red-600"><?php echo e($stats['inactive']); ?></div>
            <div class="text-sm text-gray-600">❌ Inactive</div>
        </div>
        <?php if(isset($stats['merged'])): ?>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-orange-600"><?php echo e($stats['merged']); ?></div>
            <div class="text-sm text-gray-600">🔄 Merged</div>
        </div>
        <?php endif; ?>
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

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-6">
            <!-- Show Deleted Toggle -->
            <div class="mb-4 flex items-center justify-between">
                <div class="flex items-center">
                    <label class="flex items-center">
                        <input type="checkbox" id="show-deleted-toggle" <?php echo e($showDeleted ? 'checked' : ''); ?>

                               class="rounded text-blue-600">
                        <span class="ml-2 text-sm font-medium text-gray-700">
                            🗑️ Show deleted/merged carriers
                        </span>
                    </label>
                </div>
                <?php if($showDeleted): ?>
                <div class="text-xs text-gray-500 bg-yellow-50 px-3 py-1 rounded">
                    ⚠️ Viewing all carriers including deleted ones
                </div>
                <?php endif; ?>
            </div>

            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="show_deleted" value="<?php echo e($showDeleted ? '1' : '0'); ?>">
                
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Carriers</label>
                    <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>"
                           placeholder="Search by name..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>✅ Active</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>❌ Inactive</option>
                        <option value="merged" <?php echo e(request('status') === 'merged' ? 'selected' : ''); ?>>🔄 Merged</option>
                        <?php if($showDeleted): ?>
                        <option value="deleted" <?php echo e(request('status') === 'deleted' ? 'selected' : ''); ?>>🗑️ Deleted</option>
                        <?php endif; ?>
                        <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>⏳ Pending Approval</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="name" <?php echo e(request('sort') === 'name' ? 'selected' : ''); ?>>Name</option>
                        <option value="bookings_count" <?php echo e(request('sort') === 'bookings_count' ? 'selected' : ''); ?>>Bookings Count</option>
                        <option value="last_used_at" <?php echo e(request('sort') === 'last_used_at' ? 'selected' : ''); ?>>Last Used</option>
                        <option value="created_at" <?php echo e(request('sort') === 'created_at' ? 'selected' : ''); ?>>Created Date</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        🔍 Search
                    </button>
                    <a href="<?php echo e(route('app.carriers.index')); ?>" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg transition-colors">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-4">
            <form id="bulk-form" method="POST" action="<?php echo e(route('app.carriers.bulk-action')); ?>">
                <?php echo csrf_field(); ?>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all" class="rounded">
                            <span class="ml-2 text-sm font-medium">Select All</span>
                        </label>
                        
                        <select name="action" id="bulk-action" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Choose Action...</option>
                            <option value="activate">✅ Activate Selected</option>
                            <option value="deactivate">❌ Deactivate Selected</option>
                            <option value="delete">🗑️ Delete Selected</option>
                        </select>
                        
                        <button type="submit" id="bulk-submit" disabled 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:bg-gray-400 text-sm">
                            Apply Action
                        </button>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="button" onclick="window.location.href='<?php echo e(route('app.carriers.cleanup')); ?>'"
                                class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors text-sm">
                            🧹 Auto Cleanup
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Carriers Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="overflow-hidden">
            <?php if($carriers->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded" disabled>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Carrier Details
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Bookings
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Depots
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Used
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 <?php echo e($carrier->trashed() ? 'bg-red-50' : ($carrier->is_active ? '' : 'bg-gray-50')); ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="carrier_ids[]" value="<?php echo e($carrier->id); ?>" 
                                       class="carrier-checkbox rounded" form="bulk-form">
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo e($carrier->name); ?>

                                            <?php if($carrier->trashed()): ?>
                                                <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded">DELETED</span>
                                            <?php endif; ?>
                                            <?php if($carrier->requires_approval): ?>
                                                <span class="ml-2 px-2 py-1 text-xs bg-amber-100 text-amber-800 rounded">PENDING</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($carrier->contact_email): ?>
                                            <div class="text-sm text-gray-500">📧 <?php echo e($carrier->contact_email); ?></div>
                                        <?php endif; ?>
                                        <?php if($carrier->contact_phone): ?>
                                            <div class="text-sm text-gray-500">📞 <?php echo e($carrier->contact_phone); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if($carrier->trashed()): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        🗑️ Deleted
                                    </span>
                                <?php elseif($carrier->is_active): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ✅ Active
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        ❌ Inactive
                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    📦 <?php echo e($carrier->bookings_count); ?>

                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    🏢 <?php echo e($carrier->depots->count()); ?>

                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                <?php echo e($carrier->last_used_at ? $carrier->last_used_at->diffForHumans() : 'Never'); ?>

                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('app.carriers.show', $carrier)); ?>" 
                                       class="inline-flex items-center px-2 py-1 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded text-xs font-medium transition-colors">
                                        👁️ View
                                    </a>
                                    
                                    <?php if(!$carrier->trashed()): ?>
                                        <a href="<?php echo e(route('app.carriers.edit', $carrier)); ?>" 
                                           class="inline-flex items-center px-2 py-1 border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 rounded text-xs font-medium transition-colors">
                                            ✏️ Edit
                                        </a>
                                        
                                        <button onclick="toggleCarrier(<?php echo e($carrier->id); ?>)" 
                                                class="inline-flex items-center px-2 py-1 border <?php echo e($carrier->is_active ? 'border-red-300 text-red-700 bg-red-50 hover:bg-red-100' : 'border-green-300 text-green-700 bg-green-50 hover:bg-green-100'); ?> rounded text-xs font-medium transition-colors">
                                            <?php echo e($carrier->is_active ? '❌' : '✅'); ?> <?php echo e($carrier->is_active ? 'Deactivate' : 'Activate'); ?>

                                        </button>
                                        
                                        <?php if($carrier->bookings_count === 0): ?>
                                        <button onclick="deleteCarrier(<?php echo e($carrier->id); ?>)" 
                                                class="inline-flex items-center px-2 py-1 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 rounded text-xs font-medium transition-colors">
                                            🗑️ Delete
                                        </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button onclick="restoreCarrier(<?php echo e($carrier->id); ?>)" 
                                                class="inline-flex items-center px-2 py-1 border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 rounded text-xs font-medium transition-colors">
                                            🔄 Restore
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <?php echo e($carriers->appends(request()->query())->links()); ?>

            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">🚛</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Carriers Found</h3>
                <p class="text-gray-600">No carriers match your current filters.</p>
                <a href="<?php echo e(route('app.carriers.create')); ?>" 
                   class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    ➕ Add First Carrier
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Show deleted toggle functionality
document.getElementById('show-deleted-toggle').addEventListener('change', function() {
    const currentUrl = new URL(window.location);
    if (this.checked) {
        currentUrl.searchParams.set('show_deleted', '1');
    } else {
        currentUrl.searchParams.delete('show_deleted');
        // Also clear deleted status filter if toggling off
        if (currentUrl.searchParams.get('status') === 'deleted') {
            currentUrl.searchParams.delete('status');
        }
    }
    window.location.href = currentUrl.toString();
});

// Select all functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.carrier-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    updateBulkSubmitButton();
});

// Individual checkbox change
document.querySelectorAll('.carrier-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateBulkSubmitButton);
});

// Bulk action change
document.getElementById('bulk-action').addEventListener('change', updateBulkSubmitButton);

function updateBulkSubmitButton() {
    const checkedBoxes = document.querySelectorAll('.carrier-checkbox:checked');
    const action = document.getElementById('bulk-action').value;
    const submitButton = document.getElementById('bulk-submit');
    
    submitButton.disabled = checkedBoxes.length === 0 || !action;
}

// Bulk form submission with confirmation
document.getElementById('bulk-form').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.carrier-checkbox:checked');
    const action = document.getElementById('bulk-action').value;
    
    let message = `Are you sure you want to ${action} ${checkedBoxes.length} carrier(s)?`;
    
    if (action === 'delete') {
        message += '\n\nNote: Only carriers without existing bookings will be deleted. Carriers with bookings will be skipped.';
    }
    
    if (!confirm(message)) {
        e.preventDefault();
    }
});

function toggleCarrier(carrierId) {
    if (confirm('Are you sure you want to toggle this carrier\'s status?')) {
        fetch(`/admin/carriers/${carrierId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the carrier.');
        });
    }
}

function restoreCarrier(carrierId) {
    if (confirm('Are you sure you want to restore this carrier?')) {
        fetch(`/admin/carriers/${carrierId}/restore`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            }
        })
        .then(response => {
            location.reload();
        });
    }
}

function deleteCarrier(carrierId) {
    if (confirm('Are you sure you want to permanently delete this carrier? This action cannot be undone.')) {
        fetch(`/admin/carriers/${carrierId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                response.json().then(data => {
                    alert(data.message || 'An error occurred while deleting the carrier.');
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the carrier.');
        });
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/carriers/index.blade.php ENDPATH**/ ?>