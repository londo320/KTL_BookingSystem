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
                <h2 class="font-semibold text-xl text-gray-800">🔗 Reconnect Trailer to Vehicle</h2>
                <p class="text-sm text-gray-600 mt-1">Booking #<?php echo e($booking->id); ?> - <?php echo e($booking->customer->name ?? 'No Customer'); ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('admin.dropped-trailers.index')); ?>" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Dropped Trailers
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 max-w-4xl mx-auto">
        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-bold">Reconnection Failed</h4>
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <div class="mb-6 p-6 bg-orange-50 border border-orange-200 rounded-lg">
            <h3 class="text-lg font-semibold text-orange-800 mb-3">🚛 Trailer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Current Location</p>
                    <p class="font-medium">
                        <?php if($booking->tippingBay): ?>
                            <?php echo e($booking->tippingBay->name); ?> (Bay)
                        <?php elseif($booking->tippingLocation): ?>
                            <?php echo e($booking->tippingLocation->name); ?> (Drop Zone)
                        <?php else: ?>
                            Location not set
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Container/Trailer</p>
                    <p class="font-medium font-mono"><?php echo e($booking->container_number ?? 'Not specified'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tipping Status</p>
                    <div><?php echo $booking->tipping_status_badge; ?></div>
                </div>
            </div>
            
            
            <?php if($booking->vehicle_registration): ?>
                <div class="mt-4 pt-4 border-t border-orange-200">
                    <p class="text-sm text-gray-600 mb-2">Original Delivery Vehicle</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Registration:</strong> <?php echo e($booking->vehicle_registration); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <div class="mt-4 pt-4 border-t border-orange-200">
                <p class="text-sm text-gray-600 mb-2">Timeline</p>
                <div class="space-y-1 text-sm">
                    <?php if($booking->trailer_dropped_at): ?>
                        <div>• Dropped: <?php echo e($booking->trailer_dropped_at->format('M j, Y H:i')); ?></div>
                    <?php endif; ?>
                    <?php if($booking->tipping_completed_at): ?>
                        <div>• Tipping completed: <?php echo e($booking->tipping_completed_at->format('M j, Y H:i')); ?></div>
                    <?php endif; ?>
                    <div class="text-gray-500">• Duration on site: <?php echo e($booking->trailer_dropped_at ? $booking->trailer_dropped_at->diffForHumans(null, true) : 'Unknown'); ?></div>
                </div>
            </div>
        </div>

        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚚 New Vehicle Details</h3>
                <p class="text-gray-600 mt-1">Enter details for the vehicle collecting the trailer</p>
            </div>
            
            <form method="POST" action="<?php echo e(route('admin.dropped-trailers.reconnect', $booking)); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="<?php echo e(old('vehicle_registration')); ?>"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
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
                        <p class="text-xs text-gray-500 mt-1">Registration of the vehicle collecting the trailer</p>
                    </div>


                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
                        <textarea name="departure_notes" rows="3"
                                  class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                  placeholder="Optional notes about the departure..."><?php echo e(old('departure_notes')); ?></textarea>
                        <?php $__errorArgs = ['departure_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="text-yellow-600 text-xl">⚠️</div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Departure Confirmation</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This action will:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Connect the trailer to the new vehicle</li>
                                    <li>Mark the trailer as departed from site</li>
                                    <li>Update the booking with departure time</li>
                                    <li>Free up the tipping bay/location for other use</li>
                                    <li>Complete the booking workflow</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="<?php echo e(route('admin.dropped-trailers.index')); ?>" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        🔗 Reconnect & Depart
                    </button>
                </div>
            </form>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/dropped-trailers/reconnect.blade.php ENDPATH**/ ?>