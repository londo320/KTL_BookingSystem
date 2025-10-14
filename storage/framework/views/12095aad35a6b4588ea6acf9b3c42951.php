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
                <h2 class="font-semibold text-xl text-gray-800">Create Tipping Bay</h2>
                <p class="text-sm text-gray-600 mt-1">Add a new tipping bay</p>
            </div>
            <a href="<?php echo e(route('app.tipping-bays.index')); ?>" 
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                ← Back to Bays
            </a>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-4xl mx-auto">
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
                <h3 class="text-xl font-semibold text-gray-800">🚛 New Tipping Bay</h3>
                <p class="text-sm text-gray-600 mt-1">Configure a bay where trailers can be tipped and unloaded</p>
            </div>
            <form method="POST" action="<?php echo e(route('app.tipping-bays.store')); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="depot_id">
                                Depot <span class="text-red-500">*</span>
                            </label>
                            <select name="depot_id" id="depot_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select depot...</option>
                                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($depot->id); ?>" <?php echo e(old('depot_id') == $depot->id ? 'selected' : ''); ?>>
                                        <?php echo e($depot->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                                Bay Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., Bay 1, Tipping Bay A" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="code">
                                Bay Code
                            </label>
                            <input type="text" name="code" id="code" value="<?php echo e(old('code')); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., BAY-1, TB-A">
                            <p class="text-xs text-gray-500 mt-1">Short code for easy identification (optional)</p>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       <?php echo e(old('is_active', true) ? 'checked' : ''); ?>

                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-700" for="is_active">
                                    Active (available for use)
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">
                                Inactive bays won't be available for tipping
                            </p>
                            <div class="flex items-center">
                                <input type="hidden" name="show_on_map" value="0">
                                <input type="checkbox" name="show_on_map" id="show_on_map" value="1" 
                                       <?php echo e(old('show_on_map', true) ? 'checked' : ''); ?>

                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-700" for="show_on_map">
                                    Show on depot map
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">
                                Controls whether this bay appears on the visual depot map
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="description">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Additional details about this bay..."><?php echo e(old('description')); ?></textarea>
                    </div>
                    <!-- Map Position Settings -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-medium text-gray-800 mb-4">🗺️ Map Position Settings</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="map_x">
                                    Map X Position (%)
                                </label>
                                <input type="number" name="map_x" id="map_x" 
                                       value="<?php echo e(old('map_x')); ?>" 
                                       min="0" max="100" step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       placeholder="e.g., 25.5">
                                <p class="text-xs text-gray-500 mt-1">Horizontal position on map (0-100%)</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="map_y">
                                    Map Y Position (%)
                                </label>
                                <input type="number" name="map_y" id="map_y" 
                                       value="<?php echo e(old('map_y')); ?>" 
                                       min="0" max="100" step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       placeholder="e.g., 65.2">
                                <p class="text-xs text-gray-500 mt-1">Vertical position on map (0-100%)</p>
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="text-blue-400">ℹ️</div>
                                </div>
                                <div class="ml-3">
                                    <h5 class="text-sm font-medium text-blue-800">Map Position Tips:</h5>
                                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                                        <li>• Leave positions empty to position manually using the map editor</li>
                                        <li>• Use the <a href="<?php echo e(route('app.depot-map.manage-positions', auth()->user()->depot_id ?? $depots->first()?->id ?? 1)); ?>" class="underline hover:no-underline">Map Position Manager</a> for drag-and-drop positioning</li>
                                        <li>• Position values are percentages relative to the map image size</li>
                                        <li>• (0,0) is top-left corner, (100,100) is bottom-right corner</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Equipment Available
                        </label>
                        <div id="equipment-container">
                            <?php if(old('equipment')): ?>
                                <?php $__currentLoopData = old('equipment'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $equipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($equipment): ?>
                                        <div class="flex items-center mb-2 equipment-item">
                                            <input type="text" name="equipment[]" value="<?php echo e($equipment); ?>" 
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                   placeholder="e.g., Forklift, Crane, Conveyor">
                                            <button type="button" onclick="removeEquipment(this)" 
                                                    class="ml-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                Remove
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                        <button type="button" onclick="addEquipment()" 
                                class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            + Add Equipment
                        </button>
                        <p class="text-xs text-gray-500 mt-1">List any special equipment available at this bay</p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="<?php echo e(route('app.tipping-bays.index')); ?>" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create Bay
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function addEquipment() {
            const container = document.getElementById('equipment-container');
            const div = document.createElement('div');
            div.className = 'flex items-center mb-2 equipment-item';
            div.innerHTML = `
                <input type="text" name="equipment[]" value="" 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="e.g., Forklift, Crane, Conveyor">
                <button type="button" onclick="removeEquipment(this)" 
                        class="ml-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Remove
                </button>
            `;
            container.appendChild(div);
        }
        function removeEquipment(button) {
            button.parentElement.remove();
        }
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/tipping-bays/create.blade.php ENDPATH**/ ?>