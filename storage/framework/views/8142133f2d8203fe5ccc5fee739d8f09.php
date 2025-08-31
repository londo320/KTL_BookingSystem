<?php if (isset($component)) { $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448 = $attributes; } ?>
<?php $component = App\View\Components\SiteAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SiteAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🔍 Vehicle Search
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Search Form -->
        <div class="mb-8 bg-white shadow rounded-lg p-6">
            <form method="GET" class="flex space-x-4">
                <div class="flex-1">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                           placeholder="Search by booking ref, vehicle reg, container, driver name..."
                           class="w-full border-gray-300 rounded-lg text-lg">
                </div>
                <button type="submit" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 text-lg">
                    Search
                </button>
            </form>
        </div>

        <?php if(request('search')): ?>
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium">
                        Search Results for "<?php echo e(request('search')); ?>" 
                        <span class="text-gray-500">(<?php echo e($results->count()); ?> found)</span>
                    </h3>
                </div>
                
                <?php if($results->isEmpty()): ?>
                    <div class="p-8 text-center text-gray-500">
                        No bookings found matching your search criteria.
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-200">
                        <?php $__currentLoopData = $results; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="p-6 hover:bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span class="font-mono text-sm bg-blue-100 px-3 py-1 rounded font-semibold">
                                                <?php echo e($booking->booking_reference); ?>

                                            </span>
                                            <span class="font-medium text-lg"><?php echo e($booking->customer->name ?? 'N/A'); ?></span>
                                            
                                            <?php if($booking->arrived_at && !$booking->departed_at): ?>
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">
                                                    ON SITE
                                                </span>
                                            <?php elseif($booking->departed_at): ?>
                                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-semibold">
                                                    DEPARTED
                                                </span>
                                            <?php else: ?>
                                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">
                                                    PENDING
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                            <div>
                                                <strong>Location & Time:</strong><br>
                                                📍 <?php echo e($booking->slot->depot->name); ?><br>
                                                ⏰ <?php echo e($booking->slot->start_at->format('d-M H:i')); ?>

                                                <?php if($booking->gate_number || $booking->bay_number): ?>
                                                    <br>
                                                    <?php if($booking->gate_number): ?>🚪 Gate <?php echo e($booking->gate_number); ?> <?php endif; ?>
                                                    <?php if($booking->bay_number): ?>🏗️ Bay <?php echo e($booking->bay_number); ?><?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div>
                                                <strong>Vehicle Details:</strong><br>
                                                <?php if($booking->vehicle_registration): ?>
                                                    🚛 <?php echo e($booking->vehicle_registration); ?><br>
                                                <?php endif; ?>
                                                <?php if($booking->container_number): ?>
                                                    📦 <?php echo e($booking->container_number); ?><br>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div>
                                                <strong>Status:</strong><br>
                                                <?php if($booking->arrived_at): ?>
                                                    ✅ Arrived: <?php echo e($booking->arrived_at->format('d-M H:i')); ?><br>
                                                <?php endif; ?>
                                                <?php if($booking->departed_at): ?>
                                                    🕒 Departed: <?php echo e($booking->departed_at->format('d-M H:i')); ?><br>
                                                    ⏱️ Duration: <?php echo e($booking->arrived_at?->diffInMinutes($booking->departed_at) ?? 0); ?> mins
                                                <?php elseif($booking->arrived_at): ?>
                                                    🏢 On site for: <?php echo e($booking->arrived_at->diffForHumans()); ?>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <?php if($booking->special_instructions): ?>
                                            <div class="mt-3 p-2 bg-yellow-50 rounded text-sm">
                                                <strong>⚠️ Special Instructions:</strong> <?php echo e($booking->special_instructions); ?>

                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="ml-6 flex flex-col space-y-2">
                                        <?php if(!$booking->arrived_at): ?>
                                            <button onclick="openArrivalModal(<?php echo e($booking->id); ?>, '<?php echo e($booking->booking_reference); ?>', '<?php echo e(addslashes($booking->customer->name ?? 'N/A')); ?>', '<?php echo e($booking->slot->depot->name); ?>', '<?php echo e($booking->slot->start_at->format('d-M-Y H:i')); ?>', '<?php echo e($booking->vehicle_registration ?? ''); ?>', '<?php echo e($booking->container_number ?? ''); ?>', '<?php echo e($booking->carrier_company ?? ''); ?>', '<?php echo e($booking->gate_number ?? ''); ?>', '<?php echo e($booking->expected_cases ?? 0); ?>', '<?php echo e($booking->expected_pallets ?? 0); ?>', '<?php echo e(addslashes($booking->special_instructions ?? '')); ?>')" 
                                                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 cursor-pointer">
                                                🚛 Process Arrival
                                            </button>
                                        <?php elseif(!$booking->departed_at): ?>
                                            <form method="POST" action="<?php echo e(route('site.bookings.departure', $booking)); ?>">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" 
                                                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                                    Mark Departed
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Arrival Modal -->
    <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing</h3>
                    <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Booking Summary -->
                <div id="bookingSummary" class="mt-4 p-4 bg-green-50 rounded-lg">
                    <!-- Will be populated by JavaScript -->
                </div>

                <!-- Arrival Form -->
                <form id="arrivalForm" method="POST" class="mt-6">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Required Vehicle Registration -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                                   placeholder="e.g., AB12 CDE"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                        </div>

                        <!-- Container Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
                            <input type="text" id="containerNumber" name="container_number"
                                   placeholder="e.g., CONT123456"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Driver Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                            <input type="text" id="driverName" name="driver_name"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Driver Phone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver Phone</label>
                            <input type="tel" id="driverPhone" name="driver_phone"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Gate Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gate Number</label>
                            <input type="text" id="gateNumber" name="gate_number"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Bay Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bay Number</label>
                            <input type="text" id="bayNumber" name="bay_number"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>

                        <!-- Actual Cases -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cases</label>
                            <input type="number" id="actualCases" name="actual_cases" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p id="expectedCases" class="text-xs text-gray-500 mt-1">Expected: 0</p>
                        </div>

                        <!-- Actual Pallets -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Pallets</label>
                            <input type="number" id="actualPallets" name="actual_pallets" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p id="expectedPallets" class="text-xs text-gray-500 mt-1">Expected: 0</p>
                        </div>

                    </div>

                    <!-- Special Instructions -->
                    <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p id="specialInstructionsText" class="text-yellow-700"></p>
                    </div>

                    <!-- Arrival Time Display -->
                    <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-800 mb-2">📅 Arrival Time:</h4>
                        <p class="text-green-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeArrivalModal()" 
                                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            🚛 Mark Vehicle Arrived
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentBookingId = null;

        function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, driverName, driverPhone, gateNum, bayNum, actualCases, actualPallets, expectedCases, expectedPallets, specialInstructions) {
            currentBookingId = bookingId;
            
            // Update booking summary
            document.getElementById('bookingSummary').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Booking:</strong> ${bookingRef}<br>
                        <strong>Customer:</strong> ${customer}
                    </div>
                    <div>
                        <strong>Depot:</strong> ${depot}<br>
                        <strong>Scheduled:</strong> ${scheduledTime}
                    </div>
                    <div>
                        <strong>Expected:</strong> ${expectedCases} cases, ${expectedPallets} pallets
                    </div>
                </div>
            `;

            // Update form action
            document.getElementById('arrivalForm').action = `/site-admin/bookings/${bookingId}/arrival`;

            // Populate form fields
            document.getElementById('vehicleRegistration').value = vehicleReg;
            document.getElementById('containerNumber').value = containerNum;
            document.getElementById('driverName').value = driverName;
            document.getElementById('driverPhone').value = driverPhone;
            document.getElementById('gateNumber').value = gateNum;
            document.getElementById('bayNumber').value = bayNum;
            document.getElementById('actualCases').value = actualCases;
            document.getElementById('actualPallets').value = actualPallets;

            // Update expected quantities display
            document.getElementById('expectedCases').textContent = `Expected: ${expectedCases}`;
            document.getElementById('expectedPallets').textContent = `Expected: ${expectedPallets}`;

            // Show special instructions if any
            if (specialInstructions && specialInstructions.trim() !== '') {
                document.getElementById('specialInstructionsText').textContent = specialInstructions;
                document.getElementById('specialInstructions').classList.remove('hidden');
            } else {
                document.getElementById('specialInstructions').classList.add('hidden');
            }

            // Update arrival time display
            updateArrivalTime();

            // Show modal
            document.getElementById('arrivalModal').classList.remove('hidden');
            
            // Focus on vehicle registration field
            setTimeout(() => {
                document.getElementById('vehicleRegistration').focus();
            }, 100);
        }

        function closeArrivalModal() {
            document.getElementById('arrivalModal').classList.add('hidden');
            currentBookingId = null;
        }

        function updateArrivalTime() {
            const now = new Date();
            const timeString = now.toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;
        }

        // Close modal when clicking outside
        document.getElementById('arrivalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeArrivalModal();
            }
        });

        // Update time display every second
        setInterval(() => {
            if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
                updateArrivalTime();
            }
        }, 1000);
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $attributes = $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $component = $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/site-admin/search.blade.php ENDPATH**/ ?>