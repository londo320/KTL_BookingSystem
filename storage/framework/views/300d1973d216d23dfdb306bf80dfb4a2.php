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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Factory Tipping Workflow</h2>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">📍 <?php echo e($factoryBooking->depot->name); ?></span>
                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium mr-2">FACTORY</span>
                    <?php echo e($factoryBooking->reference); ?> - <?php echo e($factoryBooking->customer->name); ?>

                </p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('app.factory-bookings.show', $factoryBooking)); ?>" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Factory Booking
                </a>
                <a href="<?php echo e(route('app.tipping-workflow.dashboard')); ?>" 
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
            <h3 class="text-lg font-semibold text-blue-800 mb-3">📋 Factory Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Factory Reference</p>
                    <p class="font-medium"><?php echo e($factoryBooking->reference); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer</p>
                    <p class="font-medium"><?php echo e($factoryBooking->customer->name); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">PO References</p>
                    <p class="font-medium">
                        <?php if($factoryBooking->poNumbers && $factoryBooking->poNumbers->count() > 0): ?>
                            <?php echo e($factoryBooking->poNumbers->pluck('po_number')->join(', ')); ?>

                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Delivery Type</p>
                    <p class="font-medium">Factory Delivery</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Vehicle</p>
                    <p class="font-medium"><?php echo e($factoryBooking->vehicle_registration ?: 'Not specified'); ?></p>
                </div>
            </div>
        </div>
        
        
        <?php if($factoryBooking->poNumbers && $factoryBooking->poNumbers->count() > 0): ?>
            <div class="mb-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">📦 PO Numbers & Load Details</h3>
                <div class="space-y-4">
                    <?php $__currentLoopData = $factoryBooking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="border border-gray-300 rounded-lg p-4 bg-white">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-lg text-gray-800">PO: <?php echo e($poNumber->po_number); ?></h4>
                                <div class="flex space-x-2">
                                    <?php if($poNumber->hasVariance()): ?>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                            ⚠️ Has Variance
                                        </span>
                                    <?php endif; ?>
                                    <?php if($poNumber->isComplete()): ?>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                            ✅ Complete
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3 p-3 bg-gray-50 rounded border">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Expected:</span>
                                        <span class="font-semibold"><?php echo e(number_format($poNumber->total_expected_cases)); ?> units, <?php echo e(number_format($poNumber->total_expected_pallets)); ?> pallets</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Actual:</span>
                                        <span class="font-semibold <?php echo e($poNumber->total_actual_cases > 0 ? 'text-green-600' : 'text-gray-400'); ?>">
                                            <?php echo e($poNumber->total_actual_cases > 0 ? number_format($poNumber->total_actual_cases) . ' units' : 'Not recorded'); ?>, 
                                            <?php echo e($poNumber->total_actual_pallets > 0 ? number_format($poNumber->total_actual_pallets) . ' pallets' : 'Not recorded'); ?>

                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($poNumber->lines->count() > 0): ?>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Line</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Expected Units</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Expected Pallets</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Actual Units</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Actual Pallets</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            <?php $__currentLoopData = $poNumber->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 font-medium"><?php echo e($line->line_number); ?></td>
                                                    <td class="px-3 py-2"><?php echo e(number_format($line->expected_cases)); ?></td>
                                                    <td class="px-3 py-2"><?php echo e(number_format($line->expected_pallets)); ?></td>
                                                    <td class="px-3 py-2 <?php echo e($line->actual_cases > 0 ? 'text-green-600 font-medium' : 'text-gray-400'); ?>">
                                                        <?php echo e($line->actual_cases > 0 ? number_format($line->actual_cases) : 'Not recorded'); ?>

                                                    </td>
                                                    <td class="px-3 py-2 <?php echo e($line->actual_pallets > 0 ? 'text-green-600 font-medium' : 'text-gray-400'); ?>">
                                                        <?php echo e($line->actual_pallets > 0 ? number_format($line->actual_pallets) : 'Not recorded'); ?>

                                                    </td>
                                                    <td class="px-3 py-2">
                                                        <?php if($line->hasVariance()): ?>
                                                            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded">Variance</span>
                                                        <?php elseif($line->actual_cases > 0): ?>
                                                            <span class="px-2 py-1 bg-green-100 text-green-600 text-xs rounded">Complete</span>
                                                        <?php else: ?>
                                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Factory Tipping Progress</h3>
                <p class="text-sm text-gray-600 mt-1">Track your factory delivery through the tipping process</p>
            </div>
            <div class="p-6">
                <?php
                    $movement = $factoryBooking->movements->last();
                    $currentStatus = $movement ? $movement->current_status : 'arrived';
                    $currentLocation = $movement?->tippingLocation;
                    $currentBay = $movement?->tippingBay;
                    
                    // Check if tipping is actually complete based on timestamps
                    if ($movement && $movement->unloading_completed_at && $currentStatus === 'unloading') {
                        $currentStatus = 'empty'; // Override status if tipping completed but status not updated
                    }
                    
                    // Factory workflow stages
                    $stages = [
                        'arrived' => ['label' => '⏳ Arrived', 'icon' => 'text-blue-500'],
                        'in_parking' => ['label' => '🚛 In Parking Area', 'icon' => 'text-blue-500'],
                        'at_bay' => ['label' => '⚡ At Tipping Bay', 'icon' => 'text-orange-500'],
                        'unloading' => ['label' => '⚡ Tipping in Progress', 'icon' => 'text-orange-500'],
                        'empty' => ['label' => '✅ Tipping Complete', 'icon' => 'text-green-500'],
                        'back_to_parking' => ['label' => '📍 Back in Parking', 'icon' => 'text-purple-500'],
                        'departed' => ['label' => '🏁 Departed', 'icon' => 'text-blue-600']
                    ];
                    
                    $stageOrder = ['arrived', 'in_parking', 'at_bay', 'unloading', 'empty', 'back_to_parking', 'departed'];
                    $currentIndex = array_search($currentStatus, $stageOrder);
                ?>
                
                
                <div class="flex items-center justify-between space-x-4 mb-6">
                    <?php $__currentLoopData = $stageOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepIndex => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $config = $stages[$status] ?? ['label' => 'Unknown', 'icon' => 'text-gray-400'];
                            $isCompleted = $stepIndex < $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                            
                            if ($isCurrent) {
                                $stepClass = 'bg-orange-500 text-white';
                            } elseif ($isCompleted) {
                                $stepClass = 'bg-green-500 text-white';
                            } else {
                                $stepClass = 'bg-gray-200 text-gray-500';
                            }
                        ?>
                        <div class="flex flex-col items-center relative <?php echo e(!$loop->last ? 'flex-1' : ''); ?>">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold <?php echo e($stepClass); ?>">
                                <?php if($isCompleted && !$isCurrent): ?>
                                    ✓
                                <?php else: ?>
                                    <?php echo e($loop->iteration); ?>

                                <?php endif; ?>
                            </div>
                            <p class="text-xs mt-3 text-center max-w-24 <?php echo e($isCurrent ? 'font-bold text-orange-600' : ($isCompleted ? 'font-medium text-gray-700' : 'text-gray-500')); ?>">
                                <?php echo e($config['label']); ?>

                            </p>
                            <?php if(!$loop->last): ?>
                                <?php
                                    $nextStepIndex = $stepIndex + 1;
                                    $lineCompleted = $nextStepIndex <= $currentIndex;
                                ?>
                                <div class="absolute top-6 left-1/2 w-full h-0.5 <?php echo e($lineCompleted ? 'bg-green-400' : 'bg-gray-300'); ?>"></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                
                <?php
                    $statusLabels = [
                        'arrived' => ['🚛 Vehicle Arrived', 'bg-blue-100 text-blue-800'],
                        'in_parking' => ['📍 In Parking Area', 'bg-blue-100 text-blue-800'], 
                        'at_bay' => ['🏗️ At Tipping Bay - Full', 'bg-orange-100 text-orange-800'],
                        'unloading' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
                        'empty' => ['✅ Tipping Complete - Empty', 'bg-green-100 text-green-800'],
                        'back_to_parking' => ['📍 Back in Parking Area', 'bg-purple-100 text-purple-800'],
                        'departed' => ['🏁 Departed', 'bg-gray-100 text-gray-800'],
                    ];
                    $statusConfig = $statusLabels[$currentStatus] ?? ['❓ Unknown Status', 'bg-gray-100 text-gray-800'];
                ?>
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Current Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?php echo e($statusConfig[1]); ?>">
                            <?php echo e($statusConfig[0]); ?>

                        </span>
                    </div>
                    <?php if($currentLocation): ?>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="font-medium"><?php echo e($currentLocation->name); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($currentBay): ?>
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Tipping Bay</p>
                            <p class="font-medium"><?php echo e($currentBay->name); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <?php if(in_array($currentStatus, ['arrived', 'in_parking'])): ?>
                        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                            <h4 class="font-medium text-blue-800 mb-3">🚛 Move to Location</h4>
                            <p class="text-xs text-blue-700 mb-3">Move factory vehicle to a parking location</p>
                            <form action="<?php echo e(route('app.factory-booking-workflow.drop-trailer', $factoryBooking)); ?>" method="POST">
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
                                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    📍 Drop at Location
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(in_array($currentStatus, ['in_parking', 'back_to_parking'])): ?>
                        <div class="p-4 border border-orange-200 rounded-lg bg-orange-50">
                            <h4 class="font-medium text-orange-800 mb-3">⚡ Move to Tipping Bay</h4>
                            <p class="text-xs text-orange-700 mb-3">Move vehicle to an available tipping bay</p>
                            <form action="<?php echo e(route('app.factory-booking-workflow.move-to-bay', $factoryBooking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipping Bay</label>
                                    <select name="tipping_bay_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select bay...</option>
                                        <?php $__currentLoopData = $availableBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($bay->id); ?>"><?php echo e($bay->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                                    🏗️ Move to Bay
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if(in_array($currentStatus, ['at_bay', 'unloading'])): ?>
                        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                            <h4 class="font-medium text-green-800 mb-3">✅ Complete Tipping</h4>
                            <p class="text-xs text-green-700 mb-3">Mark tipping as complete and record actual quantities</p>
                            <form action="<?php echo e(route('app.factory-booking-workflow.complete-tipping', $factoryBooking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    ✅ Mark as Tipped
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($currentStatus === 'empty'): ?>
                        <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-3">📍 Move to Parking</h4>
                            <p class="text-xs text-purple-700 mb-3">Move empty vehicle back to parking area</p>
                            <form action="<?php echo e(route('app.factory-booking-workflow.move-trailer', $factoryBooking)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="move_to_collection">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Parking Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        <?php $__currentLoopData = $availableLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($location->id); ?>">
                                                <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    📍 Move to Parking
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($currentStatus === 'back_to_parking'): ?>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <h4 class="font-medium text-gray-800 mb-3">🏁 Departure Flow</h4>
                            <p class="text-xs text-gray-700 mb-3">Record how the vehicle is leaving the premises</p>
                            <form action="<?php echo e(route('app.factory-booking-workflow.trailer-depart', $factoryBooking)); ?>" method="POST" class="space-y-3">
                                <?php echo csrf_field(); ?>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">What happened?</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="departure_scenario" value="completed_with_trailer" required class="mr-2">
                                            <span class="text-sm">🚛✅ Job Complete - Left WITH trailer</span>
                                        </label>
                                        <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                                            <input type="radio" name="departure_scenario" value="completed_dropped_trailer" required class="mr-2">
                                            <span class="text-sm">🚛📦 Unit Left - Trailer DROPPED</span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                                    <input type="text" name="departure_notes" placeholder="e.g., Driver requested early departure..." 
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <button type="submit" class="w-full px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                                    🏁 Record Departure
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        
        <?php if($factoryBooking->movements && $factoryBooking->movements->count() > 0): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">📊 Movement History</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php $__currentLoopData = $factoryBooking->movements->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0 mt-1">
                                    <?php
                                        $statusIcon = match($movement->current_status) {
                                            'arrived' => '🚛',
                                            'in_parking' => '📍',
                                            'at_bay' => '🏗️',
                                            'unloading' => '⚡',
                                            'empty' => '✅',
                                            'back_to_parking' => '📍',
                                            'departed' => '🏁',
                                            default => '📋'
                                        };
                                    ?>
                                    <span class="text-2xl"><?php echo e($statusIcon); ?></span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-gray-900">
                                            <?php echo e($statusLabels[$movement->current_status][0] ?? ucwords(str_replace('_', ' ', $movement->current_status))); ?>

                                        </h4>
                                        <span class="text-sm text-gray-500">
                                            <?php echo e($movement->created_at->format('d M Y, H:i')); ?>

                                        </span>
                                    </div>
                                    <?php if($movement->operation_notes): ?>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo e($movement->operation_notes); ?></p>
                                    <?php endif; ?>
                                    <?php if($movement->tippingLocation): ?>
                                        <p class="text-xs text-gray-500 mt-1">Location: <?php echo e($movement->tippingLocation->name); ?></p>
                                    <?php endif; ?>
                                    <?php if($movement->tippingBay): ?>
                                        <p class="text-xs text-gray-500 mt-1">Bay: <?php echo e($movement->tippingBay->name); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-booking-workflow/show.blade.php ENDPATH**/ ?>