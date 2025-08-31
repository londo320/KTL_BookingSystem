

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
                <h1 class="text-3xl font-bold text-gray-900">
                    🚛 <?php echo e($carrier->name); ?>

                    <?php if($carrier->trashed()): ?>
                        <span class="ml-2 px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded">DELETED</span>
                    <?php elseif(!$carrier->is_active && str_contains($carrier->name, '(MERGED INTO:')): ?>
                        <span class="ml-2 px-3 py-1 text-sm bg-orange-100 text-orange-800 rounded">MERGED</span>
                    <?php elseif(!$carrier->is_active): ?>
                        <span class="ml-2 px-3 py-1 text-sm bg-red-100 text-red-800 rounded">Inactive</span>
                    <?php endif; ?>
                    <?php if($carrier->requires_approval): ?>
                        <span class="ml-2 px-3 py-1 text-sm bg-amber-100 text-amber-800 rounded">Pending Approval</span>
                    <?php endif; ?>
                </h1>
                <p class="mt-2 text-gray-600">Carrier details and booking history</p>
                <?php if($carrier->trashed()): ?>
                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            ⚠️ <strong>This carrier has been deleted.</strong> Some actions may be limited. You can restore it if needed.
                        </p>
                    </div>
                <?php elseif(!$carrier->is_active && str_contains($carrier->name, '(MERGED INTO:')): ?>
                    <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <p class="text-sm text-orange-800">
                            🔄 <strong>This carrier has been merged into another carrier.</strong> Historical data is preserved for audit purposes.
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('app.carriers.edit', $carrier)); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    ✏️ Edit
                </a>
                <a href="<?php echo e(route('app.carriers.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Carriers
                </a>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-blue-600"><?php echo e($carrier->bookings()->count()); ?></div>
            <div class="text-sm text-gray-600">📦 Total Bookings</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-green-600"><?php echo e($bookingsByDepot->count()); ?></div>
            <div class="text-sm text-gray-600">🏢 Depots Used</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-purple-600"><?php echo e($carrier->depots()->count()); ?></div>
            <div class="text-sm text-gray-600">⚙️ Configured Depots</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">
                <?php echo e($carrier->last_used_at ? $carrier->last_used_at->diffInDays(now()) : '∞'); ?>

            </div>
            <div class="text-sm text-gray-600">📅 Days Since Used</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold <?php echo e($carrier->is_active ? 'text-green-600' : 'text-red-600'); ?>">
                <?php echo e($carrier->is_active ? 'Active' : 'Inactive'); ?>

            </div>
            <div class="text-sm text-gray-600">🔴 Status</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📞 Contact Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1 text-gray-900">
                                <?php echo e($carrier->contact_email ?: 'Not provided'); ?>

                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <div class="mt-1 text-gray-900">
                                <?php echo e($carrier->contact_phone ?: 'Not provided'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Depot Configuration -->
            <?php if($carrier->depots()->count() > 0): ?>
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🏢 Depot Configuration</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php $__currentLoopData = $carrier->depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 border rounded-lg <?php echo e($depot->pivot->is_enabled ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'); ?>">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900"><?php echo e($depot->name); ?></h3>
                                <span class="px-2 py-1 text-xs rounded <?php echo e($depot->pivot->is_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'); ?>">
                                    <?php echo e($depot->pivot->is_enabled ? 'Enabled' : 'Disabled'); ?>

                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Auto-disable:</span>
                                    <span class="font-medium">
                                        <?php echo e($depot->pivot->auto_disable_unused ? 'After ' . $depot->pivot->auto_disable_months . ' months' : 'Disabled'); ?>

                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Customer restrictions:</span>
                                    <span class="font-medium">
                                        <?php
                                            $allowedCustomerIds = json_decode($depot->pivot->allowed_customer_ids, true);
                                        ?>
                                        <?php echo e($allowedCustomerIds ? count($allowedCustomerIds) . ' customers' : 'All customers'); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Booking History by Depot -->
            <?php if($bookingsByDepot->count() > 0): ?>
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📊 Bookings by Depot</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $bookingsByDepot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="font-medium text-blue-900"><?php echo e($depot->name); ?></div>
                            <div class="text-2xl font-bold text-blue-600"><?php echo e($depot->count); ?></div>
                            <div class="text-sm text-blue-700">bookings</div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Bookings -->
            <?php if($recentBookings->count() > 0): ?>
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📋 Recent Bookings</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Depot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php $__currentLoopData = $recentBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php echo e($booking->created_at->format('M j, Y')); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php echo e($booking->slot->depot->name); ?>

                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?php echo e($booking->customer->name ?? 'N/A'); ?>

                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-900">
                                    <?php echo e($booking->booking_reference); ?>

                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View →
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">⚡ Quick Actions</h2>
                </div>
                <div class="p-4 space-y-3">
                    <?php if($carrier->trashed()): ?>
                        <form action="<?php echo e(route('app.carriers.restore', $carrier->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                🔄 Restore Carrier
                            </button>
                        </form>
                    <?php else: ?>
                        <button onclick="toggleCarrier(<?php echo e($carrier->id); ?>)" 
                                class="w-full px-4 py-2 <?php echo e($carrier->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'); ?> text-white rounded-lg transition-colors">
                            <?php echo e($carrier->is_active ? '❌ Deactivate' : '✅ Activate'); ?>

                        </button>
                        
                        <a href="<?php echo e(route('app.carriers.edit', $carrier)); ?>" 
                           class="w-full inline-block text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                            ✏️ Edit Details
                        </a>
                        
                        <?php if($carrier->bookings()->count() === 0): ?>
                        <form action="<?php echo e(route('app.carriers.destroy', $carrier)); ?>" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this carrier? This action cannot be undone.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                🗑️ Delete Carrier
                            </button>
                        </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Merge History -->
            <?php if($carrier->mergesAsSource->count() > 0 || $carrier->mergesAsTarget->count() > 0): ?>
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">🔄 Merge History</h2>
                </div>
                <div class="p-4 space-y-3">
                    <?php $__currentLoopData = $carrier->mergesAsTarget; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $merge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 bg-green-50 rounded border text-sm">
                        <div class="font-medium text-green-800">Merged from: <?php echo e($merge->source_carrier_name); ?></div>
                        <div class="text-green-600"><?php echo e($merge->created_at->diffForHumans()); ?></div>
                        <div class="text-green-600"><?php echo e($merge->bookings_moved); ?> bookings moved</div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php $__currentLoopData = $carrier->mergesAsSource; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $merge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 bg-blue-50 rounded border text-sm">
                        <div class="font-medium text-blue-800">Merged into: <?php echo e($merge->target_carrier_name); ?></div>
                        <div class="text-blue-600"><?php echo e($merge->created_at->diffForHumans()); ?></div>
                        <div class="text-blue-600"><?php echo e($merge->bookings_moved); ?> bookings moved</div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📝 Metadata</h2>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium"><?php echo e($carrier->created_at->format('M j, Y H:i')); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="font-medium"><?php echo e($carrier->updated_at->format('M j, Y H:i')); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Used:</span>
                        <span class="font-medium"><?php echo e($carrier->last_used_at ? $carrier->last_used_at->format('M j, Y H:i') : 'Never'); ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600">ID:</span>
                        <span class="font-mono"><?php echo e($carrier->id); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/carriers/show.blade.php ENDPATH**/ ?>