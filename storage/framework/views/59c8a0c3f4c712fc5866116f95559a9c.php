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
                <h2 class="font-semibold text-xl text-gray-800">🏗️ Dropped Trailers On-Site</h2>
                <p class="text-sm text-gray-600 mt-1">Manage trailers currently dropped and awaiting tipping or departure</p>
            </div>
            <div class="text-sm">
                <?php if(!$currentDepotId): ?>
                    <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
                    <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
                <?php else: ?>
                    <?php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); ?>
                    <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600"><?php echo e($currentDepot?->name ?? 'Unknown Depot'); ?></span></span>
                    <?php if($currentDepotId == $defaultDepotId): ?>
                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
                    <?php else: ?>
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6">
        <?php if(session('success')): ?>
            <div class="mb-6 max-w-7xl mx-auto p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        
        <div class="max-w-7xl mx-auto mb-6 bg-white p-4 rounded-lg shadow">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                
                <div class="min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">View</label>
                    <select name="depot_id" class="w-full border-gray-300 rounded-lg">
                        <option value="" <?php echo e(!$currentDepotId ? 'selected' : ''); ?>>All Depots (View Only)</option>
                        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
                                <?php echo e($depot->name); ?> <?php echo e($depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="min-w-48">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">All Statuses</option>
                        <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(request('status') == $value): echo 'selected'; endif; ?>>
                                <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        🔍 Filter
                    </button>
                    <a href="<?php echo e(route('app.dropped-trailers.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Clear
                    </a>
                </div>
            </form>
        </div>
        
        <div class="max-w-7xl mx-auto mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="text-2xl font-bold text-blue-600"><?php echo e($droppedTrailers->total()); ?></div>
                <div class="text-sm text-blue-800">Total Trailers On-Site</div>
            </div>
            <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                <div class="text-2xl font-bold text-orange-600"><?php echo e($droppedTrailers->where('tipping_status', 'trailer_dropped')->count()); ?></div>
                <div class="text-sm text-orange-800">Awaiting Bay Assignment</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="text-2xl font-bold text-green-600"><?php echo e($droppedTrailers->where('tipping_status', 'tipping_in_progress')->count()); ?></div>
                <div class="text-sm text-green-800">Currently Tipping</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="text-2xl font-bold text-purple-600"><?php echo e($droppedTrailers->where('tipping_status', 'tipping_completed')->count()); ?></div>
                <div class="text-sm text-purple-800">Ready for Departure</div>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto bg-white rounded-lg shadow overflow-hidden">
            <?php if($droppedTrailers->isEmpty()): ?>
                <div class="p-12 text-center">
                    <div class="text-4xl mb-4">🚛</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers On-Site</h3>
                    <p class="text-gray-500">No trailers are currently dropped and awaiting processing.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Booking
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dropped
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $droppedTrailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" class="text-blue-600 hover:text-blue-900">
                                                        #<?php echo e($booking->id); ?>

                                                    </a>
                                                </div>
                                                <div class="text-sm text-gray-500"><?php echo e($booking->slot->depot->name); ?></div>
                                                <?php if($booking->vehicle_registration): ?>
                                                    <div class="text-xs text-gray-500 font-mono"><?php echo e($booking->vehicle_registration); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo e($booking->customer->name ?? 'No Customer'); ?></div>
                                        <?php if($booking->container_number): ?>
                                            <div class="text-xs text-gray-500 font-mono"><?php echo e($booking->container_number); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if($booking->tippingBay): ?>
                                            <div class="font-medium"><?php echo e($booking->tippingBay->name); ?></div>
                                            <div class="text-xs">Bay</div>
                                        <?php elseif($booking->tippingLocation): ?>
                                            <div class="font-medium"><?php echo e($booking->tippingLocation->name); ?></div>
                                            <div class="text-xs">Drop Location</div>
                                        <?php else: ?>
                                            <span class="text-gray-400">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo $booking->tipping_status_badge; ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if($booking->trailer_dropped_at): ?>
                                            <?php echo e($booking->trailer_dropped_at->format('M j, H:i')); ?>

                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if($booking->trailer_dropped_at): ?>
                                            <?php echo e($booking->trailer_dropped_at->diffForHumans(null, true)); ?>

                                        <?php else: ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">
                                            View
                                        </a>
                                        <?php if(in_array($booking->tipping_status, ['tipping_completed'])): ?>
                                            <?php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; ?>
                                            <?php if($canTakeAction): ?>
                                                <a href="<?php echo e(route('app.dropped-trailers.reconnect.form', $booking)); ?>" 
                                                   class="text-green-600 hover:text-green-900">
                                                    🔗 Reconnect
                                                </a>
                                            <?php else: ?>
                                                <span class="text-gray-400 cursor-not-allowed" 
                                                      title="Actions only available for your default depot">
                                                    🔗 Reconnect
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="px-6 py-3 border-t border-gray-200">
                    <?php echo e($droppedTrailers->links()); ?>

                </div>
            <?php endif; ?>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/dropped-trailers/index.blade.php ENDPATH**/ ?>