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
                <h2 class="font-semibold text-xl text-gray-800">🎯 Manage Item Positions - <?php echo e($depot->name); ?></h2>
                <p class="text-sm text-gray-600 mt-1">Click and drag bays and locations to position them on your depot map</p>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="toggleFullScreen()" class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm" id="fullscreen-btn">
                    🖥️ Full Screen
                </button>
                <a href="<?php echo e(route('app.depot-map.index')); ?>" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    🗺️ View Map
                </a>
                <a href="<?php echo e(route('app.bookings.index')); ?>" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
                    ← Back to Bookings
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-full mx-auto px-4">
        <!-- Instructions -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="text-blue-400">ℹ️</div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">How to position items:</h3>
                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                        <li>• <strong>Drag</strong> bay/location markers to position them on your map</li>
                        <li>• <strong>Click</strong> a bay marker to open styling controls on the right →</li>
                        <li>• Use the item list on the right to show/hide specific items</li>
                        <li>• Positions are saved automatically when you drag an item</li>
                        <li>• Only positioned items will appear on the live depot map</li>
                        <li>• <strong>Bays</strong> = Small single-vehicle spots | <strong>Locations</strong> = Large multi-vehicle zones</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
            <!-- Map Area (3/5 width) -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold">🏭 <?php echo e($depot->name); ?> Map Editor</h3>
                    </div>
                    <div class="p-6">
                        <!-- Map Container -->
                        <div class="relative bg-gray-100 rounded-lg border-2 border-gray-300 overflow-hidden" style="min-height: 700px;" id="map-container">
                            <!-- Depot Map Image -->
                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                <?php if($depot->map_file && file_exists(public_path('images/depot-maps/' . $depot->map_file))): ?>
                                    <div class="relative max-w-full max-h-full" id="map-image-container">
                                        <img src="<?php echo e(asset('images/depot-maps/' . $depot->map_file)); ?>" 
                                             alt="<?php echo e($depot->name); ?> Layout" 
                                             class="max-w-full max-h-full object-contain rounded-lg"
                                             id="depot-map-image"
                                             style="transform-origin: center;">
                                        <!-- Draggable Bay Markers -->
                                        <?php $__currentLoopData = $bays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="absolute bay-marker cursor-move" 
                                                 data-bay-id="<?php echo e($bay->id); ?>"
                                                 data-bay-name="<?php echo e($bay->name); ?>"
                                                 style="top: <?php echo e($bay->map_y ?? 50); ?>%; left: <?php echo e($bay->map_x ?? 50); ?>%; <?php echo e(!$bay->show_on_map ? 'display: none;' : ''); ?>"
                                                 title="Drag to reposition <?php echo e($bay->name); ?>">
                                                <?php
                                                    $bayWidth = $bay->map_width ?? 60;
                                                    $bayHeight = $bay->map_height ?? 40;
                                                    $bayRotation = $bay->map_rotation ?? 0;
                                                    $bayTextSize = $bay->text_size ?? 'xs';
                                                    $bayTextColor = $bay->text_color ?? '#ffffff';
                                                ?>
                                                <div class="bay-box" 
                                                     style="width: <?php echo e($bayWidth); ?>px; height: <?php echo e($bayHeight); ?>px; 
                                                            background: rgba(59, 130, 246, 0.8); 
                                                            border: 2px solid #2563eb; 
                                                            border-radius: 4px;
                                                            transform: rotate(<?php echo e($bayRotation); ?>deg);
                                                            display: flex;
                                                            flex-direction: column;
                                                            justify-content: center;
                                                            align-items: center;">
                                                    <div class="text-<?php echo e($bayTextSize); ?> font-bold text-center" style="color: <?php echo e($bayTextColor); ?>;">
                                                        <?php echo e($bay->code ?? Str::limit($bay->name, 6)); ?>

                                                    </div>
                                                </div>
                                                <!-- Drag handle -->
                                                <div class="absolute -top-1 -right-1 w-4 h-4 bg-blue-600 rounded-full border-2 border-white text-white text-xs flex items-center justify-center cursor-move">
                                                    ⋮⋮
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <!-- Draggable Location Markers -->
                                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $typeIcons = [
                                                    'drop_zone' => '📦',
                                                    'collection_zone' => '🚚',
                                                    'general' => '📍'
                                                ];
                                                $icon = $typeIcons[$location->location_type] ?? '📍';
                                                $locationWidth = $location->map_width ?? 100;
                                                $locationHeight = $location->map_height ?? 60;
                                                $locationRotation = $location->map_rotation ?? 0;
                                                $locationTextSize = $location->text_size ?? 'xs';
                                                $locationTextColor = $location->text_color ?? '#ffffff';
                                            ?>
                                            <div class="absolute location-marker cursor-move" 
                                                 data-location-id="<?php echo e($location->id); ?>"
                                                 data-location-name="<?php echo e($location->name); ?>"
                                                 style="top: <?php echo e($location->map_y ?? 30); ?>%; left: <?php echo e($location->map_x ?? 30); ?>%; <?php echo e(!$location->show_on_map ? 'display: none;' : ''); ?>"
                                                 title="Drag to reposition <?php echo e($location->name); ?>">
                                                <div class="location-box" 
                                                     style="width: <?php echo e($locationWidth); ?>px; height: <?php echo e($locationHeight); ?>px; 
                                                            background: rgba(34, 197, 94, 0.8); 
                                                            border: 3px solid #16a34a; 
                                                            border-radius: 8px;
                                                            display: flex;
                                                            flex-direction: column;
                                                            justify-content: center;
                                                            align-items: center;
                                                            transform: rotate(<?php echo e($locationRotation); ?>deg);">
                                                    <div class="text-sm mb-1"><?php echo e($icon); ?></div>
                                                    <div class="text-<?php echo e($locationTextSize); ?> font-bold text-center leading-tight" style="color: <?php echo e($locationTextColor); ?>;">
                                                        <?php echo e($location->code ?? Str::limit($location->name, 8)); ?>

                                                    </div>
                                                    <div class="text-xs text-center" style="color: <?php echo e($locationTextColor); ?>;">
                                                        <?php echo e($location->capacity); ?>

                                                    </div>
                                                </div>
                                                <!-- Drag handle -->
                                                <div class="absolute -top-2 -right-2 w-5 h-5 bg-green-600 rounded-full border-2 border-white text-white text-xs flex items-center justify-center cursor-move">
                                                    ⋮⋮
                                                </div>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center justify-center h-full w-full bg-gray-200 rounded-lg">
                                        <div class="text-center text-gray-500">
                                            <div class="text-2xl mb-2">🗺️</div>
                                            <div>No map file found for <?php echo e($depot->name); ?></div>
                                            <div class="text-sm mt-1">Please upload a map file first</div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Control Panels (2/5 width) -->
            <div class="xl:col-span-2 space-y-6">
                <!-- Bay Styling Panel - Compact Version - TOP PRIORITY -->
                <div id="bay-styling-panel" class="bg-white rounded-lg shadow border-2 border-yellow-400 hidden">
                    <div class="p-3 border-b border-gray-200 bg-yellow-50">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800">🎨 Bay Styling</h4>
                            <button onclick="closeBayStyling()" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                        </div>
                        <p class="text-xs text-gray-600 mt-1" id="selected-bay-name">Select a bay to customize</p>
                    </div>
                    <div class="p-3 space-y-3">
                        <!-- Size Controls -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Width (px)</label>
                                <input type="number" id="bay-width" min="30" max="200" value="60" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Height (px)</label>
                                <input type="number" id="bay-height" min="20" max="150" value="40" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                            </div>
                        </div>
                        <!-- Rotation Control -->
                        <div>
                            <label class="text-xs font-medium text-gray-700">Rotation: <span id="rotation-value" class="font-bold text-blue-600">0°</span></label>
                            <input type="range" id="bay-rotation" min="0" max="360" value="0" step="5"
                                   class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        </div>
                        <!-- Text Controls -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Text Size</label>
                                <select id="bay-text-size" class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                    <option value="xs">XS</option>
                                    <option value="sm">SM</option>
                                    <option value="md">MD</option>
                                    <option value="lg">LG</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Text Color</label>
                                <div class="flex items-center space-x-1">
                                    <input type="color" id="bay-text-color" value="#ffffff" 
                                           class="w-8 h-8 border border-gray-300 rounded cursor-pointer">
                                    <input type="text" id="bay-text-color-hex" value="#ffffff" 
                                           class="flex-1 px-1 py-1 border border-gray-300 rounded text-xs font-mono">
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="flex space-x-2 pt-2 border-t border-gray-200">
                            <button onclick="applyBayChanges()" 
                                    class="flex-1 px-3 py-2 bg-blue-600 text-white rounded text-sm font-medium hover:bg-blue-700">
                                ✓ Apply
                            </button>
                            <button onclick="resetBayToDefault()" 
                                    class="flex-1 px-3 py-2 bg-gray-300 text-gray-700 rounded text-sm hover:bg-gray-400">
                                🔄 Reset
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Location Styling Panel -->
                <div id="location-styling-panel" class="bg-white rounded-lg shadow border-2 border-green-400 hidden">
                    <div class="p-3 border-b border-gray-200 bg-green-50">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-800">🎨 Location Styling</h4>
                            <button onclick="closeLocationStyling()" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
                        </div>
                        <p class="text-xs text-gray-600 mt-1" id="selected-location-name">Select a location to customize</p>
                    </div>
                    <div class="p-3 space-y-3">
                        <!-- Size Controls -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Width (px)</label>
                                <input type="number" id="location-width" min="30" max="300" value="100" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Height (px)</label>
                                <input type="number" id="location-height" min="20" max="200" value="60" 
                                       class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                            </div>
                        </div>
                        <!-- Rotation Control -->
                        <div>
                            <label class="text-xs font-medium text-gray-700">Rotation: <span id="location-rotation-value" class="font-bold text-green-600">0°</span></label>
                            <input type="range" id="location-rotation" min="0" max="360" value="0" step="5"
                                   class="w-full h-3 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        </div>
                        <!-- Text Controls -->
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-700">Text Size</label>
                                <select id="location-text-size" class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                                    <option value="xs">XS</option>
                                    <option value="sm">SM</option>
                                    <option value="md">MD</option>
                                    <option value="lg">LG</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-700">Text Color</label>
                                <div class="flex items-center space-x-1">
                                    <input type="color" id="location-text-color" value="#ffffff" 
                                           class="w-8 h-8 border border-gray-300 rounded cursor-pointer">
                                    <input type="text" id="location-text-color-hex" value="#ffffff" 
                                           class="flex-1 px-1 py-1 border border-gray-300 rounded text-xs font-mono">
                                </div>
                            </div>
                        </div>
                        <!-- Action Buttons -->
                        <div class="flex space-x-2 pt-2 border-t border-gray-200">
                            <button onclick="applyLocationChanges()" 
                                    class="flex-1 px-3 py-2 bg-green-600 text-white rounded text-sm font-medium hover:bg-green-700">
                                ✓ Apply
                            </button>
                            <button onclick="resetLocationToDefault()" 
                                    class="flex-1 px-3 py-2 bg-gray-300 text-gray-700 rounded text-sm hover:bg-gray-400">
                                🔄 Reset
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Bay List -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">📍 Item Management</h4>
                        <p class="text-xs text-gray-500 mt-1"><?php echo e($bays->count()); ?> bays, <?php echo e($locations->count()); ?> locations for <?php echo e($depot->name); ?></p>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <div class="space-y-3">
                            <?php $__currentLoopData = $bays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox" 
                                               class="bay-visibility-toggle" 
                                               data-bay-id="<?php echo e($bay->id); ?>"
                                               <?php echo e($bay->show_on_map ? 'checked' : ''); ?>>
                                        <div>
                                            <div class="font-medium text-sm"><?php echo e($bay->name); ?></div>
                                            <?php if($bay->code): ?>
                                                <div class="text-xs text-gray-500"><?php echo e($bay->code); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        <?php if($bay->map_x && $bay->map_y): ?>
                                            <?php echo e(number_format($bay->map_x, 1)); ?>%, <?php echo e(number_format($bay->map_y, 1)); ?>%
                                        <?php else: ?>
                                            Not positioned
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <!-- Locations Section -->
                            <?php if($locations->count() > 0): ?>
                                <hr class="my-4 border-gray-200">
                                <h5 class="text-sm font-medium text-gray-600 mb-3">📦 Drop/Parking Areas</h5>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-blue-50">
                                        <div class="flex items-center space-x-3">
                                            <input type="checkbox" 
                                                   class="location-visibility-toggle" 
                                                   data-location-id="<?php echo e($location->id); ?>"
                                                   <?php echo e($location->show_on_map ? 'checked' : ''); ?>>
                                            <div>
                                                <div class="font-medium text-sm flex items-center space-x-1">
                                                    <?php
                                                        $typeIcons = [
                                                            'drop_zone' => '📦',
                                                            'collection_zone' => '🚚',
                                                            'general' => '📍'
                                                        ];
                                                        $icon = $typeIcons[$location->location_type] ?? '📍';
                                                    ?>
                                                    <span><?php echo e($icon); ?></span>
                                                    <span><?php echo e($location->name); ?></span>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <?php echo e($location->code ? $location->code . ' • ' : ''); ?>Capacity: <?php echo e($location->capacity); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            <?php if($location->map_x && $location->map_y): ?>
                                                <?php echo e(number_format($location->map_x, 1)); ?>%, <?php echo e(number_format($location->map_y, 1)); ?>%
                                            <?php else: ?>
                                                Not positioned
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Save Status -->
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-center">
                        <div id="save-status" class="text-sm text-gray-500">Ready to save changes</div>
                        <div id="save-indicator" class="mt-2 hidden">
                            <div class="inline-flex items-center space-x-2 text-green-600">
                                <div class="w-2 h-2 bg-green-600 rounded-full animate-pulse"></div>
                                <span class="text-xs">Saved</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let draggedItem = null;
        let dragType = null; // 'bay' or 'location'
        let mapImageContainer = document.getElementById('map-image-container');
        let mapContainer = document.getElementById('map-container');
        let saveTimeout = null;
        let selectedBay = null;
        // Make bay markers draggable and clickable for styling
        document.querySelectorAll('.bay-marker').forEach(marker => {
            let isDragging = false;
            let startX, startY;
            marker.addEventListener('mousedown', function(e) {
                startX = e.clientX;
                startY = e.clientY;
                isDragging = false;
                dragType = 'bay';
                startDrag(e);
            });
            marker.addEventListener('mousemove', function(e) {
                if (Math.abs(e.clientX - startX) > 5 || Math.abs(e.clientY - startY) > 5) {
                    isDragging = true;
                }
            });
            marker.addEventListener('mouseup', function(e) {
                if (!isDragging) {
                    // This was a click, not a drag - show styling panel
                    selectBayForStyling(this);
                }
                isDragging = false;
            });
        });
        // Make location markers draggable and clickable for styling
        document.querySelectorAll('.location-marker').forEach(marker => {
            let isDragging = false;
            let startX, startY;
            marker.addEventListener('mousedown', function(e) {
                startX = e.clientX;
                startY = e.clientY;
                isDragging = false;
                dragType = 'location';
                startDrag(e);
            });
            marker.addEventListener('mousemove', function(e) {
                if (Math.abs(e.clientX - startX) > 5 || Math.abs(e.clientY - startY) > 5) {
                    isDragging = true;
                }
            });
            marker.addEventListener('mouseup', function(e) {
                if (!isDragging) {
                    // This was a click, not a drag - show styling panel
                    selectLocationForStyling(this);
                }
                isDragging = false;
            });
        });
        // Handle bay visibility toggles
        document.querySelectorAll('.bay-visibility-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const bayId = this.dataset.bayId;
                const isVisible = this.checked;
                const marker = document.querySelector(`[data-bay-id="${bayId}"]`);
                if (marker) {
                    marker.style.display = isVisible ? 'block' : 'none';
                }
                // Save visibility change
                updateBayPosition(bayId, null, null, isVisible);
            });
        });
        // Handle location visibility toggles
        document.querySelectorAll('.location-visibility-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const locationId = this.dataset.locationId;
                const isVisible = this.checked;
                const marker = document.querySelector(`[data-location-id="${locationId}"]`);
                if (marker) {
                    marker.style.display = isVisible ? 'block' : 'none';
                }
                // Save visibility change
                updateLocationPosition(locationId, null, null, isVisible);
            });
        });
        function startDrag(e) {
            e.preventDefault();
            draggedItem = e.currentTarget;
            document.addEventListener('mousemove', drag);
            document.addEventListener('mouseup', stopDrag);
            draggedItem.style.zIndex = '1000';
        }
        function drag(e) {
            if (!draggedItem || !mapImageContainer) return;
            const rect = mapImageContainer.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            // Keep within bounds
            const clampedX = Math.max(0, Math.min(95, x));
            const clampedY = Math.max(0, Math.min(95, y));
            draggedItem.style.left = clampedX + '%';
            draggedItem.style.top = clampedY + '%';
        }
        function stopDrag(e) {
            if (!draggedItem || !mapImageContainer) return;
            document.removeEventListener('mousemove', drag);
            document.removeEventListener('mouseup', stopDrag);
            // Get final position relative to image container
            const rect = mapImageContainer.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            const clampedX = Math.max(0, Math.min(95, x));
            const clampedY = Math.max(0, Math.min(95, y));
            // Save position based on type
            if (dragType === 'bay') {
                const bayId = draggedItem.dataset.bayId;
                updateBayPosition(bayId, clampedX, clampedY, true);
            } else if (dragType === 'location') {
                const locationId = draggedItem.dataset.locationId;
                updateLocationPosition(locationId, clampedX, clampedY, true);
            }
            draggedItem.style.zIndex = '';
            draggedItem = null;
            dragType = null;
        }
        async function updateBayPosition(bayId, x, y, showOnMap) {
            try {
                const data = { bay_id: bayId, show_on_map: showOnMap };
                if (x !== null && y !== null) {
                    data.map_x = x;
                    data.map_y = y;
                }
                const response = await fetch('<?php echo e(route("app.depot-map.update-position")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    showSaveIndicator();
                    updatePositionDisplay(bayId, x, y);
                } else {
                    console.error('Failed to save position:', result.message);
                }
            } catch (error) {
                console.error('Error saving position:', error);
            }
        }
        async function updateLocationPosition(locationId, x, y, showOnMap) {
            try {
                const data = { location_id: locationId, show_on_map: showOnMap };
                if (x !== null && y !== null) {
                    data.map_x = x;
                    data.map_y = y;
                }
                const response = await fetch('<?php echo e(route("app.depot-map.update-location-position")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                if (result.success) {
                    showSaveIndicator();
                    updateLocationPositionDisplay(locationId, x, y);
                } else {
                    console.error('Failed to save location position:', result.message);
                }
            } catch (error) {
                console.error('Error saving location position:', error);
            }
        }
        function showSaveIndicator() {
            const indicator = document.getElementById('save-indicator');
            indicator.classList.remove('hidden');
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                indicator.classList.add('hidden');
            }, 2000);
        }
        function updatePositionDisplay(bayId, x, y) {
            if (x !== null && y !== null) {
                // Update the position display in the bay list
                const bayElement = document.querySelector(`[data-bay-id="${bayId}"]`).closest('.flex').querySelector('.text-xs.text-gray-400');
                if (bayElement) {
                    bayElement.textContent = `${x.toFixed(1)}%, ${y.toFixed(1)}%`;
                }
            }
        }
        function updateLocationPositionDisplay(locationId, x, y) {
            if (x !== null && y !== null) {
                // Update the position display in the location list
                const locationElement = document.querySelector(`[data-location-id="${locationId}"]`).closest('.flex').querySelector('.text-xs.text-gray-400');
                if (locationElement) {
                    locationElement.textContent = `${x.toFixed(1)}%, ${y.toFixed(1)}%`;
                }
            }
        }
        // Bay styling functions
        function selectBayForStyling(bayMarker) {
            selectedBay = bayMarker;
            const bayId = bayMarker.dataset.bayId;
            const bayName = bayMarker.dataset.bayName;
            // Show styling panel with attention animation
            const panel = document.getElementById('bay-styling-panel');
            panel.classList.remove('hidden');
            document.getElementById('selected-bay-name').textContent = `Customizing: ${bayName}`;
            // Scroll to top of right column to ensure panel is visible
            const rightColumn = document.querySelector('.xl\\:col-span-2');
            if (rightColumn) {
                rightColumn.scrollTop = 0;
            }
            // Load current bay settings
            loadBaySettings(bayMarker);
            // Highlight selected bay
            document.querySelectorAll('.bay-marker').forEach(m => m.classList.remove('selected'));
            bayMarker.classList.add('selected');
        }
        function loadBaySettings(bayMarker) {
            const box = bayMarker.querySelector('.bay-box');
            const text = bayMarker.querySelector('.bay-box div');
            // Get current styles from rendered elements
            const currentWidth = parseInt(box.style.width) || 60;
            const currentHeight = parseInt(box.style.height) || 40;
            const currentRotation = getRotationFromTransform(box.style.transform) || 0;
            const currentColor = text.style.color || '#ffffff';
            // Set form values
            document.getElementById('bay-width').value = currentWidth;
            document.getElementById('bay-height').value = currentHeight;
            document.getElementById('bay-rotation').value = currentRotation;
            document.getElementById('rotation-value').textContent = currentRotation + '°';
            // Convert RGB to hex if needed
            const hexColor = rgbToHex(currentColor) || currentColor;
            document.getElementById('bay-text-color').value = hexColor;
            document.getElementById('bay-text-color-hex').value = hexColor;
            // Set text size based on current class
            const textSizeClass = getTextSizeClass(text);
            document.getElementById('bay-text-size').value = textSizeClass;
        }
        // Helper function to convert RGB to hex
        function rgbToHex(rgb) {
            if (!rgb || rgb === 'white' || rgb === '#ffffff') return '#ffffff';
            if (rgb.startsWith('#')) return rgb;
            const result = rgb.match(/\d+/g);
            if (result && result.length >= 3) {
                const r = parseInt(result[0]);
                const g = parseInt(result[1]);
                const b = parseInt(result[2]);
                return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
            }
            return '#ffffff';
        }
        function getRotationFromTransform(transform) {
            if (!transform || transform === 'none') return 0;
            const match = transform.match(/rotate\\(([^)]+)deg\\)/);
            return match ? parseInt(match[1]) : 0;
        }
        function getTextSizeClass(element) {
            if (element.classList.contains('text-lg')) return 'lg';
            if (element.classList.contains('text-md')) return 'md';
            if (element.classList.contains('text-sm')) return 'sm';
            return 'xs';
        }
        function applyBayChanges() {
            if (!selectedBay) return;
            const bayId = selectedBay.dataset.bayId;
            const width = document.getElementById('bay-width').value;
            const height = document.getElementById('bay-height').value;
            const rotation = document.getElementById('bay-rotation').value;
            const textSize = document.getElementById('bay-text-size').value;
            const textColor = document.getElementById('bay-text-color-hex').value;
            // Apply changes visually
            updateBayVisuals(selectedBay, width, height, rotation, textSize, textColor);
            // Save to database
            saveBaySettings(bayId, width, height, rotation, textSize, textColor);
        }
        function updateBayVisuals(bayMarker, width, height, rotation, textSize, textColor) {
            const box = bayMarker.querySelector('.bay-box');
            const text = bayMarker.querySelector('.bay-box div');
            // Update size
            box.style.width = width + 'px';
            box.style.height = height + 'px';
            // Update rotation
            box.style.transform = `rotate(${rotation}deg)`;
            // Update text color
            text.style.color = textColor;
            // Update text size
            text.className = text.className.replace(/text-(xs|sm|md|lg)/, `text-${textSize}`);
            if (!text.className.includes('text-')) {
                text.className += ` text-${textSize}`;
            }
        }
        async function saveBaySettings(bayId, width, height, rotation, textSize, textColor) {
            try {
                const response = await fetch('<?php echo e(route("app.depot-map.update-position")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        bay_id: bayId,
                        map_width: parseInt(width),
                        map_height: parseInt(height),
                        map_rotation: parseFloat(rotation),
                        text_size: textSize,
                        text_color: textColor,
                        show_on_map: true
                    })
                });
                const result = await response.json();
                if (result.success) {
                    showSaveIndicator();
                } else {
                    console.error('Failed to save bay settings:', result.message);
                    alert('Failed to save settings: ' + result.message);
                }
            } catch (error) {
                console.error('Error saving bay settings:', error);
                alert('Error saving settings: ' + error.message);
            }
        }
        function resetBayToDefault() {
            if (!selectedBay) return;
            // Reset to default values
            document.getElementById('bay-width').value = 60;
            document.getElementById('bay-height').value = 40;
            document.getElementById('bay-rotation').value = 0;
            document.getElementById('rotation-value').textContent = '0°';
            document.getElementById('bay-text-size').value = 'xs';
            document.getElementById('bay-text-color').value = '#ffffff';
            document.getElementById('bay-text-color-hex').value = '#ffffff';
            // Apply defaults
            updateBayVisuals(selectedBay, 60, 40, 0, 'xs', '#ffffff');
            // Save defaults
            const bayId = selectedBay.dataset.bayId;
            saveBaySettings(bayId, 60, 40, 0, 'xs', '#ffffff');
        }
        function closeBayStyling() {
            document.getElementById('bay-styling-panel').classList.add('hidden');
            document.querySelectorAll('.bay-marker').forEach(m => m.classList.remove('selected'));
            selectedBay = null;
        }
        // Update rotation value display
        document.getElementById('bay-rotation').addEventListener('input', function() {
            document.getElementById('rotation-value').textContent = this.value + '°';
        });
        // Sync color picker with hex input
        document.getElementById('bay-text-color').addEventListener('input', function() {
            document.getElementById('bay-text-color-hex').value = this.value;
        });
        document.getElementById('bay-text-color-hex').addEventListener('input', function() {
            document.getElementById('bay-text-color').value = this.value;
        });
        // Location styling functions
        let selectedLocation = null;
        function selectLocationForStyling(locationMarker) {
            selectedLocation = locationMarker;
            const locationId = locationMarker.dataset.locationId;
            const locationName = locationMarker.dataset.locationName;
            // Hide bay styling panel and show location styling panel
            document.getElementById('bay-styling-panel').classList.add('hidden');
            const panel = document.getElementById('location-styling-panel');
            panel.classList.remove('hidden');
            document.getElementById('selected-location-name').textContent = `Customizing: ${locationName}`;
            // Scroll to top of right column to ensure panel is visible
            const rightColumn = document.querySelector('.xl\\:col-span-2');
            if (rightColumn) {
                rightColumn.scrollTop = 0;
            }
            // Load current location settings
            loadLocationSettings(locationMarker);
            // Highlight selected location
            document.querySelectorAll('.location-marker').forEach(m => m.classList.remove('selected'));
            locationMarker.classList.add('selected');
        }
        function loadLocationSettings(locationMarker) {
            const box = locationMarker.querySelector('.location-box');
            const text = locationMarker.querySelector('.location-box div:last-child');
            // Get current styles from rendered elements
            const currentWidth = parseInt(box.style.width) || 100;
            const currentHeight = parseInt(box.style.height) || 60;
            const currentRotation = getRotationFromTransform(box.style.transform) || 0;
            const currentColor = text.style.color || '#ffffff';
            // Set form values
            document.getElementById('location-width').value = currentWidth;
            document.getElementById('location-height').value = currentHeight;
            document.getElementById('location-rotation').value = currentRotation;
            document.getElementById('location-rotation-value').textContent = currentRotation + '°';
            // Convert RGB to hex if needed
            const hexColor = rgbToHex(currentColor) || currentColor;
            document.getElementById('location-text-color').value = hexColor;
            document.getElementById('location-text-color-hex').value = hexColor;
            // Set text size based on current class
            const textSizeClass = getTextSizeClass(text);
            document.getElementById('location-text-size').value = textSizeClass;
        }
        function applyLocationChanges() {
            if (!selectedLocation) return;
            const locationId = selectedLocation.dataset.locationId;
            const width = document.getElementById('location-width').value;
            const height = document.getElementById('location-height').value;
            const rotation = document.getElementById('location-rotation').value;
            const textSize = document.getElementById('location-text-size').value;
            const textColor = document.getElementById('location-text-color-hex').value;
            // Apply changes visually
            updateLocationVisuals(selectedLocation, width, height, rotation, textSize, textColor);
            // Save to database
            saveLocationSettings(locationId, width, height, rotation, textSize, textColor);
        }
        function updateLocationVisuals(locationMarker, width, height, rotation, textSize, textColor) {
            const box = locationMarker.querySelector('.location-box');
            const texts = locationMarker.querySelectorAll('.location-box div');
            // Update size
            box.style.width = width + 'px';
            box.style.height = height + 'px';
            // Update rotation
            box.style.transform = `rotate(${rotation}deg)`;
            // Update text color and size
            texts.forEach(text => {
                text.style.color = textColor;
                // Update text size class
                text.className = text.className.replace(/text-(xs|sm|md|lg)/, `text-${textSize}`);
            });
        }
        async function saveLocationSettings(locationId, width, height, rotation, textSize, textColor) {
            try {
                const response = await fetch('<?php echo e(route("app.depot-map.update-location-position")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        location_id: locationId,
                        map_width: parseInt(width),
                        map_height: parseInt(height),
                        map_rotation: parseFloat(rotation),
                        text_size: textSize,
                        text_color: textColor
                    })
                });
                const result = await response.json();
                if (result.success) {
                    console.log('Location settings saved:', result.message);
                } else {
                    console.error('Failed to save location settings:', result.message);
                }
            } catch (error) {
                console.error('Error saving location settings:', error);
            }
        }
        function resetLocationToDefault() {
            if (!selectedLocation) return;
            // Reset to default values
            document.getElementById('location-width').value = 100;
            document.getElementById('location-height').value = 60;
            document.getElementById('location-rotation').value = 0;
            document.getElementById('location-rotation-value').textContent = '0°';
            document.getElementById('location-text-size').value = 'xs';
            document.getElementById('location-text-color').value = '#ffffff';
            document.getElementById('location-text-color-hex').value = '#ffffff';
            // Apply defaults
            updateLocationVisuals(selectedLocation, 100, 60, 0, 'xs', '#ffffff');
            // Save defaults
            const locationId = selectedLocation.dataset.locationId;
            saveLocationSettings(locationId, 100, 60, 0, 'xs', '#ffffff');
        }
        function closeLocationStyling() {
            document.getElementById('location-styling-panel').classList.add('hidden');
            document.querySelectorAll('.location-marker').forEach(m => m.classList.remove('selected'));
            selectedLocation = null;
        }
        // Update location rotation value display
        document.getElementById('location-rotation').addEventListener('input', function() {
            document.getElementById('location-rotation-value').textContent = this.value + '°';
        });
        // Sync location color picker with hex input
        document.getElementById('location-text-color').addEventListener('input', function() {
            document.getElementById('location-text-color-hex').value = this.value;
        });
        document.getElementById('location-text-color-hex').addEventListener('input', function() {
            document.getElementById('location-text-color').value = this.value;
        });
        // Full screen functionality
        function toggleFullScreen() {
            const body = document.body;
            const container = document.querySelector('.py-6.max-w-full.mx-auto.px-4');
            const fullscreenBtn = document.getElementById('fullscreen-btn');
            if (body.classList.contains('fullscreen-mode')) {
                // Exit full screen
                body.classList.remove('fullscreen-mode');
                container.classList.remove('fullscreen-container');
                fullscreenBtn.innerHTML = '🖥️ Full Screen';
            } else {
                // Enter full screen
                body.classList.add('fullscreen-mode');
                container.classList.add('fullscreen-container');
                fullscreenBtn.innerHTML = '🗙 Exit Full Screen';
            }
        }
        // ESC key to exit full screen
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('fullscreen-mode')) {
                toggleFullScreen();
            }
        });
    </script>
    <style>
        .bay-marker:hover .bay-box {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .bay-marker {
            transition: transform 0.2s ease;
            user-select: none;
        }
        .bay-marker.selected .bay-box {
            border: 3px solid #fbbf24 !important;
            box-shadow: 0 0 0 2px #fef3c7;
        }
        .location-marker.selected .location-box {
            border: 4px solid #fbbf24 !important;
            box-shadow: 0 0 0 2px #fef3c7;
        }
        /* Full screen mode styles */
        .fullscreen-mode {
            overflow: hidden;
        }
        .fullscreen-mode header,
        .fullscreen-mode nav,
        .fullscreen-mode .sidebar {
            display: none !important;
        }
        .fullscreen-container {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            max-width: none !important;
            z-index: 9999 !important;
            background: #f3f4f6 !important;
            padding: 1rem !important;
            margin: 0 !important;
        }
        .fullscreen-container .grid {
            height: calc(100vh - 2rem) !important;
        }
        .fullscreen-container .bg-white.rounded-lg.shadow {
            height: 100% !important;
        }
        .fullscreen-container .relative.bg-gray-100.rounded-lg {
            height: calc(100% - 4rem) !important;
        }
        /* Adjust sidebar in fullscreen for positioning interface */
        .fullscreen-container .xl\\:col-span-2 {
            max-height: calc(100vh - 2rem) !important;
            overflow-y: auto !important;
        }
        .fullscreen-container #bay-styling-panel {
            position: sticky !important;
            top: 0 !important;
            z-index: 10 !important;
        }
        .fullscreen-container #location-styling-panel {
            position: sticky !important;
            top: 0 !important;
            z-index: 10 !important;
        }
        /* Make styling panels more prominent */
        #bay-styling-panel, #location-styling-panel {
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/depot-map/manage-positions.blade.php ENDPATH**/ ?>