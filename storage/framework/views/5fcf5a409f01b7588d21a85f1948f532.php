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
                <h2 class="font-semibold text-xl text-gray-800">Tipping Operations Dashboard</h2>
                <p class="text-sm text-gray-600 mt-1">Real-time view of all tipping activities</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex items-center space-x-2">
                    <select name="depot_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
                                <?php echo e($depot->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 max-w-7xl mx-auto">
        
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="text-sm font-semibold text-blue-800 mb-2">📋 Workflow Priority Guide</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full">Tipping Active</span>
                    <span class="text-gray-600">Highest - Currently unloading</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Ready to Tip</span>
                    <span class="text-gray-600">High - At bay, ready to start</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">Bay Needs Clearing</span>
                    <span class="text-gray-600">Medium - Empty, clear bay</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full">Standard</span>
                    <span class="text-gray-600">Normal - In queue/location</span>
                </div>
            </div>
        </div>

        <?php $__currentLoopData = $tippingData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-8 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-xl font-semibold text-gray-800">🏭 <?php echo e($data['depot']->name); ?></h3>
                    
                    
                    <div class="mt-4 grid grid-cols-2 md:grid-cols-7 gap-3">
                        <div class="text-center p-3 bg-gray-100 rounded">
                            <div class="text-xl font-bold text-gray-600"><?php echo e($data['stats']['not_started']); ?></div>
                            <div class="text-xs text-gray-500">⏳ Not Started</div>
                        </div>
                        <div class="text-center p-3 bg-blue-100 rounded">
                            <div class="text-xl font-bold text-blue-600"><?php echo e($data['stats']['in_location'] ?? 0); ?></div>
                            <div class="text-xs text-blue-500">🚛 In Location</div>
                        </div>
                        <div class="text-center p-3 bg-red-100 rounded">
                            <div class="text-xl font-bold text-red-600"><?php echo e($data['stats']['trailer_dropped']); ?></div>
                            <div class="text-xs text-red-500">📍 Trailer Dropped</div>
                        </div>
                        <div class="text-center p-3 bg-yellow-100 rounded">
                            <div class="text-xl font-bold text-yellow-600"><?php echo e($data['stats']['moved_to_bay']); ?></div>
                            <div class="text-xs text-yellow-500">🚛 At Bay</div>
                        </div>
                        <div class="text-center p-3 bg-orange-100 rounded">
                            <div class="text-xl font-bold text-orange-600"><?php echo e($data['stats']['tipping_in_progress']); ?></div>
                            <div class="text-xs text-orange-500">⚡ Tipping</div>
                        </div>
                        <div class="text-center p-3 bg-green-100 rounded">
                            <div class="text-xl font-bold text-green-600"><?php echo e($data['stats']['tipping_completed']); ?></div>
                            <div class="text-xs text-green-500">✅ Empty</div>
                        </div>
                        <div class="text-center p-3 bg-purple-100 rounded">
                            <div class="text-xl font-bold text-purple-600"><?php echo e($data['stats']['trailer_departed']); ?></div>
                            <div class="text-xs text-purple-500">🏁 Departed</div>
                        </div>
                    </div>
                    
                    
                    <?php if($data['stats']['factory_bookings']['total'] > 0): ?>
                        <div class="mt-4 p-4 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-orange-800">🏭 Factory Deliveries (Ad-hoc Arrivals)</h4>
                                <a href="<?php echo e(route('admin.factory-bookings.index')); ?>?depot_id=<?php echo e($data['depot']->id); ?>" 
                                   class="text-xs text-orange-600 hover:text-orange-800">View All →</a>
                            </div>
                            <div class="grid grid-cols-4 gap-3">
                                <div class="text-center p-2 bg-white rounded border">
                                    <div class="text-lg font-bold text-gray-700"><?php echo e($data['stats']['factory_bookings']['total']); ?></div>
                                    <div class="text-xs text-gray-600">Total</div>
                                </div>
                                <div class="text-center p-2 bg-yellow-100 rounded border">
                                    <div class="text-lg font-bold text-yellow-700"><?php echo e($data['stats']['factory_bookings']['arrived']); ?></div>
                                    <div class="text-xs text-yellow-600">Arrived</div>
                                </div>
                                <div class="text-center p-2 bg-blue-100 rounded border">
                                    <div class="text-lg font-bold text-blue-700"><?php echo e($data['stats']['factory_bookings']['processing']); ?></div>
                                    <div class="text-xs text-blue-600">Processing</div>
                                </div>
                                <div class="text-center p-2 bg-green-100 rounded border">
                                    <div class="text-lg font-bold text-green-700"><?php echo e($data['stats']['factory_bookings']['completed']); ?></div>
                                    <div class="text-xs text-green-600">Completed</div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <div>
                            <h4 class="text-lg font-medium text-gray-800 mb-4">📍 Drop Locations</h4>
                            
                            <?php if($data['locations']->isEmpty()): ?>
                                <div class="text-center py-8 text-gray-500">
                                    <p>No drop locations configured for this depot.</p>
                                    <?php if(auth()->user()->hasRole('admin')): ?>
                                        <a href="<?php echo e(route('admin.tipping-locations.create')); ?>" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                            + Add Drop Location
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $data['locations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div class="flex-1">
                                                <h5 class="font-medium text-gray-800"><?php echo e($location->name); ?></h5>
                                                <?php if($location->code): ?>
                                                    <p class="text-sm text-gray-600">Code: <?php echo e($location->code); ?></p>
                                                <?php endif; ?>
                                                
                                                
                                                <div class="mt-2 flex items-center space-x-2">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                        <?php 
                                                            $occupancyPercent = $location->capacity > 0 ? ($location->current_occupancy / $location->capacity) * 100 : 0;
                                                        ?>
                                                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo e($occupancyPercent); ?>%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-600">
                                                        <?php echo e($location->getCurrentOccupancy()); ?>/<?php echo e($location->capacity); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="ml-4">
                                                <?php if($location->isAvailable()): ?>
                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Available</span>
                                                <?php else: ?>
                                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Full</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        
                                        <?php if($location->activeBookings->isNotEmpty()): ?>
                                            <div class="ml-4 space-y-2">
                                                <?php $__currentLoopData = $location->activeBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="flex items-center justify-between p-2 bg-blue-50 rounded text-sm">
                                                        <div>
                                                            <span class="font-medium"><?php echo e($booking->customer->name); ?></span>
                                                            <span class="text-gray-600">- <?php echo e($booking->booking_reference ?: '#' . $booking->id); ?></span>
                                                            <?php if($booking->reference): ?>
                                                                <br><span class="text-xs text-gray-500">Ref: <?php echo e($booking->reference); ?></span>
                                                            <?php endif; ?>
                                                            <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                                                                <br><span class="text-xs text-blue-600">📦 <?php echo e($booking->poNumbers->count()); ?> PO(s): <?php echo e($booking->poNumbers->pluck('po_number')->join(', ')); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <?php echo $booking->tipping_status_badge; ?>

                                                            <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                                                                <a href="<?php echo e(route('admin.factory-booking-workflow.show', $booking)); ?>" 
                                                                   class="text-blue-600 hover:text-blue-800">
                                                                    Manage →
                                                                </a>
                                                            <?php else: ?>
                                                                <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                                                                   class="text-blue-600 hover:text-blue-800">
                                                                    Manage →
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div>
                            <h4 class="text-lg font-medium text-gray-800 mb-4">🚛 Tipping Bays</h4>
                            
                            <?php if($data['bays']->isEmpty()): ?>
                                <div class="text-center py-8 text-gray-500">
                                    <p>No tipping bays configured for this depot.</p>
                                    <?php if(auth()->user()->hasRole('admin')): ?>
                                        <a href="<?php echo e(route('admin.tipping-bays.create')); ?>" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                            + Add Tipping Bay
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="space-y-3">
                                    <?php $__currentLoopData = $data['bays']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <h5 class="font-medium text-gray-800"><?php echo e($bay->name); ?></h5>
                                                    <?php if($bay->code): ?>
                                                        <span class="text-sm text-gray-600">(<?php echo e($bay->code); ?>)</span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if(!empty($bay->equipment)): ?>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        Equipment: <?php echo e(implode(', ', $bay->equipment)); ?>

                                                    </p>
                                                <?php endif; ?>

                                                
                                                <?php if($bay->currentBooking): ?>
                                                    <div class="mt-2 p-2 bg-yellow-50 rounded text-sm">
                                                        <div>
                                                            <strong><?php echo e($bay->currentBooking->customer->name); ?></strong> 
                                                            - <?php echo e($bay->currentBooking->booking_reference ?: '#' . $bay->currentBooking->id); ?>

                                                            <?php if($bay->currentBooking->reference): ?>
                                                                <br><span class="text-xs text-gray-500">Ref: <?php echo e($bay->currentBooking->reference); ?></span>
                                                            <?php endif; ?>
                                                            <?php if($bay->currentBooking->poNumbers && $bay->currentBooking->poNumbers->count() > 0): ?>
                                                                <br><span class="text-xs text-blue-600">📦 <?php echo e($bay->currentBooking->poNumbers->count()); ?> PO(s): <?php echo e($bay->currentBooking->poNumbers->pluck('po_number')->join(', ')); ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="mt-1 flex items-center justify-between">
                                                            <div><?php echo $bay->currentBooking->tipping_status_badge; ?></div>
                                                            <?php
                                                                $currentMovement = $bay->currentBooking->movements->where('tipping_bay_id', $bay->id)->first();
                                                                $timeInBay = $currentMovement ? $currentMovement->getTimeInBay() : null;
                                                                $timeInStatus = $currentMovement ? $currentMovement->getTimeInCurrentStatus() : null;
                                                            ?>
                                                            <div class="text-xs text-gray-600">
                                                                <?php if($timeInBay): ?>
                                                                    <div>⏱️ Time in Bay: <?php echo e($timeInBay); ?></div>
                                                                <?php endif; ?>
                                                                <?php if($timeInStatus && $timeInStatus !== $timeInBay): ?>
                                                                    <div>🔄 In Status: <?php echo e($timeInStatus); ?></div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <?php if($currentMovement): ?>
                                                            <?php
                                                                $priority = 'low';
                                                                $priorityText = 'Standard';
                                                                $priorityClass = 'bg-gray-100 text-gray-600';
                                                                
                                                                if ($currentMovement->current_status === 'unloading') {
                                                                    $priority = 'high';
                                                                    $priorityText = 'Tipping Active';
                                                                    $priorityClass = 'bg-orange-100 text-orange-800';
                                                                } elseif ($currentMovement->current_status === 'at_bay') {
                                                                    $priority = 'medium';
                                                                    $priorityText = 'Ready to Tip';
                                                                    $priorityClass = 'bg-yellow-100 text-yellow-800';
                                                                } elseif ($currentMovement->current_status === 'empty') {
                                                                    $priority = 'medium';
                                                                    $priorityText = 'Bay Needs Clearing';
                                                                    $priorityClass = 'bg-green-100 text-green-800';
                                                                }
                                                            ?>
                                                            <div class="mt-1">
                                                                <span class="px-2 py-1 text-xs <?php echo e($priorityClass); ?> rounded-full">
                                                                    <?php echo e($priorityText); ?>

                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="ml-4 flex flex-col items-end space-y-2">
                                                <?php echo $bay->status_badge; ?>

                                                
                                                <?php if($bay->currentBooking): ?>
                                                    <?php if($bay->currentBooking instanceof \App\Models\FactoryBooking): ?>
                                                        <a href="<?php echo e(route('admin.factory-booking-workflow.show', $bay->currentBooking)); ?>" 
                                                           class="text-sm text-blue-600 hover:text-blue-800">
                                                            Manage →
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?php echo e(route('admin.tipping-workflow.show', $bay->currentBooking)); ?>" 
                                                           class="text-sm text-blue-600 hover:text-blue-800">
                                                            Manage →
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if(empty($tippingData)): ?>
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500">No depots available or no tipping operations configured.</p>
            </div>
        <?php endif; ?>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-workflow/dashboard.blade.php ENDPATH**/ ?>