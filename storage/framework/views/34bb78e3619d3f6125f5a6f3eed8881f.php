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
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Transfer Booking to New Bay</h2>
                <p class="text-sm text-gray-600 mt-1">Booking #<?php echo e($booking->id); ?> - <?php echo e($booking->customer->name ?? 'No Customer'); ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Booking
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-4xl mx-auto">
        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-bold">Transfer Failed</h4>
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">🚛 Current Bay Assignment</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Current Bay</p>
                    <p class="font-medium"><?php echo e($booking->tippingBay->name ?? 'Not assigned'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Depot</p>
                    <p class="font-medium"><?php echo e($booking->tippingBay->depot->name ?? 'N/A'); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Status</p>
                    <div><?php echo $booking->tipping_status_badge; ?></div>
                </div>
            </div>
            <?php if($booking->moved_to_bay_at): ?>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Moved to Current Bay</p>
                    <p class="text-gray-800"><?php echo e($booking->moved_to_bay_at->format('M j, Y H:i')); ?></p>
                </div>
            <?php endif; ?>
            <?php if($booking->bay_transferred_at): ?>
                <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded">
                    <p class="text-sm font-medium text-orange-800">Previous Transfer</p>
                    <p class="text-sm text-orange-700">
                        Transferred <?php echo e($booking->bay_transferred_at->format('M j, Y H:i')); ?>

                        <?php if($booking->bay_transfer_reason): ?>
                            - <?php echo e($booking->bay_transfer_reason); ?>

                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🔄 Transfer to New Bay</h3>
            </div>
            <form method="POST" action="<?php echo e(route('app.bookings.transfer-bay', $booking)); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select New Bay</label>
                    <select name="new_bay_id" required class="w-full border-gray-300 rounded-lg">
                        <option value="">– Choose Available Bay –</option>
                        <?php $__currentLoopData = $availableBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($bay->id); ?>" <?php if(old('new_bay_id') == $bay->id): echo 'selected'; endif; ?>>
                                <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>)
                                <?php if($bay->is_occupied): ?>
                                    - Occupied
                                <?php else: ?>
                                    - Available
                                <?php endif; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['new_bay_id'];
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
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Reason</label>
                    <select name="transfer_reason" required class="w-full border-gray-300 rounded-lg">
                        <option value="">– Select Reason –</option>
                        <option value="Equipment failure" <?php if(old('transfer_reason') == 'Equipment failure'): echo 'selected'; endif; ?>>Equipment failure</option>
                        <option value="Maintenance required" <?php if(old('transfer_reason') == 'Maintenance required'): echo 'selected'; endif; ?>>Maintenance required</option>
                        <option value="Operational efficiency" <?php if(old('transfer_reason') == 'Operational efficiency'): echo 'selected'; endif; ?>>Operational efficiency</option>
                        <option value="Health and safety concern" <?php if(old('transfer_reason') == 'Health and safety concern'): echo 'selected'; endif; ?>>Health and safety concern</option>
                        <option value="Customer request" <?php if(old('transfer_reason') == 'Customer request'): echo 'selected'; endif; ?>>Customer request</option>
                        <option value="Other" <?php if(old('transfer_reason') == 'Other'): echo 'selected'; endif; ?>>Other</option>
                    </select>
                    <?php $__errorArgs = ['transfer_reason'];
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
                
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="text-yellow-600 text-xl">⚠️</div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Transfer Confirmation</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This action will:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Mark the current bay as available</li>
                                    <li>Assign the booking to the new bay</li>
                                    <li>Record the transfer time and reason</li>
                                    <li>Add a note to the booking's tipping log</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                        🔄 Transfer Bay
                    </button>
                </div>
            </form>
        </div>
        
        <?php if($availableBays->isEmpty()): ?>
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 font-medium">⚠️ No Available Bays</p>
                <p class="text-red-700 text-sm">There are currently no available bays for transfer. Please wait for a bay to become available or contact an administrator.</p>
            </div>
        <?php else: ?>
            <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h4 class="font-medium text-gray-800">Available Bays (<?php echo e($availableBays->count()); ?>)</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $availableBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-3 border border-gray-200 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium"><?php echo e($bay->name); ?></p>
                                        <p class="text-sm text-gray-600"><?php echo e($bay->depot->name); ?></p>
                                        <?php if($bay->description): ?>
                                            <p class="text-xs text-gray-500 mt-1"><?php echo e($bay->description); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded 
                                        <?php echo e($bay->is_occupied ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'); ?>">
                                        <?php echo e($bay->is_occupied ? 'Occupied' : 'Available'); ?>

                                    </span>
                                </div>
                                <?php if(!empty($bay->equipment)): ?>
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-600">Equipment:</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            <?php $__currentLoopData = $bay->equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs"><?php echo e($equipment); ?></span>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/transfer-bay.blade.php ENDPATH**/ ?>