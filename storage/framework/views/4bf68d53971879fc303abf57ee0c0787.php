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
      <h2 class="font-semibold text-xl">🚛 Empty Unit Collection</h2>
      <a href="<?php echo e(route('admin.bookings.index')); ?>"
         class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to Bookings
      </a>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-6xl mx-auto">
    
    <!-- Collection Form -->
    <div class="bg-white p-6 rounded shadow mb-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Record Unit Collection</h3>
      <p class="text-sm text-gray-600 mb-6">
        Use this form when a vehicle arrives to collect a trailer from either collection zones or tipping bays (no booking reference required).
      </p>
      
      <form action="<?php echo e(route('admin.empty-unit-collection.process')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          
          <!-- Vehicle Details -->
          <div class="col-span-2 border-t pt-4">
            <h4 class="font-medium text-gray-800 mb-3">🚛 Vehicle Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Vehicle Registration <span class="text-red-500">*</span>
                </label>
                <input type="text" name="vehicle_registration" required
                       value="<?php echo e(old('vehicle_registration')); ?>"
                       placeholder="e.g., AB12 CDE"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Carrier Company</label>
                <div class="relative">
                  <input type="text" id="carrier-search" name="carrier_name"
                         value="<?php echo e(old('carrier_company')); ?>"
                         placeholder="Search or type carrier name..."
                         autocomplete="off"
                         class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <input type="hidden" id="carrier-id" name="carrier_id" value="">
                  
                  <!-- Dropdown for search results -->
                  <div id="carrier-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-auto">
                    <div id="carrier-results"></div>
                  </div>
                </div>
                <?php $__errorArgs = ['carrier_company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </div>
          </div>
          
          <!-- Collection Details -->
          <div class="col-span-2 border-t pt-4">
            <h4 class="font-medium text-gray-800 mb-3">📦 Collection Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Trailer Number/ID <span class="text-red-500">*</span>
                </label>
                <input type="text" name="collected_trailer_number" required
                       value="<?php echo e(old('collected_trailer_number')); ?>"
                       placeholder="e.g., TR-001, CONT789123"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <?php $__errorArgs = ['collected_trailer_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Collection Location <span class="text-red-500">*</span>
                </label>
                <select name="collection_location" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <option value="">– Select Location –</option>
                  
                  <?php if($collectionZones->count() > 0): ?>
                    <optgroup label="📦 Collection Zones">
                      <?php $__currentLoopData = $collectionZones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $zone): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="ZONE_<?php echo e($zone->id); ?>" <?php echo e(old('collection_location') == 'ZONE_'.$zone->id ? 'selected' : ''); ?>>
                          <?php echo e($zone->name); ?><?php if($zone->code): ?> (<?php echo e($zone->code); ?>)<?php endif; ?>
                          - <?php echo e($zone->getAvailableCapacity()); ?>/<?php echo e($zone->capacity); ?> spaces
                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                  <?php endif; ?>
                  
                  <?php if($tippingBays->count() > 0): ?>
                    <optgroup label="🏗️ Tipping Bays">
                      <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="BAY_<?php echo e($bay->id); ?>" <?php echo e(old('collection_location') == 'BAY_'.$bay->id ? 'selected' : ''); ?>>
                          <?php echo e($bay->name); ?><?php if($bay->code): ?> (<?php echo e($bay->code); ?>)<?php endif; ?>
                          <?php if($bay->is_occupied): ?> - Currently Occupied <?php else: ?> - Available <?php endif; ?>
                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                  <?php endif; ?>
                </select>
                <?php $__errorArgs = ['collection_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Link to Booking (Optional)</label>
                <select name="collected_from_booking_id"
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <option value="">– No specific booking –</option>
                  <?php $__currentLoopData = $availableTrailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($booking->id); ?>" <?php echo e(old('collected_from_booking_id') == $booking->id ? 'selected' : ''); ?>>
                      <?php echo e($booking->booking_reference); ?> - <?php echo e($booking->container_number ?? 'No container number'); ?> 
                      (<?php echo e($booking->dropped_trailer_location ?? 'Unknown location'); ?>)
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">If collecting a specific trailer from a booking, select it here</p>
                <?php $__errorArgs = ['collected_from_booking_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Form Actions -->
        <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
          <a href="<?php echo e(route('admin.bookings.index')); ?>"
             class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
            Cancel
          </a>
          <button type="submit"
                  class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            🚛 Record Collection
          </button>
        </div>
      </form>
    </div>
    
    <!-- Available Trailers -->
    <?php if($availableTrailers->count() > 0): ?>
    <div class="bg-white p-6 rounded shadow">
      <h3 class="text-lg font-medium text-gray-900 mb-4">📦 Trailers Available for Collection (Empty & Full)</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container/Trailer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Collection</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $__currentLoopData = $availableTrailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <?php echo e($booking->booking_reference); ?>

              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php echo e($booking->container_number ?? 'Not specified'); ?>

                <?php if($booking->trailerType): ?>
                  <br><span class="text-xs text-gray-500"><?php echo e($booking->trailerType->name); ?></span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php echo e($booking->current_location ?? 'Unknown'); ?>

              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full
                  <?php if($booking->movement_status === 'empty'): ?> bg-green-100 text-green-800
                  <?php elseif($booking->movement_status === 'awaiting_collection'): ?> bg-orange-100 text-orange-800
                  <?php elseif($booking->movement_status === 'trailer_dropped'): ?> bg-blue-100 text-blue-800
                  <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                  <?php if($booking->movement_status === 'empty'): ?> Empty - Ready
                  <?php elseif($booking->movement_status === 'awaiting_collection'): ?> Awaiting Collection
                  <?php elseif($booking->movement_status === 'trailer_dropped'): ?> Full - Dropped
                  <?php else: ?> <?php echo e(ucwords(str_replace('_', ' ', $booking->movement_status ?? 'Unknown'))); ?>

                  <?php endif; ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php if($booking->trailer_collection_scheduled): ?>
                  <?php echo e($booking->trailer_collection_scheduled->format('d-M-Y H:i')); ?>

                <?php else: ?>
                  <span class="text-gray-400">Not scheduled</span>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <?php echo e($booking->customer->name ?? 'Unknown'); ?>

              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
    <?php else: ?>
    <div class="bg-white p-6 rounded shadow">
      <div class="text-center py-8">
        <div class="text-gray-400 text-6xl mb-4">📭</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers Available for Collection</h3>
        <p class="text-gray-600">There are currently no trailers left on site awaiting collection.</p>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <script>
    // Auto-populate form when booking is selected
    document.addEventListener('DOMContentLoaded', function() {
        const bookingSelect = document.querySelector('select[name="collected_from_booking_id"]');
        const trailerNumberInput = document.querySelector('input[name="collected_trailer_number"]');
        const carrierSearchInput = document.querySelector('#carrier-search');
        
        if (bookingSelect && trailerNumberInput) {
            // Booking data for auto-population
            const bookingData = {
                <?php $__currentLoopData = $availableTrailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo e($booking->id); ?>: {
                    trailer_number: '<?php echo e($booking->container_number ?? "TRAILER-" . $booking->id); ?>',
                    carrier_name: '<?php echo e(addslashes($booking->carrier_company ?? '')); ?>',
                    current_location: '<?php echo e(addslashes($booking->current_location ?? '')); ?>'
                },
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            };
            
            bookingSelect.addEventListener('change', function() {
                const selectedBookingId = this.value;
                
                if (selectedBookingId && bookingData[selectedBookingId]) {
                    const data = bookingData[selectedBookingId];
                    
                    // Auto-populate trailer number
                    trailerNumberInput.value = data.trailer_number;
                    
                    // Auto-populate carrier name
                    if (carrierSearchInput && data.carrier_name) {
                        carrierSearchInput.value = data.carrier_name;
                    }
                } else {
                    // Clear fields
                    trailerNumberInput.value = '';
                    if (carrierSearchInput) {
                        carrierSearchInput.value = '';
                    }
                }
            });
        }
        
        // Carrier search functionality
        const searchInput = document.getElementById('carrier-search');
        const dropdown = document.getElementById('carrier-dropdown');
        const resultsDiv = document.getElementById('carrier-results');
        const carrierIdInput = document.getElementById('carrier-id');
        let selectedCarrierId = '';
        let isLoading = false;

        function searchCarriers(query) {
            if (query.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }
            
            if (isLoading) return;
            isLoading = true;
            
            fetch(`<?php echo e(route('api.carriers.search')); ?>?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    
                    if (data.carriers && data.carriers.length > 0) {
                        data.carriers.forEach(carrier => {
                            const item = document.createElement('div');
                            item.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                            item.innerHTML = `
                                <div class="font-medium">${carrier.name}</div>
                                <div class="text-xs text-gray-500">Used in ${carrier.bookings_count || 0} bookings</div>
                            `;
                            item.onclick = () => selectCarrier(carrier);
                            resultsDiv.appendChild(item);
                        });
                        
                        // Add option to create new carrier
                        const createItem = document.createElement('div');
                        createItem.className = 'px-4 py-2 hover:bg-blue-100 cursor-pointer border-t border-blue-200 bg-blue-50';
                        createItem.innerHTML = `<div class="text-blue-600 font-medium">+ Create "${query}" as new carrier</div>`;
                        createItem.onclick = () => createNewCarrier(query);
                        resultsDiv.appendChild(createItem);
                        
                        dropdown.classList.remove('hidden');
                    } else {
                        // No results, show create option
                        const createItem = document.createElement('div');
                        createItem.className = 'px-4 py-2 hover:bg-blue-100 cursor-pointer bg-blue-50';
                        createItem.innerHTML = `<div class="text-blue-600 font-medium">+ Create "${query}" as new carrier</div>`;
                        createItem.onclick = () => createNewCarrier(query);
                        resultsDiv.appendChild(createItem);
                        dropdown.classList.remove('hidden');
                    }
                    
                    isLoading = false;
                })
                .catch(error => {
                    console.error('Search failed:', error);
                    dropdown.classList.add('hidden');
                    isLoading = false;
                });
        }

        function selectCarrier(carrier) {
            searchInput.value = carrier.name;
            carrierIdInput.value = carrier.id;
            selectedCarrierId = carrier.id;
            dropdown.classList.add('hidden');
        }

        function createNewCarrier(name) {
            searchInput.value = name;
            carrierIdInput.value = '';
            selectedCarrierId = '';
            dropdown.classList.add('hidden');
        }

        // Search input events
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query !== searchInput.dataset.lastQuery) {
                searchInput.dataset.lastQuery = query;
                searchCarriers(query);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/empty-unit-collection.blade.php ENDPATH**/ ?>