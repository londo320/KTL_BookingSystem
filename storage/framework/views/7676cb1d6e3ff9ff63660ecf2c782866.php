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
                <h2 class="font-semibold text-xl text-gray-800">Edit Tipping Location</h2>
                <p class="text-sm text-gray-600 mt-1"><?php echo e($tippingLocation->name); ?> - <?php echo e($tippingLocation->depot->name); ?></p>
            </div>
            <div class="flex space-x-2">
                <a href="<?php echo e(route('app.tipping-locations.show', $tippingLocation)); ?>" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View Location
                </a>
                <a href="<?php echo e(route('app.tipping-locations.index')); ?>" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Locations
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-4xl mx-auto">
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
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">📍 Edit Location</h3>
                <p class="text-sm text-gray-600 mt-1">Update location settings and capacity</p>
            </div>
            <form method="POST" action="<?php echo e(route('app.tipping-locations.update', $tippingLocation)); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="depot_id">
                                Depot <span class="text-red-500">*</span>
                            </label>
                            <select name="depot_id" id="depot_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select depot...</option>
                                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($depot->id); ?>" <?php echo e((old('depot_id', $tippingLocation->depot_id) == $depot->id) ? 'selected' : ''); ?>>
                                        <?php echo e($depot->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                                Location Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name', $tippingLocation->name)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., Parking Area A, Trailer Park 1" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="code">
                                Location Code
                            </label>
                            <input type="text" name="code" id="code" value="<?php echo e(old('code', $tippingLocation->code)); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., DZ-A, TP-1">
                            <p class="text-xs text-gray-500 mt-1">Short code for easy identification (optional)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="location_type">
                                Location Type <span class="text-red-500">*</span>
                            </label>
                            <select name="location_type" id="location_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="parking" <?php echo e(old('location_type', $tippingLocation->location_type) == 'parking' ? 'selected' : ''); ?>>Parking Area</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">parking areas for incoming trailers, parking areas for awaiting pickup</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="capacity">
                                Capacity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="capacity" id="capacity" value="<?php echo e(old('capacity', $tippingLocation->capacity)); ?>" min="1" max="50"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">
                                Maximum number of trailers that can be dropped here
                                <?php if($tippingLocation->getCurrentOccupancy() > 0): ?>
                                    <br><span class="text-orange-600">Currently: <?php echo e($tippingLocation->getCurrentOccupancy()); ?> trailers on-site</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="description">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Additional details about this location..."><?php echo e(old('description', $tippingLocation->description)); ?></textarea>
                    </div>
                    <div>
                        <div class="flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   <?php echo e(old('is_active', $tippingLocation->is_active) ? 'checked' : ''); ?>

                                   class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label class="ml-2 block text-sm text-gray-700" for="is_active">
                                Active (available for use)
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            Inactive locations won't be available for trailer drops
                            <?php if($tippingLocation->getCurrentOccupancy() > 0): ?>
                                <br><span class="text-orange-600">⚠️ Warning: This location currently has trailers</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="<?php echo e(route('app.tipping-locations.show', $tippingLocation)); ?>" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Update Location
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-locations/edit.blade.php ENDPATH**/ ?>