<?php if (isset($component)) { $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448 = $attributes; } ?>
<?php $component = App\View\Components\SiteAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SiteAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            🚛 Gate Arrival Processing - SEPARATE FORM VERSION - <?php echo e($booking->booking_reference); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            
            <!-- Booking Summary -->
            <div class="px-6 py-4 border-b bg-green-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Customer:</strong> <?php echo e($booking->customer->name ?? 'N/A'); ?><br>
                        <strong>Booking Type:</strong> <?php echo e($booking->bookingType->name ?? 'N/A'); ?>

                    </div>
                    <div>
                        <strong>Depot:</strong> <?php echo e($booking->slot->depot->name); ?><br>
                        <strong>Scheduled:</strong> <?php echo e($booking->slot->start_at->format('d-M-Y H:i')); ?>

                    </div>
                    <div>
                        <strong>Expected:</strong> <?php echo e($booking->total_expected_cases ?? 0); ?> cases, <?php echo e($booking->total_expected_pallets ?? 0); ?> pallets<br>
                        <?php if($booking->estimated_arrival): ?>
                            <strong>Est. Arrival:</strong> <?php echo e($booking->estimated_arrival->format('d-M-Y H:i')); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('site.bookings.arrival', $booking)); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                
                <h3 class="text-lg font-medium text-gray-900 mb-6">🚪 Gate Processing - Vehicle Arrival</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Required Vehicle Registration -->
                    <div class="md:col-span-2">
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="<?php echo e(old('vehicle_registration', $booking->vehicle_registration)); ?>"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-lg p-3">
                        <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-sm text-gray-500 mt-1"><strong>REQUIRED:</strong> Must be entered to process arrival</p>
                    </div>

                    <!-- Container/Trailer Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Container/Trailer Number
                        </label>
                        <input type="text" name="container_number"
                               value="<?php echo e(old('container_number', $booking->container_number)); ?>"
                               placeholder="e.g., CONT123456 or TR123456"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <?php $__errorArgs = ['container_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
                    </div>

                    <!-- Transport Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Carrier Company <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="carrier_company" required
                               value="<?php echo e(old('carrier_company', $booking->carrier_company)); ?>"
                               placeholder="e.g., ABC Transport Ltd"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <?php $__errorArgs = ['carrier_company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                    </div>


                    <!-- Tipping Location Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🚛 Tipping Drop Location</label>
                        <select name="tipping_location_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">– Assign Drop Location –</option>
                            <?php if(isset($tippingLocations)): ?>
                                <?php $__currentLoopData = $tippingLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($location->id); ?>" 
                                            <?php if(old('tipping_location_id', $booking->tipping_location_id) == $location->id): echo 'selected'; endif; ?>>
                                        <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                        <?php $__errorArgs = ['tipping_location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle to drop zone</p>
                    </div>


                    <!-- Tipping Bay Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🏗️ Tipping Bay (Direct Assignment)</label>
                        <select name="tipping_bay_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">– Skip to bay directly –</option>
                            <?php if(isset($tippingBays)): ?>
                                <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($bay->id); ?>" 
                                            <?php if(old('tipping_bay_id', $booking->tipping_bay_id) == $bay->id): echo 'selected'; endif; ?>
                                            <?php if($bay->is_occupied): echo 'disabled'; endif; ?>>
                                        <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>) 
                                        <?php if($bay->is_occupied): ?>
                                            - Occupied
                                        <?php else: ?>
                                            - Available
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>
                        <?php $__errorArgs = ['tipping_bay_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle directly to tipping bay</p>
                    </div>

                </div>

                <?php if($booking->special_instructions): ?>
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p class="text-yellow-700"><?php echo e($booking->special_instructions); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <div class="mt-8 bg-green-50 p-4 rounded-lg">
                    <div class="flex justify-end space-x-3">
                        <a href="<?php echo e(route('site.bookings.index')); ?>" 
                           class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-green-600 text-white text-lg font-medium rounded-lg hover:bg-green-700">
                            ✅ Process Arrival
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $attributes = $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $component = $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/site-admin/arrival-form.blade.php ENDPATH**/ ?>