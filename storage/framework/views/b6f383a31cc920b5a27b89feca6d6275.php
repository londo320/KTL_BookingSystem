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
            <div class="flex items-center space-x-6">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800">🗺️ <?php echo e($depot->name); ?> - Site Map</h2>
                    <p class="text-sm text-gray-600 mt-1">Real-time depot layout with live booking status</p>
                </div>
                <!-- Depot Filter Dropdown -->
                <?php if($userDepots->count() > 1): ?>
                <div>
                    <label for="depot-filter" class="block text-xs font-medium text-gray-600 mb-1">Switch Depot:</label>
                    <select id="depot-filter" 
                            class="px-3 py-1 bg-white border border-gray-300 rounded text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            onchange="switchDepot(this.value)">
                        <?php $__currentLoopData = $userDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $userDepot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($userDepot->id); ?>" <?php echo e($userDepot->id === $depot->id ? 'selected' : ''); ?>>
                                <?php echo e($userDepot->name); ?><?php echo e($userDepot->map_file ? ' 🗺️' : ''); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endif; ?>
            </div>
            <div class="flex items-center space-x-3">
                <div class="text-sm text-gray-600">
                    Last updated: <span id="last-updated"><?php echo e(now()->format('H:i:s')); ?></span>
                </div>
                <button onclick="refreshMapData()" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    🔄 Refresh
                </button>
                <?php
                    $routePrefix = request()->route()->getPrefix() === 'depot-admin' ? 'app.' : 'app.';
                ?>
                <a href="<?php echo e(route($routePrefix . 'depot-map.select-map-file', $depot->id)); ?>" class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
                    📁 Upload Map
                </a>
                <a href="<?php echo e(route($routePrefix . 'depot-map.manage-positions', $depot->id)); ?>" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                    🎯 Position Bays
                </a>
                <a href="<?php echo e(route($routePrefix . 'bookings.index')); ?>" class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
                    ← Back to Bookings
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6 max-w-full mx-auto px-4">
        <!-- Status Summary Cards -->
        <div class="mb-6 grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="text-sm text-gray-600">Available</div>
                <div class="text-2xl font-bold text-green-600" id="available-count"><?php echo e($activitySummary['available_locations']); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <div class="text-sm text-gray-600">Active Tipping</div>
                <div class="text-2xl font-bold text-red-600" id="active-count"><?php echo e($activitySummary['active_bookings']); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                <div class="text-sm text-gray-600">Awaiting Collection</div>
                <div class="text-2xl font-bold text-orange-600" id="waiting-count"><?php echo e($activitySummary['awaiting_collection']); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="text-sm text-gray-600">Today's Arrivals</div>
                <div class="text-2xl font-bold text-blue-600" id="arrivals-count"><?php echo e($activitySummary['todays_arrivals']); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="text-sm text-gray-600">Pending Arrivals</div>
                <div class="text-2xl font-bold text-purple-600" id="pending-count"><?php echo e($activitySummary['pending_arrivals']); ?></div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-gray-500">
                <div class="text-sm text-gray-600">Total Locations</div>
                <div class="text-2xl font-bold text-gray-600" id="total-count"><?php echo e($activitySummary['total_locations']); ?></div>
            </div>
        </div>
        <!-- Main Map and Activity Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Main Map Area (3/4 width) -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">🏭 <?php echo e($depot->name); ?> Layout</h3>
                            <div class="flex space-x-2">
                                <!-- Status Legend -->
                                <div class="flex items-center space-x-4 text-xs">
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-green-500 rounded"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-red-500 rounded"></div>
                                        <span>Active</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-orange-500 rounded"></div>
                                        <span>Collection</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-blue-500 rounded"></div>
                                        <span>Occupied</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <div class="w-3 h-3 bg-gray-500 rounded"></div>
                                        <span>Offline</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Map Container -->
                        <div class="relative bg-gray-100 rounded-lg border-2 border-gray-300 overflow-hidden" style="min-height: 700px;">
                            <!-- Depot Map Image with Overlays -->
                            <div class="absolute inset-0 flex items-center justify-center p-4">
                                <?php if($depot->map_file && file_exists(public_path('images/depot-maps/' . $depot->map_file))): ?>
                                    <div class="relative max-w-full max-h-full" id="map-image-container">
                                        <img src="<?php echo e(asset('images/depot-maps/' . $depot->map_file)); ?>" 
                                             alt="<?php echo e($depot->name); ?> Layout" 
                                             class="max-w-full max-h-full object-contain rounded-lg"
                                             id="depot-map-image"
                                             style="transform-origin: center; width: 100%; height: 100%;">
                                        <!-- Interactive Bay Overlays - positioned on the map image -->
                                        <?php if($bays->count() > 0): ?>
                                            <?php $__currentLoopData = $bays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $status = $bayStatuses[$bay->id]['status'] ?? 'available';
                                                $occupancy = $bayStatuses[$bay->id]['occupancy'] ?? 0;
                                                // Map status to colors
                                                $colors = [
                                                    'available' => 'rgba(34, 197, 94, 0.8)', // Green
                                                    'active' => 'rgba(239, 68, 68, 0.8)',    // Red  
                                                    'waiting_collection' => 'rgba(249, 115, 22, 0.8)', // Orange
                                                    'occupied' => 'rgba(59, 130, 246, 0.8)', // Blue
                                                    'full' => 'rgba(156, 163, 175, 0.8)',    // Gray
                                                    'offline' => 'rgba(107, 114, 128, 0.8)', // Dark Gray
                                                    'disabled' => 'rgba(239, 68, 68, 0.3)', // Red with low opacity - locked/disabled
                                                ];
                                                $borderColors = [
                                                    'available' => '#16a34a',
                                                    'active' => '#dc2626',
                                                    'waiting_collection' => '#ea580c',
                                                    'occupied' => '#2563eb',
                                                    'full' => '#9ca3af',
                                                    'offline' => '#6b7280',
                                                    'disabled' => '#dc2626', // Red border for disabled
                                                ];
                                                $bgColor = $colors[$status] ?? $colors['available'];
                                                $borderColor = $borderColors[$status] ?? $borderColors['available'];
                                                // Use stored coordinates if available, otherwise default position
                                                if ($bay->map_x !== null && $bay->map_y !== null && $bay->show_on_map) {
                                                    $position = [
                                                        'top' => $bay->map_y . '%', 
                                                        'left' => $bay->map_x . '%'
                                                    ];
                                                } else {
                                                    // Skip bays without coordinates or marked as hidden
                                                    continue;
                                                }
                                                // Use stored bay dimensions or defaults
                                                $width = ($bay->map_width ?? 60) . 'px';
                                                $height = ($bay->map_height ?? 40) . 'px';
                                                $rotation = $bay->map_rotation ?? 0;
                                                $textSize = $bay->text_size ?? 'xs';
                                                $textColor = $bay->text_color ?? '#ffffff';
                                            ?>
                                            <div class="absolute bay-overlay scalable-overlay" 
                                                 data-bay-id="<?php echo e($bay->id); ?>"
                                                 data-status="<?php echo e($status); ?>"
                                                 style="top: <?php echo e($position['top']); ?>; left: <?php echo e($position['left']); ?>;"
                                                 onclick="showBayDetails(<?php echo e($bay->id); ?>)">
                                                <div class="location-box cursor-pointer hover:scale-110 transition-transform duration-200" 
                                                     style="width: <?php echo e($width); ?>; height: <?php echo e($height); ?>; 
                                                            background: <?php echo e($bgColor); ?>; 
                                                            border: 2px solid <?php echo e($borderColor); ?>; 
                                                            border-radius: 4px;
                                                            transform: rotate(<?php echo e($rotation); ?>deg);"
                                                     title="<?php echo e($bay->name); ?> - <?php echo e(ucfirst($status)); ?> (<?php echo e($bay->is_occupied ? 'Occupied' : 'Available'); ?>)">
                                                    <div class="flex flex-col items-center justify-center h-full p-1">
                                                        <?php if($status === 'disabled'): ?>
                                                            <div class="text-sm text-center" style="color: <?php echo e($textColor); ?>;">🔒</div>
                                                            <div class="text-<?php echo e($textSize); ?> font-bold text-center leading-tight" style="color: <?php echo e($textColor); ?>;">
                                                                LOCKED
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="text-<?php echo e($textSize); ?> font-bold text-center leading-tight" style="color: <?php echo e($textColor); ?>;">
                                                                <?php echo e($bay->code ?? Str::limit($bay->name, 6)); ?>

                                                            </div>
                                                            <?php if($occupancy > 0): ?>
                                                                <div class="text-<?php echo e($textSize); ?> text-center" style="color: <?php echo e($textColor); ?>;">
                                                                    Occ
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                        <!-- Interactive Location Overlays - positioned on the map image -->
                                        <?php if($locations->count() > 0): ?>
                                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $status = $locationStatuses[$location->id]['status'] ?? 'available';
                                                $occupancy = $locationStatuses[$location->id]['occupancy'] ?? 0;
                                                $capacity = $location->capacity;
                                                $available = $capacity - $occupancy;
                                                // Map status to colors (same as bays)
                                                $colors = [
                                                    'available' => 'rgba(34, 197, 94, 0.8)', // Green
                                                    'active' => 'rgba(239, 68, 68, 0.8)',    // Red  
                                                    'waiting_collection' => 'rgba(249, 115, 22, 0.8)', // Orange
                                                    'occupied' => 'rgba(59, 130, 246, 0.8)', // Blue
                                                    'full' => 'rgba(156, 163, 175, 0.8)',    // Gray
                                                    'offline' => 'rgba(107, 114, 128, 0.8)', // Dark Gray
                                                ];
                                                $borderColors = [
                                                    'available' => '#16a34a',
                                                    'active' => '#dc2626',
                                                    'waiting_collection' => '#ea580c',
                                                    'occupied' => '#2563eb',
                                                    'full' => '#9ca3af',
                                                    'offline' => '#6b7280',
                                                    'disabled' => '#dc2626', // Red border for disabled
                                                ];
                                                $bgColor = $colors[$status] ?? $colors['available'];
                                                $borderColor = $borderColors[$status] ?? $borderColors['available'];
                                                // Use stored coordinates if available
                                                if ($location->map_x !== null && $location->map_y !== null && $location->show_on_map) {
                                                    $position = [
                                                        'top' => $location->map_y . '%', 
                                                        'left' => $location->map_x . '%'
                                                    ];
                                                } else {
                                                    // Skip locations without coordinates or marked as hidden
                                                    continue;
                                                }
                                                // Use stored location dimensions or defaults
                                                $width = ($location->map_width ?? 100) . 'px';
                                                $height = ($location->map_height ?? 60) . 'px';
                                                $rotation = $location->map_rotation ?? 0;
                                                $textSize = $location->text_size ?? 'xs';
                                                $textColor = $location->text_color ?? '#ffffff';
                                                // Location type icons
                                                $typeIcons = [
                                                    'drop_zone' => '📦',
                                                    'collection_zone' => '🚚',
                                                    'general' => '📍'
                                                ];
                                                $icon = $typeIcons[$location->location_type] ?? '📍';
                                            ?>
                                            <div class="absolute location-overlay scalable-overlay" 
                                                 data-location-id="<?php echo e($location->id); ?>"
                                                 data-status="<?php echo e($status); ?>"
                                                 style="top: <?php echo e($position['top']); ?>; left: <?php echo e($position['left']); ?>;"
                                                 onclick="showLocationDetails(<?php echo e($location->id); ?>)">
                                                <div class="location-box cursor-pointer hover:scale-105 transition-transform duration-200" 
                                                     style="width: <?php echo e($width); ?>; height: <?php echo e($height); ?>; 
                                                            background: <?php echo e($bgColor); ?>; 
                                                            border: 3px solid <?php echo e($borderColor); ?>; 
                                                            border-radius: 8px;
                                                            display: flex;
                                                            flex-direction: column;
                                                            justify-content: center;
                                                            align-items: center;
                                                            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                                                            transform: rotate(<?php echo e($rotation); ?>deg);"
                                                     title="<?php echo e($location->name); ?> - <?php echo e(ucfirst($status)); ?> (<?php echo e($occupancy); ?>/<?php echo e($capacity); ?>)">
                                                    <div class="text-sm mb-1"><?php echo e($icon); ?></div>
                                                    <div class="text-<?php echo e($textSize); ?> font-bold text-center leading-tight" style="color: <?php echo e($textColor); ?>;">
                                                        <?php echo e($location->code ?? Str::limit($location->name, 8)); ?>

                                                    </div>
                                                    <div class="text-xs text-white text-center">
                                                        <?php echo e($occupancy); ?>/<?php echo e($capacity); ?>

                                                        <?php if($available > 0): ?>
                                                            <div class="text-xs"><?php echo e($available); ?> free</div>
                                                        <?php else: ?>
                                                            <div class="text-xs">FULL</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="flex items-center justify-center h-full w-full bg-gray-200 rounded-lg">
                                        <div class="text-center text-gray-500">
                                            <div class="text-2xl mb-2">🗺️</div>
                                            <div>No map file found for <?php echo e($depot->name); ?></div>
                                            <div class="text-sm mb-4">Upload an SVG map file to visualize your depot layout</div>
                                            <a href="<?php echo e(route($routePrefix . 'depot-map.select-map-file', $depot->id)); ?>" class="inline-block px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
                                                📁 Upload Map File
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if($bays->count() === 0 && $locations->count() === 0): ?>
                                <!-- No positioned items message -->
                                <div class="absolute inset-0 flex items-center justify-center z-10">
                                    <div class="bg-white rounded-lg shadow-lg p-8 text-center max-w-md mx-4">
                                        <div class="text-6xl mb-4">🎯</div>
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Items Positioned</h3>
                                        <p class="text-gray-600 mb-6">
                                            You have <?php echo e(\App\Models\TippingBay::where('depot_id', $depot->id)->count()); ?> tipping bays and <?php echo e(\App\Models\TippingLocation::where('depot_id', $depot->id)->count()); ?> locations for <?php echo e($depot->name); ?>, but none have been positioned on the map yet.
                                        </p>
                                        <div class="space-y-3">
                                            <a href="<?php echo e(route($routePrefix . 'depot-map.manage-positions')); ?>?depot_id=<?php echo e($depot->id); ?>" 
                                               class="inline-block w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                                🎯 Position Items Now
                                            </a>
                                            <p class="text-xs text-gray-500">
                                                Drag and drop your bays onto the map to see them here
                                            </p>
                                            <p class="text-xs text-gray-400 mt-2">
                                                Debug: Route = <?php echo e($routePrefix); ?>depot-map.manage-positions<br>
                                                URL = <?php echo e(route($routePrefix . 'depot-map.manage-positions')); ?>

                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- Map Controls -->
                            <div class="absolute top-4 right-4 space-y-2">
                                <button onclick="toggleFullScreen()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50 text-sm" id="fullscreen-btn" title="Full Screen Dashboard">
                                    🖥️
                                </button>
                                <button onclick="zoomIn()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50 text-sm">
                                    🔍+
                                </button>
                                <button onclick="zoomOut()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50 text-sm">
                                    🔍-
                                </button>
                                <button onclick="resetZoom()" class="bg-white shadow-lg rounded p-2 hover:bg-gray-50 text-sm">
                                    🏠
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Activity Panel (1/4 width) -->
            <div class="space-y-6">
                <!-- Recent Activity -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">📝 Recent Activity</h4>
                    </div>
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <div id="recent-activity" class="space-y-3">
                            <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <div class="text-sm border-b border-gray-100 pb-2">
                                    <div class="font-medium text-gray-800"><?php echo e($activity['booking_reference']); ?></div>
                                    <div class="text-gray-600"><?php echo e($activity['action']); ?></div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo e($activity['location_name']); ?> • <?php echo e($activity['time_formatted']); ?>

                                        <?php if($activity['vehicle_registration']): ?>
                                            • <?php echo e($activity['vehicle_registration']); ?>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <div class="text-sm text-gray-500">No recent activity</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">⚡ Quick Actions</h4>
                    </div>
                    <div class="p-4 space-y-3">
                        <button onclick="showAllBookings()" class="w-full px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                            📋 View All Bookings
                        </button>
                        <button onclick="openTrailerCollection()" class="w-full px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                            🚛 Record Collection
                        </button>
                        <button onclick="openTippingWorkflow()" class="w-full px-3 py-2 bg-orange-600 text-white rounded text-sm hover:bg-orange-700">
                            🏗️ Tipping Workflow
                        </button>
                        <a href="<?php echo e(route('app.trailer-location-report')); ?>" class="block w-full px-3 py-2 bg-purple-600 text-white rounded text-sm hover:bg-purple-700 text-center">
                            📍 Location Report
                        </a>
                    </div>
                </div>
                <!-- Location List -->
                <div class="bg-white rounded-lg shadow">
                    <div class="p-4 border-b border-gray-200">
                        <h4 class="font-semibold text-gray-800">📍 Positioned Items</h4>
                        <?php if($bays->count() === 0 && $locations->count() === 0): ?>
                            <p class="text-xs text-gray-500 mt-1">No items positioned yet</p>
                        <?php else: ?>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo e($bays->count()); ?> bays, <?php echo e($locations->count()); ?> locations positioned
                            </p>
                        <?php endif; ?>
                    </div>
                    <div class="p-4 max-h-64 overflow-y-auto">
                        <?php if($bays->count() > 0): ?>
                            <div class="space-y-2 text-sm">
                                <?php $__currentLoopData = $bays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $status = $bayStatuses[$bay->id]['status'] ?? 'available';
                                    $occupancy = $bayStatuses[$bay->id]['occupancy'] ?? 0;
                                ?>
                                <div class="flex items-center justify-between cursor-pointer hover:bg-gray-50 p-2 rounded"
                                     onclick="showBayDetails(<?php echo e($bay->id); ?>)">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded <?php echo e($status === 'available' ? 'bg-green-500' :
                                            ($status === 'active' ? 'bg-red-500' :
                                            ($status === 'waiting_collection' ? 'bg-orange-500' :
                                            ($status === 'occupied' ? 'bg-blue-500' : 'bg-gray-500')))); ?>"></div>
                                        <span class="font-medium"><?php echo e($bay->name); ?></span>
                                    </div>
                                    <span class="text-xs text-gray-500"><?php echo e($bay->is_occupied ? 'Occupied' : 'Available'); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        <!-- Tipping Locations Section -->
                        <?php if($locations->count() > 0): ?>
                            <?php if($bays->count() > 0): ?>
                                <hr class="my-4 border-gray-200">
                                <h5 class="text-sm font-medium text-gray-600 mb-2">📦 Drop/Collection Zones</h5>
                            <?php endif; ?>
                            <div class="space-y-2 text-sm">
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $status = $locationStatuses[$location->id]['status'] ?? 'available';
                                    $occupancy = $locationStatuses[$location->id]['occupancy'] ?? 0;
                                    $typeIcons = [
                                        'drop_zone' => '📦',
                                        'collection_zone' => '🚚', 
                                        'general' => '📍'
                                    ];
                                    $icon = $typeIcons[$location->location_type] ?? '📍';
                                ?>
                                <div class="flex items-center justify-between cursor-pointer hover:bg-gray-50 p-2 rounded"
                                     onclick="showLocationDetails(<?php echo e($location->id); ?>)">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded <?php echo e($status === 'available' ? 'bg-green-500' :
                                            ($status === 'active' ? 'bg-red-500' :
                                            ($status === 'waiting_collection' ? 'bg-orange-500' :
                                            ($status === 'occupied' ? 'bg-blue-500' : 'bg-gray-500')))); ?>"></div>
                                        <span class="text-lg mr-1"><?php echo e($icon); ?></span>
                                        <span class="font-medium"><?php echo e($location->name); ?></span>
                                    </div>
                                    <span class="text-xs text-gray-500"><?php echo e($occupancy); ?>/<?php echo e($location->capacity); ?></span>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                        <?php if($bays->count() === 0 && $locations->count() === 0): ?>
                            <div class="text-center py-8">
                                <div class="text-gray-400 text-4xl mb-2">🎯</div>
                                <div class="text-sm text-gray-500 mb-3">No bays positioned</div>
                                <a href="<?php echo e(route($routePrefix . 'depot-map.manage-positions')); ?>?depot_id=<?php echo e($depot->id); ?>" 
                                   class="inline-block px-4 py-2 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                    Position Bays
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Location Details Modal -->
    <div id="location-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4 shadow-xl">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-lg font-semibold">Location Details</h3>
                <button onclick="closeLocationModal()" class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>
            </div>
            <div id="modal-content" class="space-y-3">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="closeLocationModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Close
                </button>
                <button id="modal-action-btn" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    View Bookings
                </button>
            </div>
        </div>
    </div>
    <script>
        // Real bay data from Laravel
        const bays = <?php echo json_encode($bays, 15, 512) ?>;
        const bayStatuses = <?php echo json_encode($bayStatuses, 15, 512) ?>;
        let currentZoom = 1;
        // Auto-refresh every 60 seconds
        setInterval(refreshMapData, 60000);
        // Refresh map data
        async function refreshMapData() {
            try {
                const response = await fetch('<?php echo e(route("app.depot-map.refresh")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        depot_id: <?php echo e($depot->id); ?>

                    })
                });
                if (response.ok) {
                    const data = await response.json();
                    updateLocationStatuses(data.location_statuses);
                    updateActivitySummary(data.activity_summary);
                    document.getElementById('last-updated').textContent = data.timestamp;
                }
            } catch (error) {
                console.error('Failed to refresh map data:', error);
            }
        }
        // Update location visual status
        function updateLocationStatuses(newStatuses) {
            Object.keys(newStatuses).forEach(locationId => {
                const status = newStatuses[locationId];
                const overlay = document.querySelector(`[data-location-id="${locationId}"]`);
                if (overlay) {
                    const box = overlay.querySelector('.location-box');
                    overlay.setAttribute('data-status', status.status);
                    // Update colors based on status
                    const colors = {
                        'available': { bg: 'rgba(34, 197, 94, 0.8)', border: '#16a34a' },
                        'active': { bg: 'rgba(239, 68, 68, 0.8)', border: '#dc2626' },
                        'waiting_collection': { bg: 'rgba(249, 115, 22, 0.8)', border: '#ea580c' },
                        'occupied': { bg: 'rgba(59, 130, 246, 0.8)', border: '#2563eb' },
                        'full': { bg: 'rgba(156, 163, 175, 0.8)', border: '#9ca3af' },
                        'disabled': { bg: 'rgba(239, 68, 68, 0.3)', border: '#dc2626' },
                        'offline': { bg: 'rgba(107, 114, 128, 0.8)', border: '#6b7280' }
                    };
                    const colorScheme = colors[status.status] || colors['available'];
                    box.style.background = colorScheme.bg;
                    box.style.borderColor = colorScheme.border;
                }
            });
        }
        // Update activity summary counts
        function updateActivitySummary(summary) {
            document.getElementById('available-count').textContent = summary.available_locations;
            document.getElementById('active-count').textContent = summary.active_bookings;
            document.getElementById('waiting-count').textContent = summary.awaiting_collection;
            document.getElementById('arrivals-count').textContent = summary.todays_arrivals;
            document.getElementById('pending-count').textContent = summary.pending_arrivals;
            document.getElementById('total-count').textContent = summary.total_locations;
        }
        // Show bay details in modal
        async function showBayDetails(bayId) {
            try {
                const response = await fetch(`<?php echo e(url('admin/depot-map/bay')); ?>/${bayId}`);
                if (response.ok) {
                    const data = await response.json();
                    displayBayModal(data);
                }
            } catch (error) {
                console.error('Failed to fetch bay details:', error);
            }
        }
        // Show location details in modal
        async function showLocationDetails(locationId) {
            try {
                const response = await fetch(`<?php echo e(url('admin/depot-map/location')); ?>/${locationId}`);
                if (response.ok) {
                    const data = await response.json();
                    displayLocationModal(data);
                }
            } catch (error) {
                console.error('Failed to fetch location details:', error);
            }
        }
        // Display bay modal with data
        function displayBayModal(data) {
            document.getElementById('modal-title').textContent = `${data.bay_name} (Bay)`;
            let content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><strong>Status:</strong> <span class="px-2 py-1 rounded text-xs bg-${getStatusColor(data.status)}-100 text-${getStatusColor(data.status)}-800">${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</span></div>
                        <div><strong>Bay Code:</strong> ${data.bay_code || 'N/A'}</div>
                        <div><strong>Active:</strong> ${data.is_active ? 'Yes' : 'No'}</div>
                        <div><strong>Occupied:</strong> ${data.is_occupied ? 'Yes' : 'No'}</div>
                    </div>
            `;
            if (data.current_booking) {
                const booking = data.current_booking;
                content += `
                    <hr class="border-gray-200">
                    <div>
                        <h4 class="font-semibold mb-3">Current Booking Details</h4>
                        <div class="bg-blue-50 p-4 rounded-lg space-y-3">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div><strong>Reference:</strong> ${booking.booking_reference}</div>
                                <div><strong>Customer:</strong> ${booking.customer_name}</div>
                                <div><strong>Vehicle:</strong> ${booking.vehicle_registration || 'N/A'}</div>
                                <div><strong>Driver:</strong> ${booking.driver_name || 'N/A'}</div>
                                <div><strong>Container:</strong> ${booking.container_number || 'N/A'}</div>
                                <div><strong>Waste Type:</strong> ${booking.waste_type || 'N/A'}</div>
                                <div><strong>Scheduled:</strong> ${booking.scheduled_at || 'N/A'}</div>
                                <div><strong>Arrived:</strong> ${booking.arrived_at || 'Not arrived'}</div>
                            </div>
                            ${booking.customer_phone ? `<div class="text-sm"><strong>Phone:</strong> <a href="tel:${booking.customer_phone}" class="text-blue-600">${booking.customer_phone}</a></div>` : ''}
                            <div class="flex space-x-2 mt-4">
                                <a href="${booking.workflow_url}" target="_blank" class="px-3 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                    📋 View Booking Details
                                </a>
                                ${booking.tipping_workflow_url ? `
                                    <a href="${booking.tipping_workflow_url}" target="_blank" class="px-3 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                                        🏗️ Tipping Workflow
                                    </a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
                // Bay change option
                if (data.can_change_bay && data.alternative_bays && data.alternative_bays.length > 0) {
                    content += `
                        <hr class="border-gray-200">
                        <div>
                            <h4 class="font-semibold mb-3">Move to Different Bay</h4>
                            <div class="space-y-2">
                                <select id="alternative-bay-select" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select alternative bay...</option>
                    `;
                    data.alternative_bays.forEach(bay => {
                        content += `<option value="${bay.id}">${bay.name} ${bay.code ? '(' + bay.code + ')' : ''}</option>`;
                    });
                    content += `
                                </select>
                                <button onclick="changeBay(${data.bay_id}, ${booking.id})" class="w-full px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                                    🔄 Move Vehicle to Selected Bay
                                </button>
                            </div>
                        </div>
                    `;
                }
            } else {
                content += `
                    <div class="text-center py-4 text-gray-500">
                        <div class="text-4xl mb-2">🅿️</div>
                        <div>Bay is available</div>
                        <div class="text-sm mt-1">No vehicle currently assigned</div>
                    </div>
                `;
            }
            content += '</div>';
            document.getElementById('modal-content').innerHTML = content;
            document.getElementById('location-modal').classList.remove('hidden');
            document.getElementById('location-modal').classList.add('flex');
        }
        function getStatusColor(status) {
            const colors = {
                'available': 'green',
                'active': 'red',
                'waiting_collection': 'orange',
                'occupied': 'blue',
                'disabled': 'gray',
                'offline': 'gray'
            };
            return colors[status] || 'gray';
        }
        // Change bay function
        async function changeBay(currentBayId, bookingId) {
            const newBayId = document.getElementById('alternative-bay-select').value;
            if (!newBayId) {
                alert('Please select a bay to move to');
                return;
            }
            if (confirm('Are you sure you want to move this vehicle to the selected bay?')) {
                try {
                    const response = await fetch('<?php echo e(route("app.depot-map.change-bay")); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            booking_id: bookingId,
                            current_bay_id: currentBayId,
                            new_bay_id: newBayId
                        })
                    });
                    const result = await response.json();
                    if (result.success) {
                        alert('Vehicle moved successfully!');
                        closeLocationModal();
                        // Refresh the map to show changes
                        refreshMapData();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error changing bay:', error);
                    alert('Error moving vehicle. Please try again.');
                }
            }
        }
        // Display location modal with data
        function displayLocationModal(data) {
            document.getElementById('modal-title').textContent = data.location_name;
            let content = `
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><strong>Status:</strong> ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}</div>
                        <div><strong>Type:</strong> ${data.location_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</div>
                        <div><strong>Capacity:</strong> ${data.current_occupancy}/${data.capacity}</div>
                        <div><strong>Available:</strong> ${data.available_capacity}</div>
                    </div>
            `;
            if (data.current_bookings && data.current_bookings.length > 0) {
                content += `
                    <div>
                        <strong>Current Bookings:</strong>
                        <div class="mt-2 space-y-2">
                `;
                data.current_bookings.forEach(booking => {
                    content += `
                        <div class="bg-gray-50 p-3 rounded text-sm">
                            <div class="font-medium">${booking.booking_reference}</div>
                            <div class="text-gray-600">${booking.customer_name}</div>
                            ${booking.vehicle_registration ? `<div class="text-gray-500">${booking.vehicle_registration}</div>` : ''}
                            <div class="text-xs text-gray-500">
                                Status: ${booking.status} 
                                ${booking.arrived_at ? `• Arrived: ${booking.arrived_at}` : ''}
                                ${booking.unit_departed ? `• Unit Left: ${booking.unit_departed}` : ''}
                            </div>
                        </div>
                    `;
                });
                content += '</div></div>';
            }
            content += '</div>';
            document.getElementById('modal-content').innerHTML = content;
            document.getElementById('location-modal').classList.remove('hidden');
            document.getElementById('location-modal').classList.add('flex');
        }
        // Close modal
        function closeLocationModal() {
            document.getElementById('location-modal').classList.add('hidden');
            document.getElementById('location-modal').classList.remove('flex');
        }
        // Map zoom controls
        function zoomIn() {
            currentZoom = Math.min(currentZoom * 1.2, 3);
            applyZoom();
        }
        function zoomOut() {
            currentZoom = Math.max(currentZoom / 1.2, 0.5);
            applyZoom();
        }
        function resetZoom() {
            currentZoom = 1;
            applyZoom();
        }
        function applyZoom() {
            const mapImage = document.getElementById('depot-map-image');
            mapImage.style.transform = `scale(${currentZoom})`;
        }
        // Full screen functionality
        function toggleFullScreen() {
            const body = document.body;
            const mapContainer = document.querySelector('.py-6.max-w-7xl.mx-auto');
            const fullscreenBtn = document.getElementById('fullscreen-btn');
            if (body.classList.contains('fullscreen-mode')) {
                // Exit full screen
                body.classList.remove('fullscreen-mode');
                mapContainer.classList.remove('fullscreen-container');
                fullscreenBtn.innerHTML = '🖥️';
                fullscreenBtn.title = 'Full Screen Dashboard';
            } else {
                // Enter full screen
                body.classList.add('fullscreen-mode');
                mapContainer.classList.add('fullscreen-container');
                fullscreenBtn.innerHTML = '🗙';
                fullscreenBtn.title = 'Exit Full Screen';
            }
        }
        // ESC key to exit full screen
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.body.classList.contains('fullscreen-mode')) {
                toggleFullScreen();
            }
        });
        // Depot switching
        function switchDepot(depotId) {
            if (depotId && depotId !== '<?php echo e($depot->id); ?>') {
                window.location.href = '<?php echo e(route($routePrefix . "depot-map.index")); ?>?depot_id=' + depotId;
            }
        }
        // Quick actions
        function showAllBookings() {
            window.location.href = '<?php echo e(route("app.bookings.index")); ?>';
        }
        function openTrailerCollection() {
            window.open('<?php echo e(route("app.empty-unit-collection")); ?>', '_blank', 'width=1200,height=800');
        }
        function openTippingWorkflow() {
            // Find first active booking to redirect to workflow
            const activeBookings = Object.values(locationStatuses).filter(status => status.status === 'active');
            if (activeBookings.length > 0) {
                alert('Select a specific booking from the map to access tipping workflow');
            } else {
                alert('No active tipping operations found');
            }
        }
    </script>
    <style>
        .location-overlay:hover .location-box {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        #depot-map-image {
            transition: transform 0.3s ease;
        }
        .location-box {
            transition: all 0.2s ease;
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/depot-map/index.blade.php ENDPATH**/ ?>