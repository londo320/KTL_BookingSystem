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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏢 Warehouse Dashboard
        </h2>
     <?php $__env->endSlot(); ?>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Depot Assignment Info -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <?php if(auth()->user()->hasRole('admin')): ?>
                        <p class="text-sm text-blue-700">
                            <strong>Admin Access - Depots:</strong>
                            <?php if($userDepots->isEmpty()): ?>
                                <span class="inline-block bg-red-100 px-2 py-1 rounded text-xs ml-1 text-red-600">
                                    No depots assigned
                                </span>
                            <?php else: ?>
                                <?php $__currentLoopData = $userDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-block bg-blue-100 px-2 py-1 rounded text-xs ml-1">
                                        <?php echo e($depot->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </p>
                        <p class="text-sm text-blue-700 mt-2">
                            <strong>Admin Access - Customers:</strong>
                            <?php
                                $assignedCustomers = auth()->user()->customers()->get();
                            ?>
                            <?php if($assignedCustomers->isEmpty()): ?>
                                <span class="inline-block bg-green-100 px-2 py-1 rounded text-xs ml-1 text-green-800">
                                    🔑 All Customers
                                </span>
                            <?php else: ?>
                                <?php $__currentLoopData = $assignedCustomers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-block bg-blue-100 px-2 py-1 rounded text-xs ml-1">
                                        <?php echo e($customer->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-blue-700">
                            <strong>Your Assigned Depots:</strong>
                            <?php if($userDepots->isEmpty()): ?>
                                <span class="inline-block bg-red-100 px-2 py-1 rounded text-xs ml-1 text-red-600">
                                    No depots assigned
                                </span>
                            <?php else: ?>
                                <?php $__currentLoopData = $userDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="inline-block bg-blue-100 px-2 py-1 rounded text-xs ml-1">
                                        <?php echo e($depot->name); ?>

                                    </span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Depot Filter -->
        <?php if($userDepots->count() > 1): ?>
        <div class="mb-6">
            <form method="GET" class="flex items-center space-x-4">
                <label for="depot_filter" class="text-sm font-medium text-gray-700">Filter by Depot:</label>
                <select name="depot_id" id="depot_filter" class="border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">All Assigned Depots</option>
                    <?php $__currentLoopData = $userDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($depot->id); ?>" <?php echo e($depotFilter == $depot->id ? 'selected' : ''); ?>>
                            <?php echo e($depot->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($depotFilter): ?>
                    <a href="<?php echo e(url()->current()); ?>" class="text-sm bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600">
                        Clear Filter
                    </a>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">📋</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Today's Bookings
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    <?php echo e($stats['total_bookings'] ?? 0); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">✅</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Arrived
                                </dt>
                                <dd class="text-lg font-medium text-green-600">
                                    <?php echo e($stats['arrived'] ?? 0); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🏢</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    On Site
                                </dt>
                                <dd class="text-lg font-medium text-blue-600">
                                    <?php echo e($stats['on_site'] ?? 0); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second Row of Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🕒</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Departed
                                </dt>
                                <dd class="text-lg font-medium text-gray-600">
                                    <?php echo e($stats['departed'] ?? 0); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">⏳</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Outstanding
                                </dt>
                                <dd class="text-lg font-medium text-orange-600">
                                    <?php echo e($stats['outstanding'] ?? 0); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🚨</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Late Runners
                                </dt>
                                <dd class="text-lg font-medium text-red-600">
                                    <?php echo e($stats['late_runners'] ?? 0); ?>

                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upcoming Bookings -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🚛 Upcoming Arrivals (Next 3 Hours)
                    </h3>
                    <?php if($upcomingBookings->isEmpty()): ?>
                        <p class="text-gray-500 text-sm">No upcoming bookings in the next 3 hours.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $upcomingBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">
                                                <?php echo e($booking->booking_reference); ?>

                                            </span>
                                            <span class="font-medium"><?php echo e($booking->customer->name ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 <?php echo e($booking->slot->depot->name); ?> • 
                                            ⏰ <?php echo e($booking->slot->start_at->format('H:i')); ?>

                                            <?php if($booking->vehicle_registration): ?>
                                                • 🚛 <?php echo e($booking->vehicle_registration); ?>

                                            <?php endif; ?>
                                            <?php if($booking->container_number): ?>
                                                • 📦 <?php echo e($booking->container_number); ?>

                                            <?php endif; ?>
                                        </div>
                                        <?php if($booking->special_instructions): ?>
                                            <div class="text-xs text-orange-600 mt-1">
                                                ⚠️ <?php echo e(Str::limit($booking->special_instructions, 50)); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">
                                            <?php echo e($booking->slot->start_at->diffForHumans()); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Late Runners -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🚨 Late Runners (Overdue)
                    </h3>
                    <?php if($lateRunnersData->isEmpty()): ?>
                        <p class="text-gray-500 text-sm">No overdue bookings.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $lateRunnersData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-3 bg-red-50 border-l-4 border-red-400 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-red-100 px-2 py-1 rounded">
                                                <?php echo e($booking->booking_reference); ?>

                                            </span>
                                            <span class="font-medium"><?php echo e($booking->customer->name ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 <?php echo e($booking->slot->depot->name); ?>

                                        </div>
                                        <?php if($booking->estimated_arrival): ?>
                                            <div class="text-sm text-blue-600 mt-1 font-medium">
                                                💬 Updated ETA: <?php echo e(\Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                                            </div>
                                        <?php endif; ?>
                                        <div class="text-sm text-red-600 mt-1 font-medium">
                                            ⏰ Original: <?php echo e($booking->slot->start_at->format('d-M H:i')); ?>

                                            <?php if($booking->vehicle_registration): ?>
                                                • 🚛 <?php echo e($booking->vehicle_registration); ?>

                                            <?php endif; ?>
                                            <?php if($booking->container_number): ?>
                                                • 📦 <?php echo e($booking->container_number); ?>

                                            <?php endif; ?>
                                        </div>
                                        <?php if($booking->special_instructions): ?>
                                            <div class="text-xs text-orange-600 mt-1">
                                                ⚠️ <?php echo e(Str::limit($booking->special_instructions, 50)); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-red-600">
                                            <?php echo e($booking->slot->start_at->diffForHumans()); ?>

                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Current Arrivals -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🏢 Currently On Site
                    </h3>
                    <?php if($currentArrivals->isEmpty()): ?>
                        <p class="text-gray-500 text-sm">No vehicles currently on site.</p>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php $__currentLoopData = $currentArrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $isFactory = isset($booking->type) && $booking->type === 'factory';
                                    $bgColor = $isFactory ? 'bg-purple-50' : 'bg-green-50';
                                    $borderColor = $isFactory ? 'border-l-4 border-purple-400' : '';
                                ?>
                                <div class="flex items-center justify-between p-3 <?php echo e($bgColor); ?> <?php echo e($borderColor); ?> rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <?php if($isFactory): ?>
                                                <span class="font-mono text-xs bg-purple-100 px-2 py-1 rounded">
                                                    🏭 <?php echo e($booking->booking_reference); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">
                                                    <?php echo e($booking->booking_reference); ?>

                                                </span>
                                            <?php endif; ?>
                                            <span class="font-medium"><?php echo e($booking->customer->name ?? 'N/A'); ?></span>
                                            <?php if($isFactory): ?>
                                                <span class="text-xs bg-purple-500 text-white px-2 py-1 rounded">Factory</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 <?php echo e($booking->slot->depot->name); ?> • 
                                            ✅ Arrived: <?php echo e($booking->arrived_at->format('H:i')); ?>

                                            <?php if($booking->vehicle_registration): ?>
                                                • 🚛 <?php echo e($booking->vehicle_registration); ?>

                                            <?php endif; ?>
                                            <?php if($isFactory && $booking->trailer_registration): ?>
                                                • 🚚 <?php echo e($booking->trailer_registration); ?>

                                            <?php endif; ?>
                                            <?php if($booking->container_number): ?>
                                                • 📦 <?php echo e($booking->container_number); ?>

                                            <?php endif; ?>
                                        </div>
                                        <?php if(!$isFactory && ($booking->gate_number || $booking->bay_number)): ?>
                                            <div class="text-xs text-blue-600 mt-1">
                                                <?php if($booking->gate_number): ?>🚪 Gate <?php echo e($booking->gate_number); ?> <?php endif; ?>
                                                <?php if($booking->bay_number): ?>🏗️ Bay <?php echo e($booking->bay_number); ?><?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500">
                                            On site: <?php echo e($booking->arrived_at->diffForHumans()); ?>

                                        </div>
                                        <?php if($isFactory): ?>
                                            <span class="text-xs bg-purple-400 text-white px-2 py-1 rounded">
                                                Factory Vehicle
                                            </span>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" 
                                               class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                                View Details
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    ⚡ Quick Actions
                </h3>
                <div class="flex flex-wrap gap-4">
                    <a href="<?php echo e(route('app.bookings.index')); ?>" 
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        📋 View All Bookings
                    </a>
                    <a href="<?php echo e(route('app.slots.index')); ?>" 
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        ⏰ Manage Slots
                    </a>
                    <a href="<?php echo e(route('app.arrivals.index')); ?>" 
                       class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                        🚛 Live Arrivals
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh the page every 5 minutes (300,000 milliseconds)
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        // Show a refresh indicator in page title
        let refreshCounter = 300; // 5 minutes in seconds
        const originalTitle = document.title;
        function updateRefreshIndicator() {
            const minutes = Math.floor(refreshCounter / 60);
            const seconds = refreshCounter % 60;
            document.title = `${originalTitle} - Auto-refresh in ${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (refreshCounter <= 0) {
                document.title = `${originalTitle} - Refreshing...`;
                return;
            }
            refreshCounter--;
        }
        // Update countdown every second
        setInterval(updateRefreshIndicator, 1000);
        updateRefreshIndicator(); // Initial call
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/app/dashboard.blade.php ENDPATH**/ ?>