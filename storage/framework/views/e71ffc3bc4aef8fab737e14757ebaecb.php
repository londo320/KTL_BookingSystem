<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Tipping Location Details</h2>
                <div class="text-sm text-gray-600 mt-1">
                    <p><?php echo e($tippingLocation->name); ?> - <?php echo e($tippingLocation->depot->name); ?></p>
                    <?php $canTakeAction = $tippingLocation->depot_id == $defaultDepotId; ?>
                    <div class="mt-1">
                        <?php if($canTakeAction): ?>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
                        <?php else: ?>
                            <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only - Actions Restricted</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('admin.tipping-locations.index')); ?>" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Locations
                </a>
                <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('admin.tipping-locations.edit', $tippingLocation)); ?>" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Edit Location
                    </a>
                <?php else: ?>
                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed"
                          title="Actions only available for your default depot">
                        Edit Location
                    </span>
                <?php endif; ?>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 max-w-6xl mx-auto">
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">📍 Location Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Name</label>
                        <p class="text-lg font-medium"><?php echo e($tippingLocation->name); ?></p>
                    </div>
                    
                    <?php if($tippingLocation->code): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Code</label>
                            <p class="text-lg font-mono"><?php echo e($tippingLocation->code); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Depot</label>
                        <p class="text-lg"><?php echo e($tippingLocation->depot->name); ?></p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Capacity</label>
                        <p class="text-lg"><?php echo e($tippingLocation->capacity); ?> vehicles</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Status</label>
                        <p class="text-lg">
                            <?php if($tippingLocation->is_active): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded">Active</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded">Inactive</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    
                    <?php if($tippingLocation->description): ?>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Description</label>
                            <p class="text-lg"><?php echo e($tippingLocation->description); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">📊 Current Status</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Occupancy</label>
                        <div class="mt-1 flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-4">
                                <?php 
                                    $occupancyPercent = $tippingLocation->capacity > 0 ? ($currentOccupancy / $tippingLocation->capacity) * 100 : 0;
                                ?>
                                <div class="bg-blue-500 h-4 rounded-full" style="width: <?php echo e($occupancyPercent); ?>%"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                <?php echo e($currentOccupancy); ?>/<?php echo e($tippingLocation->capacity); ?>

                            </span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Available Capacity</label>
                        <p class="text-2xl font-bold <?php echo e($availableCapacity > 0 ? 'text-green-600' : 'text-red-600'); ?>">
                            <?php echo e($availableCapacity); ?>

                        </p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600">Availability</label>
                        <p class="text-lg">
                            <?php if($tippingLocation->isAvailable()): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded">Available</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded">Full</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="mt-6 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Active Bookings</h3>
                <p class="text-sm text-gray-600 mt-1">Vehicles currently using this drop location</p>
            </div>
            
            <div class="p-6">
                <?php if($tippingLocation->activeBookings && $tippingLocation->activeBookings->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $tippingLocation->activeBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-medium text-gray-800"><?php echo e($booking->customer->name); ?></h4>
                                        <span class="text-gray-600">-</span>
                                        <span class="text-gray-600"><?php echo e($booking->booking_reference ?: '#' . $booking->id); ?></span>
                                    </div>
                                    
                                    <?php if($booking->reference): ?>
                                        <p class="text-sm text-gray-500 mt-1">Customer Ref: <?php echo e($booking->reference); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if($booking->container_number): ?>
                                        <p class="text-sm text-gray-500 mt-1">Container: <?php echo e($booking->container_number); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span>Slot: <?php echo e($booking->slot->start_at->format('D, d M Y - H:i')); ?></span>
                                        <?php if($booking->arrived_at): ?>
                                            <span class="ml-4">Arrived: <?php echo e($booking->arrived_at->format('H:i')); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <?php echo $booking->tipping_status_badge; ?>

                                    <?php $canManageBooking = $booking->slot->depot_id == $defaultDepotId; ?>
                                    <?php if($canManageBooking): ?>
                                        <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                                           class="text-blue-600 hover:text-blue-800 text-sm">
                                            Manage →
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 cursor-not-allowed text-sm"
                                              title="Actions only available for your default depot">
                                            Manage →
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <div class="text-6xl mb-4">🚛</div>
                        <h4 class="text-lg font-medium mb-2">No Active Bookings</h4>
                        <p class="text-sm">This location is currently empty and available for new arrivals.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-locations/show.blade.php ENDPATH**/ ?>