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
                <h2 class="font-semibold text-xl text-gray-800">Tipping Bays Management</h2>
                <p class="text-sm text-gray-600 mt-1">Manage tipping bays for each depot</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex items-center space-x-2">
                    <select name="depot_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All Depots</option>
                        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($depot->id); ?>" <?php echo e(request('depot_id') == $depot->id ? 'selected' : ''); ?>>
                                <?php echo e($depot->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </form>
                <a href="<?php echo e(route('app.tipping-bays.create')); ?>" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + New Bay
                </a>
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
                <h3 class="text-lg font-semibold text-gray-800">🚛 Tipping Bays (<?php echo e($bays->total()); ?>)</h3>
            </div>
            <?php if($bays->isEmpty()): ?>
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-4">🚛</div>
                    <p class="text-lg mb-2">No tipping bays found</p>
                    <p class="text-sm mb-4">Create tipping bays where trailers can be processed and unloaded.</p>
                    <a href="<?php echo e(route('app.tipping-bays.create')); ?>" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create First Bay
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bay
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Depot
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Equipment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Booking
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $bays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($bay->name); ?></div>
                                            <?php if($bay->code): ?>
                                                <div class="text-sm text-gray-500">Code: <?php echo e($bay->code); ?></div>
                                            <?php endif; ?>
                                            <?php if($bay->description): ?>
                                                <div class="text-xs text-gray-400 mt-1"><?php echo e(Str::limit($bay->description, 50)); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e($bay->depot->name); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if(!empty($bay->equipment)): ?>
                                            <div class="space-y-1">
                                                <?php $__currentLoopData = $bay->equipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $equipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded"><?php echo e($equipment); ?></span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">None specified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if(!$bay->is_active): ?>
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                        <?php elseif($bay->is_occupied): ?>
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Occupied</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php if($bay->currentBooking): ?>
                                            <div>
                                                <div class="font-medium"><?php echo e($bay->currentBooking->customer->name); ?></div>
                                                <div class="text-xs text-gray-500">Booking #<?php echo e($bay->currentBooking->id); ?></div>
                                                <div class="text-xs"><?php echo $bay->currentBooking->tipping_status_badge; ?></div>
                                                <?php if($bay->currentBooking->tipping_started_at): ?>
                                                    <div class="text-xs text-gray-400"><?php echo e($bay->currentBooking->tipping_started_at->diffForHumans()); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="<?php echo e(route('app.tipping-bays.show', $bay)); ?>" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <?php if($bay->depot_id === $defaultDepotId): ?>
                                            <a href="<?php echo e(route('app.tipping-bays.edit', $bay)); ?>" 
                                               class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        <?php else: ?>
                                            <span class="text-gray-400" title="Can only edit bays in your default depot">Edit</span>
                                        <?php endif; ?>
                                        <?php if($bay->currentBooking): ?>
                                            <a href="<?php echo e(route('app.tipping-workflow.show', $bay->currentBooking)); ?>" 
                                               class="text-orange-600 hover:text-orange-900">Manage</a>
                                        <?php endif; ?>
                                        <?php if($bay->is_occupied): ?>
                                            <form method="POST" action="<?php echo e(route('app.tipping-bays.mark-available', $bay)); ?>" 
                                                  class="inline-block" onsubmit="return confirm('Mark this bay as available?');">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" class="text-green-600 hover:text-green-900">Free Up</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if(!$bay->is_occupied && $bay->depot_id === $defaultDepotId): ?>
                                            <form method="POST" action="<?php echo e(route('app.tipping-bays.destroy', $bay)); ?>" 
                                                  class="inline-block" onsubmit="return confirm('Are you sure you want to delete this bay?');">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        <?php elseif($bay->is_occupied): ?>
                                            <span class="text-gray-400" title="Cannot delete - bay is occupied">Delete</span>
                                        <?php else: ?>
                                            <span class="text-gray-400" title="Can only delete bays in your default depot">Delete</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    <?php echo e($bays->links()); ?>

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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-bays/index.blade.php ENDPATH**/ ?>