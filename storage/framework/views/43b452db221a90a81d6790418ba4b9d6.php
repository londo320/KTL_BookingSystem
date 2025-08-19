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
                <h2 class="font-semibold text-xl text-gray-800">Tipping Workflow</h2>
                <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">📍 <?php echo e($booking->depot->name); ?></span>
                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium mr-2">FACTORY</span>
                        <?php echo e($booking->reference); ?> - <?php echo e($booking->customer->name); ?>

                    </p>
                <?php else: ?>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">📍 <?php echo e($booking->slot->depot->name); ?></span>
                        Booking <?php echo e($booking->booking_reference ?: '#' . $booking->id); ?> - <?php echo e($booking->customer->name); ?>

                    </p>
                <?php endif; ?>
            </div>
            <div class="flex space-x-2">
                <?php if($booking instanceof \App\Models\FactoryBooking): ?>
                    <a href="<?php echo e(route('admin.factory-bookings.show', $booking)); ?>" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        ← Back to Factory Booking
                    </a>
                <?php else: ?>
                    <a href="<?php echo e(route('admin.bookings.show', $booking)); ?>" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        ← Back to Booking
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('admin.tipping-workflow.dashboard')); ?>" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    📊 Tipping Dashboard
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 max-w-6xl mx-auto">
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Please fix the following errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">📋 Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Booking Reference</p>
                    <p class="font-medium"><?php echo e($booking->booking_reference ?: '#' . $booking->id); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer</p>
                    <p class="font-medium"><?php echo e($booking->customer->name); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer Reference</p>
                    <p class="font-medium"><?php echo e($booking->reference ?: 'Not provided'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Slot Time</p>
                    <p class="font-medium"><?php echo e($booking->slot->start_at->format('D, d M Y - H:i')); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Container</p>
                    <p class="font-medium"><?php echo e($booking->container_number ?: 'Not specified'); ?></p>
                </div>
            </div>
        </div>

        
        <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
            <div class="mb-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">📦 PO Numbers & Load Details</h3>
                
                <div class="space-y-4">
                    <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-300 rounded-lg p-4 bg-white">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-lg text-gray-800">PO: <?php echo e($poNumber->po_number); ?></h4>
                                <div class="flex space-x-2">
                                    <?php if($poNumber->hasVariance()): ?>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                            ⚠️ Has Variance
                                        </span>
                                    <?php endif; ?>
                                    <?php if($poNumber->hasTypeVariances()): ?>
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                            🔄 Type Variance
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            
                            <div class="mb-3 p-3 bg-gray-50 rounded border">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Expected:</span>
                                        <span class="font-semibold"><?php echo e(number_format($poNumber->total_expected_units)); ?> units, <?php echo e(number_format($poNumber->total_expected_pallets)); ?> pallets</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Actual:</span>
                                        <span class="font-semibold <?php echo e($poNumber->total_actual_units > 0 ? 'text-green-600' : 'text-gray-400'); ?>">
                                            <?php echo e($poNumber->total_actual_units > 0 ? number_format($poNumber->total_actual_units) . ' units' : 'Not recorded'); ?>, 
                                            <?php if($poNumber->total_actual_pallets > 0): ?>
                                                <?php if(!empty($poNumber->actual_pallet_breakdown)): ?>
                                                    <?php $__currentLoopData = $poNumber->actual_pallet_breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $breakdown): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php echo e($breakdown['count']); ?> <?php echo e($breakdown['type']); ?><?php echo e($index < count($poNumber->actual_pallet_breakdown) - 1 ? ', ' : ''); ?>

                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    (<?php echo e(number_format($poNumber->total_actual_pallets)); ?> total)
                                                <?php else: ?>
                                                    <?php echo e(number_format($poNumber->total_actual_pallets)); ?> pallets
                                                <?php endif; ?>
                                            <?php else: ?>
                                                Not recorded
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            
                            <?php if($poNumber->lines->count() > 0): ?>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium"><?php echo e($poNumber->lines->count()); ?> line(s)</span>
                                    <?php if($poNumber->lines->where('actual_cases', '>', 0)->count() > 0): ?>
                                        <span class="ml-2 text-green-600">• <?php echo e($poNumber->lines->where('actual_cases', '>', 0)->count()); ?> recorded</span>
                                    <?php endif; ?>
                                    <?php if($poNumber->lines->filter(fn($line) => $line->hasVariance())->count() > 0): ?>
                                        <span class="ml-2 text-red-600">• <?php echo e($poNumber->lines->filter(fn($line) => $line->hasVariance())->count()); ?> with variance</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php if($booking->poNumbers->count() > 1): ?>
                    <div class="border-t pt-4 mt-4 bg-white p-3 rounded">
                        <h5 class="font-medium text-gray-800 mb-2">📊 Summary Totals</h5>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Expected:</span>
                                <span class="font-semibold"><?php echo e(number_format($booking->total_expected_cases)); ?> units, <?php echo e(number_format($booking->total_expected_pallets)); ?> pallets</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Actual:</span>
                                <span class="font-semibold <?php echo e($booking->total_actual_cases > 0 ? 'text-green-600' : 'text-gray-400'); ?>">
                                    <?php echo e($booking->total_actual_cases > 0 ? number_format($booking->total_actual_cases) . ' units' : 'Not recorded'); ?>, 
                                    <?php echo e($booking->total_actual_pallets > 0 ? number_format($booking->total_actual_pallets) . ' pallets' : 'Not recorded'); ?>

                                </span>
                                <?php if($booking->total_actual_pallets > 0): ?>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <?php
                                            $allPalletBreakdown = [];
                                            foreach($booking->poNumbers as $po) {
                                                foreach($po->actual_pallet_breakdown as $breakdown) {
                                                    if (!isset($allPalletBreakdown[$breakdown['type']])) {
                                                        $allPalletBreakdown[$breakdown['type']] = 0;
                                                    }
                                                    $allPalletBreakdown[$breakdown['type']] += $breakdown['count'];
                                                }
                                            }
                                        ?>
                                        <?php if(!empty($allPalletBreakdown)): ?>
                                            Breakdown: 
                                            <?php $__currentLoopData = $allPalletBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php echo e($count); ?> <?php echo e($type); ?><?php echo e(!$loop->last ? ', ' : ''); ?>

                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if($booking->hasPoVariances()): ?>
                            <div class="mt-2 text-sm text-red-600">
                                ⚠️ This booking has quantity or type variances
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center text-yellow-800">
                    <span class="text-2xl mr-2">📦</span>
                    <div>
                        <h4 class="font-medium">No PO Numbers</h4>
                        <p class="text-sm text-yellow-700">No purchase order numbers have been added to this booking.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        
        <?php if(!$workflowEnabled): ?>
            <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium">Manual Tipping Mode</h4>
                        <p class="text-sm mt-1">Workflow enforcement is disabled. You can perform actions in any order without restrictions.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Tipping Progress</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Current Status: <?php echo $booking->tipping_status_badge; ?>

                    <?php if(!$workflowEnabled): ?>
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Manual Mode</span>
                    <?php endif; ?>
                </p>
            </div>

            
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    <?php
                        $movement = $booking->movements()->first();
                        $isEmptyTrailer = $movement && in_array($movement->current_status, ['empty', 'departed']) && $movement->unloading_completed_at;
                        
                        // Dynamic stages based on whether trailer is empty or not
                        if ($isEmptyTrailer) {
                            $stages = [
                                'scheduled' => ['label' => '⏳ Not Started', 'icon' => 'text-gray-400'],
                                'in_location' => ['label' => '✅ Empty Unit Positioned', 'icon' => 'text-green-500'],
                                'trailer_dropped' => ['label' => '✅ Empty Trailer Dropped', 'icon' => 'text-green-500'],
                                'at_bay' => ['label' => '✅ Empty Unit at Bay', 'icon' => 'text-green-500'],
                                'departed' => ['label' => '🏁 Departed', 'icon' => 'text-purple-500']
                            ];
                        } else {
                            $stages = [
                                'scheduled' => ['label' => '⏳ Not Started', 'icon' => 'text-gray-400'],
                                'in_location' => ['label' => '🚛 Unit & Trailer Positioned', 'icon' => 'text-blue-500'],
                                'at_bay' => ['label' => '🚛 At Bay', 'icon' => 'text-yellow-500'],
                                'unloading' => ['label' => '⚡ Tipping', 'icon' => 'text-orange-500'],
                                'empty' => ['label' => '✅ Tipped - Ready for Collection', 'icon' => 'text-green-500'],
                                'trailer_dropped' => ['label' => '📍 In Collection Zone', 'icon' => 'text-purple-500'],
                                'departed' => ['label' => '🏁 Collected', 'icon' => 'text-blue-600']
                            ];
                        }
                        
                        $currentIndex = array_search($booking->tipping_status, array_keys($stages));
                        // If status not found in current stage set, find the closest match
                        if ($currentIndex === false) {
                            if ($booking->tipping_status === 'arrived') $currentIndex = 0;
                            else $currentIndex = -1;
                        }
                    ?>

                    <?php $__currentLoopData = $stages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php 
                            $stepIndex = array_search($status, array_keys($stages));
                            $isCompleted = $stepIndex <= $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                            
                            // Dynamic step colors based on stage and completion
                            if ($isCurrent) {
                                $stepClass = 'bg-blue-500 text-white ring-2 ring-blue-200';
                            } elseif ($isCompleted) {
                                if ($isEmptyTrailer) {
                                    $stepClass = 'bg-green-500 text-white';
                                } else {
                                    $stepClass = 'bg-green-500 text-white';
                                }
                            } else {
                                $stepClass = 'bg-gray-200 text-gray-500';
                            }
                        ?>
                        
                        <div class="flex flex-col items-center relative <?php echo e(!$loop->last ? 'flex-1' : ''); ?>">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold <?php echo e($stepClass); ?>">
                                <?php if($isCompleted && !$isCurrent): ?>
                                    ✓
                                <?php else: ?>
                                    <?php echo e($loop->iteration); ?>

                                <?php endif; ?>
                            </div>
                            <p class="text-xs mt-2 text-center max-w-20 <?php echo e($isCurrent ? 'font-bold text-blue-600' : ($isCompleted ? 'font-medium text-gray-700' : 'text-gray-500')); ?>">
                                <?php echo e($config['label']); ?>

                            </p>
                            
                            <?php if(!$loop->last): ?>
                                <?php
                                    $nextStepIndex = $stepIndex + 1;
                                    $lineCompleted = $nextStepIndex <= $currentIndex;
                                ?>
                                <div class="absolute top-5 left-1/2 w-full h-0.5 <?php echo e($lineCompleted ? 'bg-green-400' : 'bg-gray-300'); ?>"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <?php if($workflowEnabled ? in_array($booking->tipping_status, ['scheduled', 'arrived']) : true): ?>
                        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50 <?php echo e(!$workflowEnabled && !in_array($booking->tipping_status, ['scheduled', 'arrived']) ? 'opacity-75' : ''); ?>">
                            <h4 class="font-medium text-blue-800 mb-3">🚛 Move to Location (Attached)</h4>
                            <p class="text-xs text-blue-700 mb-3">Vehicle with trailer attached moves to a location on-site</p>
                            <form action="<?php echo e(route('admin.tipping-workflow.drop-trailer', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        <?php $__currentLoopData = $availableLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location->id); ?>">
                                                <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Move to Location
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($workflowEnabled ? in_array($booking->tipping_status, ['scheduled', 'arrived', 'in_location']) : true): ?>
                        <div class="p-4 border border-red-200 rounded-lg bg-red-50 <?php echo e(!$workflowEnabled && !in_array($booking->tipping_status, ['scheduled', 'arrived', 'in_location']) ? 'opacity-75' : ''); ?>">
                            <h4 class="font-medium text-red-800 mb-3">📍 Drop Trailer (Detached)</h4>
                            <p class="text-xs text-red-700 mb-3">Detach trailer from unit and leave at location</p>
                            <form action="<?php echo e(route('admin.tipping-workflow.drop-trailer-detached', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Drop Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        <?php $__currentLoopData = $availableLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location->id); ?>">
                                                <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Drop Trailer (Detached)
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($workflowEnabled ? $booking->tipping_status === 'in_location' : true): ?>
                        <div class="p-4 border border-cyan-200 rounded-lg bg-cyan-50 <?php echo e(!$workflowEnabled && $booking->tipping_status !== 'in_location' ? 'opacity-75' : ''); ?>">
                            <h4 class="font-medium text-cyan-800 mb-3">🔄 Move Between Locations</h4>
                            <p class="text-xs text-cyan-700 mb-3">Move vehicle to a different location on-site</p>
                            <form action="<?php echo e(route('admin.tipping-workflow.move-to-location', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        <?php $__currentLoopData = $availableLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location->id); ?>" 
                                                    <?php if($booking->tippingLocation && $booking->tippingLocation->id == $location->id): ?> disabled <?php endif; ?>>
                                                <?php echo e($location->name); ?> 
                                                <?php if($booking->tippingLocation && $booking->tippingLocation->id == $location->id): ?> 
                                                    (Current Location)
                                                <?php else: ?>
                                                    (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Reason for move..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-cyan-500 text-white rounded hover:bg-cyan-600">
                                    Move to New Location
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $currentMovement = $booking->movements->first();
                        $tippingAlreadyCompleted = $currentMovement && $currentMovement->unloading_completed_at;
                    ?>
                    <?php if($workflowEnabled ? in_array($booking->tipping_status, ['in_location', 'trailer_dropped']) && $booking->tipping_status !== 'empty' && !$tippingAlreadyCompleted : !in_array($booking->tipping_status, ['empty', 'departed']) && !$tippingAlreadyCompleted): ?>
                        <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50 <?php echo e(!$workflowEnabled && !in_array($booking->tipping_status, ['in_location', 'trailer_dropped']) ? 'opacity-75' : ''); ?>">
                            <h4 class="font-medium text-yellow-800 mb-3">🚛 Move to Tipping Bay</h4>
                            <form action="<?php echo e(route('admin.tipping-workflow.move-to-bay', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipping Bay</label>
                                    <select name="tipping_bay_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select bay...</option>
                                        <?php $__currentLoopData = $availableBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bay->id); ?>">
                                                <?php echo e($bay->name); ?>

                                                <?php if(!empty($bay->equipment)): ?>
                                                    (<?php echo e(implode(', ', $bay->equipment)); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Move to Bay
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $currentMovement = $booking->movements->first();
                        $tippingAlreadyCompleted = $currentMovement && $currentMovement->unloading_completed_at;
                    ?>
                    <?php if($workflowEnabled ? ($booking->tipping_status === 'at_bay' && !$tippingAlreadyCompleted) : (!$tippingAlreadyCompleted)): ?>
                        <div class="p-4 border border-orange-200 rounded-lg bg-orange-50 <?php echo e(!$workflowEnabled && $booking->tipping_status !== 'at_bay' ? 'opacity-75' : ''); ?>">
                            <h4 class="font-medium text-orange-800 mb-3">⚡ Start Tipping</h4>
                            <form action="<?php echo e(route('admin.tipping-workflow.start-tipping', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                                    Start Tipping
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($workflowEnabled ? ($booking->tipping_status === 'unloading' && !$tippingAlreadyCompleted) : (!$tippingAlreadyCompleted)): ?>
                        <div class="col-span-2 p-4 border border-green-200 rounded-lg bg-green-50 <?php echo e(!$workflowEnabled && $booking->tipping_status !== 'unloading' ? 'opacity-75' : ''); ?>">
                            <h4 class="font-medium text-green-800 mb-3">✅ Complete Tipping</h4>
                            <p class="text-sm text-green-700 mb-3">Complete this form only after tipping has finished to record the actual quantities received.</p>
                            
                            <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                                <form action="<?php echo e(route('admin.tipping-workflow.complete-tipping', $booking)); ?>" method="POST" id="complete-tipping-form">
                                    <?php echo csrf_field(); ?>
                                    
                                    
                                    <div class="mb-6 bg-white p-4 rounded border">
                                        <h5 class="font-medium text-gray-800 mb-4">📦 Record Actual Quantities Received <span class="text-red-500">*</span></h5>
                                        
                                        <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="mb-6 border border-gray-300 rounded p-4">
                                                <h6 class="font-medium text-gray-800 mb-3">PO: <?php echo e($poNumber->po_number); ?></h6>
                                                
                                                <?php $__currentLoopData = $poNumber->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <div class="mb-4 p-3 bg-gray-50 rounded border" data-line-id="<?php echo e($line->id); ?>">
                                                        <div class="flex justify-between items-start mb-3">
                                                            <div>
                                                                <h7 class="font-medium text-gray-700">Line <?php echo e($line->line_number); ?></h7>
                                                                <p class="text-sm text-gray-600">
                                                                    Expected: <?php echo e(number_format($line->expected_cases)); ?> units, 
                                                                    <?php echo e(number_format($line->expected_pallets)); ?> <?php echo e($line->expectedPalletType?->name ?? 'pallets'); ?>

                                                                </p>
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                Actual Units/Cases <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="number" 
                                                                   name="po_lines[<?php echo e($line->id); ?>][actual_cases]" 
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                                                   min="0" 
                                                                   value="<?php echo e($line->actual_cases ?? ''); ?>"
                                                                   required>
                                                        </div>
                                                        
                                                        
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                Actual Pallets <span class="text-red-500">*</span>
                                                            </label>
                                                            
                                                            <div class="pallet-entries" data-line-id="<?php echo e($line->id); ?>">
                                                                
                                                                <?php if($line->actualPallets->count() > 0): ?>
                                                                    <?php $__currentLoopData = $line->actualPallets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $actualPallet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                        <div class="flex items-center space-x-2 mb-2 pallet-entry">
                                                                            <select name="po_lines[<?php echo e($line->id); ?>][actual_pallets][<?php echo e($index); ?>][pallet_type_id]" 
                                                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                                                                                <option value="">Select pallet type...</option>
                                                                                <?php $__currentLoopData = $palletTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                    <option value="<?php echo e($palletType->id); ?>" 
                                                                                            <?php echo e($actualPallet->pallet_type_id == $palletType->id ? 'selected' : ''); ?>>
                                                                                        <?php echo e($palletType->display_name); ?>

                                                                                    </option>
                                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                            </select>
                                                                            <input type="number" 
                                                                                   name="po_lines[<?php echo e($line->id); ?>][actual_pallets][<?php echo e($index); ?>][quantity]" 
                                                                                   class="w-24 px-3 py-2 border border-gray-300 rounded-md" 
                                                                                   placeholder="Qty" 
                                                                                   min="1" 
                                                                                   value="<?php echo e($actualPallet->quantity); ?>"
                                                                                   required>
                                                                            <button type="button" onclick="removePalletEntry(this)" class="px-2 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200">
                                                                                ✕
                                                                            </button>
                                                                        </div>
                                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                <?php else: ?>
                                                                    
                                                                    <div class="flex items-center space-x-2 mb-2 pallet-entry">
                                                                        <select name="po_lines[<?php echo e($line->id); ?>][actual_pallets][0][pallet_type_id]" 
                                                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                                                                            <option value="">Select pallet type...</option>
                                                                            <?php $__currentLoopData = $palletTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                <option value="<?php echo e($palletType->id); ?>" 
                                                                                        <?php echo e($line->expected_pallet_type_id == $palletType->id ? 'selected' : ''); ?>>
                                                                                    <?php echo e($palletType->display_name); ?>

                                                                                </option>
                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                        </select>
                                                                        <input type="number" 
                                                                               name="po_lines[<?php echo e($line->id); ?>][actual_pallets][0][quantity]" 
                                                                               class="w-24 px-3 py-2 border border-gray-300 rounded-md" 
                                                                               placeholder="Qty" 
                                                                               min="1" 
                                                                               value="<?php echo e($line->expected_pallets ?? ''); ?>"
                                                                               required>
                                                                        <button type="button" onclick="removePalletEntry(this)" class="px-2 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200">
                                                                            ✕
                                                                        </button>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            
                                                            <button type="button" onclick="addPalletEntry(<?php echo e($line->id); ?>)" class="text-sm text-blue-600 hover:text-blue-800 mt-2">
                                                                + Add another pallet type
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Issues (if any)</label>
                                        <div id="issues-container">
                                            <input type="text" name="issues[]" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Describe any issues...">
                                        </div>
                                        <button type="button" onclick="addIssueField()" class="text-sm text-blue-600 hover:text-blue-800">+ Add another issue</button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Completion notes..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Complete Tipping
                                    </button>
                                </form>
                            <?php else: ?>
                                
                                <form action="<?php echo e(route('admin.tipping-workflow.complete-tipping', $booking)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Issues (if any)</label>
                                        <div id="issues-container">
                                            <input type="text" name="issues[]" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Describe any issues...">
                                        </div>
                                        <button type="button" onclick="addIssueField()" class="text-sm text-blue-600 hover:text-blue-800">+ Add another issue</button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Completion notes..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Complete Tipping
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $currentMovement = $booking->movements->first();
                        $isUnitStillOnSite = !$currentMovement || !$currentMovement->unit_departed_at;
                    ?>
                    <?php if($booking->tipping_status === 'unloading' && $isUnitStillOnSite): ?>
                        <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-3">🚛 Unit Depart (Leave Trailer)</h4>
                            <p class="text-xs text-purple-700 mb-3">Record when the vehicle leaves site while trailer continues tipping process</p>
                            <form action="<?php echo e(route('admin.tipping-workflow.unit-depart', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Departure Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes about unit departure..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    🚛 Record Unit Departure
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($booking->tipping_status === 'empty' && $booking->tippingBay): ?>
                        <div class="p-4 border border-indigo-200 rounded-lg bg-indigo-50">
                            <h4 class="font-medium text-indigo-800 mb-3">⚡ Quick Actions</h4>
                            <div class="space-y-2">
                                <form action="<?php echo e(route('admin.bookings.clear-bay', $booking)); ?>" method="POST" class="inline-block w-full">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                        🔄 Clear Bay (Make Available)
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.bookings.move-to-waiting', $booking)); ?>" method="POST" class="inline-block w-full">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                        📍 Move to Waiting Area
                                    </button>
                                </form>
                            </div>
                            <p class="text-xs text-indigo-700 mt-2">💡 Use these actions to quickly make room for the next vehicle</p>
                        </div>
                    <?php endif; ?>

                    
                    <?php
                        $currentMovement = $booking->movements->first();
                        $isUnitStillOnSite = !$currentMovement || !$currentMovement->unit_departed_at;
                        $isTrailerWaitingCollection = $currentMovement && $currentMovement->current_status === 'trailer_dropped';
                        $isCollectionInProgress = $currentMovement && $currentMovement->current_status === 'trailer_collected';
                    ?>

                    
                    <?php if($booking->tipping_status === 'empty'): ?>
                        <div class="col-span-2 p-6 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-4 text-center">🏁 Tipping Complete - Move to Collection Zone</h4>
                            <p class="text-sm text-purple-600 mb-4 text-center">Select which specific collection zone to move the empty trailer to for organized pickup.</p>
                            
                            <form action="<?php echo e(route('admin.operations.move-to-collection-zone', $booking)); ?>" method="POST" class="max-w-md mx-auto">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        📍 Select Specific Collection Zone <span class="text-red-500">*</span>
                                    </label>
                                    <select name="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Choose which collection zone...</option>
                                        <?php $__empty_1 = true; $__currentLoopData = $collectionZones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                            <option value="<?php echo e($location->id); ?>">
                                                📦 <?php echo e($location->name); ?> 
                                                <?php if($location->code): ?> 
                                                    (<?php echo e($location->code); ?>) 
                                                <?php endif; ?>
                                                - <?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> spaces
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                            <option value="" disabled>❌ No collection zones available</option>
                                        <?php endif; ?>
                                    </select>
                                    <p class="text-xs text-gray-600 mt-1">Trailer will be positioned in the selected zone for transport pickup</p>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Collection zone notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    Move to Collection Zone
                                </button>
                            </form>
                            
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-600">💡 Empty trailer will be positioned for collection by transport company</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($isTrailerWaitingCollection): ?>
                        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                            <h4 class="font-medium text-green-800 mb-3">🚚 Collection Unit Arrival</h4>
                            <form action="<?php echo e(route('admin.tipping-workflow.collection-arrival', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Collection Unit Registration *</label>
                                        <input type="text" name="collection_unit_registration" class="w-full px-3 py-2 border border-gray-300 rounded-md" required placeholder="AB12 XYZ">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrier Company *</label>
                                        <div class="relative">
                                            <input type="text" 
                                                   id="collection-carrier-search" 
                                                   name="carrier_name"
                                                   placeholder="Search or type carrier name..."
                                                   required
                                                   autocomplete="off"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 pr-10">
                                            
                                            
                                            <input type="hidden" 
                                                   id="collection-carrier-id" 
                                                   name="carrier_id" 
                                                   value="">
                                            
                                            
                                            <div id="collection-carrier-dropdown" 
                                                 class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                              
                                            </div>
                                            
                                            
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                              <span id="collection-carrier-status" class="text-xs"></span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trailer Location</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                                        <?php if($booking->tippingLocation): ?>
                                            📍 <?php echo e($booking->tippingLocation->name); ?>

                                            <?php if($booking->tippingLocation->description): ?>
                                                <span class="text-gray-500 text-sm">- <?php echo e($booking->tippingLocation->description); ?></span>
                                            <?php endif; ?>
                                        <?php elseif($booking->tippingBay): ?>
                                            🏗️ <?php echo e($booking->tippingBay->name); ?>

                                            <?php if($booking->tippingBay->description): ?>
                                                <span class="text-gray-500 text-sm">- <?php echo e($booking->tippingBay->description); ?></span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            📍 Location not specified
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Current trailer location for collection</p>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Collection arrival notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    Record Collection Arrival
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($isCollectionInProgress): ?>
                        <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-3">🏁 Collection Departure</h4>
                            <form action="<?php echo e(route('admin.tipping-workflow.collection-depart', $booking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Collection departure notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    Complete Collection
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">📊 Status Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <?php if($booking->tippingLocation): ?>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Drop Location</h4>
                            <p class="text-sm text-gray-600"><?php echo e($booking->tippingLocation->name); ?></p>
                            <?php if($booking->trailer_dropped_at): ?>
                                <p class="text-xs text-gray-500">Dropped: <?php echo e($booking->trailer_dropped_at->format('M j, H:i')); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($booking->tippingBay): ?>
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Tipping Bay</h4>
                            <p class="text-sm text-gray-600"><?php echo e($booking->tippingBay->name); ?></p>
                            <?php if($booking->moved_to_bay_at): ?>
                                <p class="text-xs text-gray-500">Moved: <?php echo e($booking->moved_to_bay_at->format('M j, H:i')); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Timing</h4>
                        <?php if($booking->tipping_started_at): ?>
                            <p class="text-sm text-gray-600">Started: <?php echo e($booking->tipping_started_at->format('M j, H:i')); ?></p>
                        <?php endif; ?>
                        <?php if($booking->tipping_completed_at && $booking->actual_tipping_duration): ?>
                            <p class="text-sm text-gray-600">Duration: <?php echo e($booking->actual_tipping_duration); ?> minutes</p>
                        <?php endif; ?>
                        <?php if($booking->trailer_departed_at): ?>
                            <p class="text-sm text-gray-600">Departed: <?php echo e($booking->trailer_departed_at->format('M j, H:i')); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if($booking->tipping_notes): ?>
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-2">Notes</h4>
                        <p class="text-sm text-gray-600 whitespace-pre-line"><?php echo e($booking->tipping_notes); ?></p>
                    </div>
                <?php endif; ?>

                
                <?php if($booking->tipping_issues): ?>
                    <div class="mt-6 p-4 bg-red-50 rounded-lg">
                        <h4 class="font-medium text-red-800 mb-2">Issues Reported</h4>
                        <ul class="text-sm text-red-600 list-disc list-inside">
                            <?php $__currentLoopData = $booking->tipping_issues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($issue); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Pallet types data for JavaScript
        const palletTypes = <?php echo json_encode($palletTypes->map(fn($pt) => ['id' => $pt->id, 'name' => $pt->display_name]), 512) ?>;
        
        function addIssueField() {
            const container = document.getElementById('issues-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'issues[]';
            input.className = 'w-full px-3 py-2 border border-gray-300 rounded-md mb-2';
            input.placeholder = 'Describe any issues...';
            container.appendChild(input);
        }
        
        function addPalletEntry(lineId) {
            const container = document.querySelector(`.pallet-entries[data-line-id="${lineId}"]`);
            const existingEntries = container.querySelectorAll('.pallet-entry').length;
            
            const palletEntry = document.createElement('div');
            palletEntry.className = 'flex items-center space-x-2 mb-2 pallet-entry';
            
            // Create select options
            let optionsHtml = '<option value="">Select pallet type...</option>';
            palletTypes.forEach(palletType => {
                optionsHtml += `<option value="${palletType.id}">${palletType.name}</option>`;
            });
            
            palletEntry.innerHTML = `
                <select name="po_lines[${lineId}][actual_pallets][${existingEntries}][pallet_type_id]" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                    ${optionsHtml}
                </select>
                <input type="number" 
                       name="po_lines[${lineId}][actual_pallets][${existingEntries}][quantity]" 
                       class="w-24 px-3 py-2 border border-gray-300 rounded-md" 
                       placeholder="Qty" 
                       min="1" 
                       required>
                <button type="button" onclick="removePalletEntry(this)" class="px-2 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200">
                    ✕
                </button>
            `;
            
            container.appendChild(palletEntry);
        }
        
        function removePalletEntry(button) {
            const entry = button.closest('.pallet-entry');
            const container = entry.parentElement;
            
            // Don't allow removing the last entry
            if (container.querySelectorAll('.pallet-entry').length > 1) {
                entry.remove();
                // Reindex remaining entries
                reindexPalletEntries(container);
            }
        }
        
        function reindexPalletEntries(container) {
            const lineId = container.getAttribute('data-line-id');
            const entries = container.querySelectorAll('.pallet-entry');
            
            entries.forEach((entry, index) => {
                const select = entry.querySelector('select');
                const input = entry.querySelector('input[type="number"]');
                
                select.name = `po_lines[${lineId}][actual_pallets][${index}][pallet_type_id]`;
                input.name = `po_lines[${lineId}][actual_pallets][${index}][quantity]`;
            });
        }

        // Collection Carrier Search Logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('collection-carrier-search');
            const carrierIdInput = document.getElementById('collection-carrier-id');
            const dropdown = document.getElementById('collection-carrier-dropdown');
            const statusSpan = document.getElementById('collection-carrier-status');
            
            if (!searchInput) return; // Exit if elements don't exist
            
            let searchTimeout;
            let selectedCarrierId = carrierIdInput.value;
            let isLoading = false;
            
            // Update status based on current state
            function updateStatus() {
                if (selectedCarrierId) {
                    statusSpan.textContent = '✓';
                    statusSpan.className = 'text-xs text-green-600';
                } else if (searchInput.value.trim()) {
                    statusSpan.textContent = '+';
                    statusSpan.className = 'text-xs text-blue-600';
                    statusSpan.title = 'Will create new carrier';
                } else {
                    statusSpan.textContent = '';
                    statusSpan.className = 'text-xs';
                }
            }
            
            // Search carriers
            function searchCarriers(query) {
                if (query.length < 2) {
                    dropdown.classList.add('hidden');
                    return;
                }
                
                if (isLoading) return;
                isLoading = true;
                
                fetch(`<?php echo e(route('api.carriers.search')); ?>?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        populateDropdown(data, query);
                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Search failed:', error);
                        dropdown.classList.add('hidden');
                        isLoading = false;
                    });
            }
            
            function populateDropdown(data, query) {
                dropdown.innerHTML = '';
                dropdown.classList.remove('hidden');
                
                if (data.carriers && data.carriers.length > 0) {
                    data.carriers.forEach(carrier => {
                        const item = document.createElement('div');
                        item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0';
                        item.innerHTML = `
                            <div class="font-medium text-sm">${carrier.name}</div>
                            <div class="text-xs text-gray-500">${carrier.is_active ? 'Active' : 'Inactive'} carrier</div>
                        `;
                        item.addEventListener('click', () => selectCarrier(carrier));
                        dropdown.appendChild(item);
                    });
                }
                
                // Add option to create new carrier
                if (query.trim()) {
                    const createItem = document.createElement('div');
                    createItem.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-t-2 border-blue-200 bg-blue-25';
                    createItem.innerHTML = `
                        <div class="font-medium text-sm text-blue-600">+ Create "${query}"</div>
                        <div class="text-xs text-blue-500">Add as new carrier company</div>
                    `;
                    createItem.addEventListener('click', () => createNewCarrier(query));
                    dropdown.appendChild(createItem);
                }
            }
            
            function selectCarrier(carrier) {
                searchInput.value = carrier.name;
                carrierIdInput.value = carrier.id;
                selectedCarrierId = carrier.id;
                dropdown.classList.add('hidden');
                updateStatus();
            }
            
            function createNewCarrier(name) {
                searchInput.value = name;
                carrierIdInput.value = '';
                selectedCarrierId = '';
                dropdown.classList.add('hidden');
                updateStatus();
            }
            
            // Event listeners
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                selectedCarrierId = '';
                carrierIdInput.value = '';
                
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => searchCarriers(query), 300);
                updateStatus();
            });
            
            searchInput.addEventListener('focus', function() {
                if (this.value.length >= 2) {
                    searchCarriers(this.value);
                }
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            
            updateStatus();
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-workflow/show.blade.php ENDPATH**/ ?>