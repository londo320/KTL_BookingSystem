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
                <h2 class="font-semibold text-xl text-gray-800">🗺️ Select Map File - <?php echo e($depot->name); ?></h2>
                <p class="text-sm text-gray-600 mt-1">Choose which map file to use for this depot</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="<?php echo e(route('admin.depot-map.index')); ?>" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    🗺️ View Map
                </a>
                <a href="<?php echo e(route('admin.depots.index')); ?>" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
                    ← Back to Depots
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-6 max-w-4xl mx-auto px-4">
        
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <?php if(session('error')): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>

        <!-- Current Map Status -->
        <div class="mb-6 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Current Map Configuration</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-600">Current Depot:</label>
                            <div class="text-lg font-semibold"><?php echo e($depot->name); ?></div>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-600">Current Map File:</label>
                            <div class="text-lg <?php echo e($depot->map_file ? 'text-green-600' : 'text-red-600'); ?>">
                                <?php echo e($depot->map_file ?: 'No map file selected'); ?>

                            </div>
                        </div>
                        <?php if($depot->map_notes): ?>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Notes:</label>
                                <div class="text-sm text-gray-700 bg-gray-50 p-2 rounded"><?php echo e($depot->map_notes); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if($depot->map_file): ?>
                    <div>
                        <label class="text-sm font-medium text-gray-600 block mb-2">Current Map Preview:</label>
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <?php if(file_exists(public_path('images/depot-maps/' . $depot->map_file))): ?>
                                <img src="<?php echo e(asset('images/depot-maps/' . $depot->map_file)); ?>" 
                                     alt="<?php echo e($depot->name); ?> Map" 
                                     class="max-w-full h-32 object-contain rounded">
                            <?php else: ?>
                                <div class="text-red-500 text-center py-4">
                                    ⚠️ Map file not found: <?php echo e($depot->map_file); ?>

                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Map File Upload and Selection Form -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">📁 Upload or Select Map File</h3>
                <p class="text-sm text-gray-600 mt-1">Upload a new SVG map file or choose from existing files</p>
            </div>
            
            <!-- File Upload Section -->
            <div class="p-6 border-b border-gray-100 bg-blue-50">
                <h4 class="text-md font-semibold text-gray-800 mb-4">📤 Upload New Map File</h4>
                <form method="POST" action="<?php echo e(route('admin.depot-map.upload-map-file')); ?>" enctype="multipart/form-data" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    
                    <input type="hidden" name="depot_id" value="<?php echo e($depot->id); ?>">
                    
                    <div>
                        <label for="map_file_upload" class="block text-sm font-medium text-gray-700 mb-2">
                            Choose SVG Map File:
                        </label>
                        <input type="file" 
                               name="map_file_upload" 
                               id="map_file_upload" 
                               accept=".svg,.png,.jpg,.jpeg,.gif"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                               onchange="previewUploadFile(this)">
                        <p class="text-xs text-gray-500 mt-1">Recommended: SVG files for best quality. Max size: 10MB</p>
                    </div>
                    
                    <div id="upload-preview" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview:</label>
                        <div class="border border-gray-200 rounded-lg p-4 bg-white">
                            <img id="preview-image" src="" alt="Upload Preview" class="max-w-full h-32 object-contain rounded">
                        </div>
                    </div>
                    
                    <div>
                        <label for="upload_map_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Map Notes (optional):
                        </label>
                        <textarea name="map_notes" id="upload_map_notes" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Add notes about this map layout..."><?php echo e($depot->map_notes); ?></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:bg-gray-400"
                                id="upload-submit" disabled>
                            📤 Upload and Set as Map
                        </button>
                    </div>
                </form>
            </div>
            
            <form method="POST" action="<?php echo e(route('admin.depot-map.update-map-file')); ?>" class="p-6">
                <?php echo csrf_field(); ?>
                
                <!-- Depot Selection -->
                <div class="mb-6">
                    <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Depot:
                    </label>
                    <select name="depot_id" id="depot_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            onchange="window.location.href='<?php echo e(route('admin.depot-map.select-map-file', '')); ?>/' + this.value">
                        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($d->id); ?>" <?php echo e($d->id === $depot->id ? 'selected' : ''); ?>>
                                <?php echo e($d->name); ?><?php echo e($d->map_file ? ' (has map)' : ' (no map)'); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Existing Map File Selection -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-3">🗂️ Or Select Existing Map File</h4>
                    <label for="map_file" class="block text-sm font-medium text-gray-700 mb-2">
                        Choose from uploaded files:
                    </label>
                    
                    <?php if(count($availableFiles) > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <?php $__currentLoopData = $availableFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="map_file" value="<?php echo e($file); ?>" 
                                           class="sr-only peer" 
                                           <?php echo e($depot->map_file === $file ? 'checked' : ''); ?>>
                                    <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-colors relative">
                                        <!-- Selected indicator -->
                                        <div class="absolute top-2 right-2 hidden peer-checked:block">
                                            <div class="bg-blue-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">
                                                ✓
                                            </div>
                                        </div>
                                        
                                        <!-- Current map indicator -->
                                        <?php if($depot->map_file === $file): ?>
                                            <div class="absolute top-2 left-2">
                                                <div class="bg-green-500 text-white rounded-full px-2 py-1 text-xs font-bold">
                                                    CURRENT
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="aspect-video bg-gray-100 rounded mb-2 flex items-center justify-center overflow-hidden">
                                            <?php if(file_exists(public_path('images/depot-maps/' . $file))): ?>
                                                <img src="<?php echo e(asset('images/depot-maps/' . $file)); ?>" 
                                                     alt="<?php echo e($file); ?>" 
                                                     class="max-w-full max-h-full object-contain">
                                            <?php else: ?>
                                                <div class="text-gray-400">📄 <?php echo e(strtoupper(pathinfo($file, PATHINFO_EXTENSION))); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm font-medium text-center"><?php echo e($file); ?></div>
                                        <div class="text-xs text-gray-500 text-center mb-2">
                                            <?php echo e(strtoupper(pathinfo($file, PATHINFO_EXTENSION))); ?> • 
                                            <?php echo e(number_format(filesize(public_path('images/depot-maps/' . $file)) / 1024, 1)); ?> KB
                                        </div>
                                        
                                        <!-- Delete button -->
                                        <div class="text-center">
                                            <button type="button" 
                                                    onclick="deleteMapFile('<?php echo e($file); ?>')" 
                                                    class="px-2 py-1 bg-red-100 text-red-600 rounded text-xs hover:bg-red-200 transition-colors"
                                                    title="Delete this file">
                                                🗑️ Delete
                                            </button>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <div class="text-gray-400 text-4xl mb-2">📁</div>
                            <div class="text-gray-600 mb-2">No existing map files found</div>
                            <div class="text-sm text-gray-500">
                                Use the upload section above to add your first map file
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Map Notes -->
                <div class="mb-6">
                    <label for="map_notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Map Notes (optional):
                    </label>
                    <textarea name="map_notes" id="map_notes" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Add any notes about this map layout, bay locations, etc."><?php echo e($depot->map_notes); ?></textarea>
                </div>

                <!-- Submit Button -->
                <?php if(count($availableFiles) > 0): ?>
                    <!-- Clear Map Option -->
                    <?php if($depot->map_file): ?>
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h5 class="text-sm font-medium text-yellow-800 mb-2">Clear Current Map</h5>
                            <p class="text-sm text-yellow-700 mb-3">Remove the current map file assignment (file will not be deleted)</p>
                            <button type="button" 
                                    onclick="clearMapFile()"
                                    class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 text-sm">
                                🚫 Clear Map Assignment
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" 
                                onclick="window.location.href='<?php echo e(route('admin.depot-map.index')); ?>'"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            💾 Update Map File
                        </button>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Upload Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="text-blue-400">ℹ️</div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Map File Guidelines:</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• <strong>SVG files recommended</strong> - scalable vector graphics provide the best quality at all zoom levels</li>
                        <li>• PNG/JPG also supported - good for photo-based depot layouts</li>
                        <li>• Maximum file size: 10MB</li>
                        <li>• Use descriptive filenames like <code class="bg-blue-100 px-1 rounded">Depot_London.svg</code></li>
                        <li>• Upload files are automatically saved and appear in the selection list</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript for upload preview -->
    <script>
    function previewUploadFile(input) {
        const file = input.files[0];
        const submitBtn = document.getElementById('upload-submit');
        const previewDiv = document.getElementById('upload-preview');
        const previewImg = document.getElementById('preview-image');
        
        if (file) {
            // Enable submit button
            submitBtn.disabled = false;
            
            // Show preview for images
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.classList.add('hidden');
            }
        } else {
            submitBtn.disabled = true;
            previewDiv.classList.add('hidden');
        }
    }
    
    function deleteMapFile(filename) {
        if (confirm(`Are you sure you want to delete "${filename}"? This action cannot be undone.`)) {
            // Create a form to submit the delete request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("admin.depot-map.delete-map-file")); ?>';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfToken);
            
            // Add method override for DELETE
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Add filename
            const filenameField = document.createElement('input');
            filenameField.type = 'hidden';
            filenameField.name = 'filename';
            filenameField.value = filename;
            form.appendChild(filenameField);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    function clearMapFile() {
        if (confirm('Are you sure you want to clear the current map assignment? The file will not be deleted.')) {
            // Create a form to submit the clear request
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo e(route("admin.depot-map.update-map-file")); ?>';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrfToken);
            
            // Add depot_id
            const depotIdField = document.createElement('input');
            depotIdField.type = 'hidden';
            depotIdField.name = 'depot_id';
            depotIdField.value = '<?php echo e($depot->id); ?>';
            form.appendChild(depotIdField);
            
            // Don't add map_file field - this will clear it
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        }
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/depot-map/select-map-file.blade.php ENDPATH**/ ?>