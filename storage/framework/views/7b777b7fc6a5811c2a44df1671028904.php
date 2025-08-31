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
                <h2 class="font-semibold text-xl text-gray-800">Tipping Locations Management</h2>
                <div class="text-sm text-gray-600 mt-1">
                    <p>Manage trailer drop locations for each depot</p>
                    <?php if(!$currentDepotId): ?>
                        <div class="mt-1">
                            <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
                            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
                        </div>
                    <?php else: ?>
                        <?php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); ?>
                        <div class="mt-1">
                            <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600"><?php echo e($currentDepot?->name ?? 'Unknown Depot'); ?></span></span>
                            <?php if($currentDepotId == $defaultDepotId): ?>
                                <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
                            <?php else: ?>
                                <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex items-center space-x-2">
                    <label for="depot_id" class="text-sm font-medium text-gray-700">View:</label>
                    <select name="depot_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All Depots (View Only)</option>
                        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
                                <?php echo e($depot->name); ?> <?php echo e($depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
                <?php $canTakeAction = !$currentDepotId || $currentDepotId == $defaultDepotId; ?>
                <?php if($canTakeAction): ?>
                    <a href="<?php echo e(route('app.tipping-locations.create')); ?>" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        + New Location
                    </a>
                <?php else: ?>
                    <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded cursor-not-allowed"
                          title="Actions only available for your default depot">
                        + New Location
                    </span>
                <?php endif; ?>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-7xl mx-auto">
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📍 Drop Locations (<?php echo e($locations->total()); ?>)</h3>
            </div>
            <?php if($locations->isEmpty()): ?>
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-4">📍</div>
                    <p class="text-lg mb-2">No tipping locations found</p>
                    <p class="text-sm mb-4">Create drop locations where trailers can wait before being moved to tipping bays.</p>
                    <a href="<?php echo e(route('app.tipping-locations.create')); ?>" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create First Location
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Depot
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Capacity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Occupancy
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($location->name); ?></div>
                                            <?php if($location->code): ?>
                                                <div class="text-sm text-gray-500">Code: <?php echo e($location->code); ?></div>
                                            <?php endif; ?>
                                            <?php if($location->description): ?>
                                                <div class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($location->description, 50)); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($location->depot->name); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php
                                            $typeColors = [
                                                'parking' => 'bg-blue-100 text-blue-800',
                                                'parking' => 'bg-green-100 text-green-800',
                                                'general' => 'bg-gray-100 text-gray-800'
                                            ];
                                            $typeLabels = [
                                                'parking' => 'Parking Area',
                                                'parking' => 'Parking Area',
                                                'general' => 'General'
                                            ];
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($typeColors[$location->location_type] ?? $typeColors['general']); ?>">
                                            <?php echo e($typeLabels[$location->location_type] ?? ucfirst(str_replace('_', ' ', $location->location_type))); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="text-lg font-semibold"><?php echo e($location->capacity); ?></div>
                                            <div class="ml-2 text-xs text-gray-500">trailers</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php 
                                            $occupancy = $location->getCurrentOccupancy();
                                            $available = $location->getAvailableCapacity();
                                            $percentage = $location->capacity > 0 ? ($occupancy / $location->capacity) * 100 : 0;
                                        ?>
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span><?php echo e($occupancy); ?>/<?php echo e($location->capacity); ?></span>
                                                    <span><?php echo e($available); ?> available</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full <?php echo e($percentage > 80 ? 'bg-red-500' : ($percentage > 60 ? 'bg-yellow-500' : 'bg-green-500')); ?>" 
                                                         style="width: <?php echo e($percentage); ?>%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if($location->is_active): ?>
                                            <?php if($location->isAvailable()): ?>
                                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Full</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="<?php echo e(route('app.tipping-locations.show', $location)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <?php $canTakeAction = $location->depot_id == $defaultDepotId; ?>
                                        <?php if($canTakeAction): ?>
                                            <a href="<?php echo e(route('app.tipping-locations.edit', $location)); ?>" 
                                               class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                            <?php if(!$location->is_active): ?>
                                                <!-- Reactivate button for inactive locations -->
                                                <form method="POST" action="<?php echo e(route('app.tipping-locations.toggle-active', $location)); ?>" 
                                                      class="inline-block" onsubmit="return confirm('Reactivate this location? It will become available for new bookings.');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button type="submit" class="text-green-600 hover:text-green-900">Reactivate</button>
                                                </form>
                                            <?php elseif($location->activeBookings()->count() === 0): ?>
                                                <!-- Delete/Deactivate button for active locations with no bookings -->
                                                <form method="POST" action="<?php echo e(route('app.tipping-locations.destroy', $location)); ?>" 
                                                      class="inline-block" onsubmit="return confirm('Are you sure you want to delete this location?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-gray-400" title="Cannot delete - has active bookings">Delete</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 cursor-not-allowed" 
                                                  title="Actions only available for your default depot">Edit</span>
                                            <?php if(!$location->is_active): ?>
                                                <span class="text-gray-400 cursor-not-allowed" 
                                                      title="Actions only available for your default depot">Reactivate</span>
                                            <?php else: ?>
                                                <span class="text-gray-400 cursor-not-allowed" 
                                                      title="Actions only available for your default depot">Delete</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($locations->links()); ?>

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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-locations/index.blade.php ENDPATH**/ ?>