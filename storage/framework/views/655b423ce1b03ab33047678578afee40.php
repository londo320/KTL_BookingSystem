<div class="space-y-6">
  
  <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-medium text-blue-900 mb-3">📋 Required Information</h3>
    
    <div class="grid grid-cols-2 gap-4">
      
      <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasFunction('customers.view') || auth()->user()->hasFunction('bookings.create') || request()->routeIs('app.*')): ?>
        <div class="col-span-2">
          <label class="block text-sm font-medium text-blue-800">Customer <span class="text-red-500">*</span></label>
          <select name="customer_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
            <option value="">– Choose customer –</option>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($customer->id); ?>"
                <?php if(old('customer_id', $booking->customer_id) == $customer->id): echo 'selected'; endif; ?>
              >
                <?php echo e($customer->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      <?php endif; ?>

      
      <div class="col-span-2">
        <label class="block text-sm font-medium text-blue-800">Slot <span class="text-red-500">*</span>
          <?php if($booking->exists): ?>
            <span class="text-xs text-red-600 ml-2">⚠️ Slot changes disabled - Use Rebook button instead</span>
          <?php endif; ?>
        </label>
        <select name="slot_id" required <?php if($booking->exists): ?> disabled <?php endif; ?> class="mt-1 block w-full border-blue-300 rounded <?php if($booking->exists): ?> bg-gray-100 text-gray-500 cursor-not-allowed <?php else: ?> bg-white <?php endif; ?>">
          <?php if($booking->exists && $booking->slot): ?>
            <option value="<?php echo e($booking->slot->id); ?>" selected>
              <?php echo e($booking->slot->depot->name); ?> - 
              <?php echo e($booking->slot->start_at->format('D d-M H:i')); ?> → <?php echo e($booking->slot->end_at->format('H:i')); ?>

            </option>
          <?php else: ?>
            <option value="">– Choose slot –</option>
            <?php
              $groupedSlots = $slots->sortBy('start_at')->groupBy(fn($slot) => $slot->depot->name);
            ?>
            <?php $__currentLoopData = $groupedSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $depotSlots): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <optgroup label="<?php echo e($depotName); ?>">
                <?php $__currentLoopData = $depotSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $isRestricted = $slot->allowed_customers->count() > 0;
                  ?>
                  <option value="<?php echo e($slot->id); ?>"
                    <?php if(old('slot_id', $booking->slot_id) == $slot->id): echo 'selected'; endif; ?>>
                    <?php echo e($isRestricted ? '🔒' : '🌐'); ?> <?php echo e($slot->start_at->format('D d-M H:i')); ?> → <?php echo e($slot->end_at->format('H:i')); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </optgroup>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
        </select>
        
        
        <?php if($booking->exists && $booking->slot): ?>
          <input type="hidden" name="slot_id" value="<?php echo e($booking->slot->id); ?>">
        <?php endif; ?>
        
        <?php $__errorArgs = ['slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-blue-800">Booking Type <span class="text-red-500">*</span></label>
        <select name="booking_type_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
          <option value="">– Choose type –</option>
          <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type->id); ?>"
              <?php if(old('booking_type_id', $booking->booking_type_id) == $type->id): echo 'selected'; endif; ?>
            >
              <?php echo e($type->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['booking_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-blue-800">Carrier Company <span class="text-red-500">*</span></label>
        <div class="relative">
          <input type="text" 
                 id="admin-carrier-search" 
                 name="carrier_name"
                 value="<?php echo e(old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company)); ?>"
                 placeholder="Search or type carrier name..."
                 required
                 autocomplete="off"
                 class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
          
          
          <input type="hidden" 
                 id="admin-carrier-id" 
                 name="carrier_id" 
                 value="<?php echo e(old('carrier_id', $booking->carrier_id)); ?>">
          
          
          <div id="admin-carrier-dropdown" 
               class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            
          </div>
          
          
          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <span id="admin-carrier-status" class="text-xs"></span>
          </div>
        </div>
        
        <div class="mt-2">
          <a href="<?php echo e(route('app.carriers.create')); ?>" target="_blank"
             class="text-xs text-blue-600 hover:text-blue-800 underline">
            🏢 Manage carriers
          </a>
        </div>
        
        <?php $__errorArgs = ['carrier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['carrier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
  </div>

  
  <div class="bg-green-50 p-4 rounded-lg border border-green-200">
    <h3 class="text-lg font-medium text-green-900 mb-3">📦 PO Numbers & Expected Quantities</h3>
    <p class="text-sm text-green-700 mb-3">At least one PO with expected quantities is required</p>
    <?php if (isset($component)) { $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $booking,'hideActuals' => !$booking->exists]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking),'hide_actuals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$booking->exists)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $attributes = $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $component = $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>
  </div>

  
  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
    <h3 class="text-lg font-medium text-gray-700 mb-3">🚛 Transportation Details <span class="text-sm font-normal text-gray-500">(Optional - can be added later)</span></h3>
    
    <div class="grid grid-cols-2 gap-4">
      
      <div>
        <label class="block text-sm font-medium text-gray-600">Vehicle Registration</label>
        <input type="text" name="vehicle_registration"
               value="<?php echo e(old('vehicle_registration', $booking->vehicle_registration)); ?>"
               placeholder="e.g., AB12 CDE"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-600">Vehicle/Trailer Number</label>
        <input type="text" name="container_number"
               value="<?php echo e(old('container_number', $booking->container_number)); ?>"
               placeholder="e.g., CONT123456"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['container_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>


      
      <div>
        <label class="block text-sm font-medium text-gray-600">Trailer Type</label>
        <select name="trailer_type_id" class="mt-1 block w-full border-gray-300 rounded-lg">
          <option value="">– Select Trailer Type –</option>
          <?php $__currentLoopData = $trailerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailerType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($trailerType->id); ?>" 
                    <?php if(old('trailer_type_id', $booking->trailer_type_id) == $trailerType->id): echo 'selected'; endif; ?>>
              <?php echo e($trailerType->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['trailer_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>


      
      <div>
        <label class="block text-sm font-medium text-gray-600">🚛 Tipping Type</label>
        <div class="mt-2 space-y-2">
          <div class="flex items-center">
            <input type="radio" id="tipping_type_live" name="tipping_type" value="live_tip" 
                   <?php if(old('tipping_type', $booking->tipping_type) == 'live_tip'): echo 'checked'; endif; ?>
                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
            <label for="tipping_type_live" class="ml-3 flex items-center">
              <span class="text-lg mr-2">🚛📦</span>
              <div>
                <div class="text-sm font-medium text-gray-900">Live Tip</div>
                <div class="text-xs text-gray-500">Unit stays connected during tipping</div>
              </div>
            </label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="tipping_type_drop" name="tipping_type" value="drop" 
                   <?php if(old('tipping_type', $booking->tipping_type) == 'drop'): echo 'checked'; endif; ?>
                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
            <label for="tipping_type_drop" class="ml-3 flex items-center">
              <span class="text-lg mr-2">📦</span>
              <div>
                <div class="text-sm font-medium text-gray-900">Drop</div>
                <div class="text-xs text-gray-500">Unit leaves, trailer handled separately</div>
              </div>
            </label>
          </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">Select how this booking will be handled during tipping</p>
        <?php $__errorArgs = ['tipping_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <?php if($booking->exists): ?>
        <div>
          <label class="block text-sm font-medium text-gray-600">🏗️ Tipping Bay</label>
          <select name="tipping_bay_id" class="mt-1 block w-full border-gray-300 rounded-lg">
            <option value="">– Select Bay –</option>
            <?php if(isset($tippingBays)): ?>
              <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($bay->id); ?>" 
                        <?php if(old('tipping_bay_id', $booking->tipping_bay_id) == $bay->id): echo 'selected'; endif; ?>
                        <?php if($bay->is_occupied && $bay->id != $booking->tipping_bay_id): echo 'disabled'; endif; ?>>
                  <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>) 
                  <?php if($bay->is_occupied && $bay->id != $booking->tipping_bay_id): ?>
                    - Occupied
                  <?php elseif($bay->is_occupied): ?>
                    - Current Bay
                  <?php else: ?>
                    - Available
                  <?php endif; ?>
                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
          </select>
          <?php $__errorArgs = ['tipping_bay_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  
  <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
    <h3 class="text-lg font-medium text-yellow-900 mb-3">📝 Notes & Instructions</h3>
    
    <div class="space-y-4">
      
      <div>
        <label class="block text-sm font-medium text-yellow-800">General Notes</label>
        <textarea name="notes" rows="2"
                  placeholder="Internal notes about this booking..."
                  class="mt-1 block w-full border-yellow-300 rounded bg-white"><?php echo e(old('notes', $booking->notes)); ?></textarea>
        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-yellow-800">Special Instructions</label>
        <textarea name="special_instructions" rows="2"
                  placeholder="Special handling instructions for the driver/operator..."
                  class="mt-1 block w-full border-yellow-300 rounded bg-white"><?php echo e(old('special_instructions', $booking->special_instructions)); ?></textarea>
        <?php $__errorArgs = ['special_instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
  </div>

  
  <?php if($booking->exists && $booking->arrived_at): ?>
    <div class="bg-green-100 p-4 rounded-lg border border-green-300">
      <h3 class="text-lg font-medium text-green-900 mb-2">✅ Arrival Status</h3>
      <p class="text-sm text-green-800">
        <strong>Vehicle Arrived:</strong> <?php echo e($booking->arrived_at->format('d-M-Y H:i:s')); ?>

        <?php if($booking->departed_at): ?>
          <br><strong>Departed:</strong> <?php echo e($booking->departed_at->format('d-M-Y H:i:s')); ?>

        <?php endif; ?>
      </p>
    </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Admin carrier search functionality (similar to customer but with admin prefix)
    const searchInput = document.getElementById('admin-carrier-search');
    const carrierIdInput = document.getElementById('admin-carrier-id');
    const dropdown = document.getElementById('admin-carrier-dropdown');
    const statusSpan = document.getElementById('admin-carrier-status');
    
    if (!searchInput) return; // Exit if elements don't exist
    
    let searchTimeout;
    let selectedCarrierId = carrierIdInput.value;
    let currentPage = 1;
    let isLoading = false;
    
    // Update status based on current state
    function updateStatus() {
        if (selectedCarrierId) {
            statusSpan.textContent = '✓';
            statusSpan.className = 'text-xs text-green-600';
        } else if (searchInput.value.trim()) {
            statusSpan.textContent = '+';
            statusSpan.className = 'text-xs text-blue-600';
            statusSpan.title = 'Will create new carrier';
        } else {
            statusSpan.textContent = '';
            statusSpan.className = 'text-xs';
        }
    }
    
    // Search carriers
    function searchCarriers(query, page = 1) {
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }
        
        if (isLoading) return;
        isLoading = true;
        
        fetch(`<?php echo e(route('api.carriers.search')); ?>?q=${encodeURIComponent(query)}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (page === 1) {
                    populateDropdown(data, query);
                } else {
                    appendToDropdown(data, query);
                }
                currentPage = page;
                isLoading = false;
            })
            .catch(error => {
                console.error('Search failed:', error);
                dropdown.classList.add('hidden');
                isLoading = false;
            });
    }
    
    // Append more results to dropdown
    function appendToDropdown(data, query) {
        // Remove the "Load more" button
        const loadMoreButton = dropdown.querySelector('[onclick*="searchCarriers"]');
        if (loadMoreButton) {
            loadMoreButton.remove();
        }
        
        // Add new carriers
        data.carriers.forEach(carrier => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
            item.innerHTML = `
                <div class="font-medium text-gray-900">${carrier.name}</div>
                <div class="text-xs text-gray-500">
                    ${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}
                </div>
            `;
            item.onclick = () => selectCarrier(carrier.id, carrier.name);
            dropdown.appendChild(item);
        });
        
        // Add "Load more" again if there are still more results
        if (data.has_more) {
            const loadMoreItem = document.createElement('div');
            loadMoreItem.className = 'px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-200 bg-gray-25 text-center';
            loadMoreItem.innerHTML = `<div class="text-sm text-gray-600">📄 Load more carriers...</div>`;
            loadMoreItem.onclick = () => {
                loadMoreItem.innerHTML = '<div class="text-sm text-gray-600">⏳ Loading...</div>';
                searchCarriers(query, currentPage + 1);
            };
            dropdown.appendChild(loadMoreItem);
        }
    }
    
    // Populate dropdown with results
    function populateDropdown(data, query) {
        dropdown.innerHTML = '';
        
        // Show total results if more than displayed
        if (data.total > data.carriers.length) {
            const headerItem = document.createElement('div');
            headerItem.className = 'px-3 py-2 bg-gray-100 border-b border-gray-200 text-xs text-gray-600';
            headerItem.innerHTML = `Showing ${data.carriers.length} of ${data.total} carriers`;
            dropdown.appendChild(headerItem);
        }
        
        // Show existing carriers
        data.carriers.forEach(carrier => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
            item.innerHTML = `
                <div class="font-medium text-gray-900">${carrier.name}</div>
                <div class="text-xs text-gray-500">
                    ${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}
                </div>
            `;
            item.onclick = () => selectCarrier(carrier.id, carrier.name);
            dropdown.appendChild(item);
        });
        
        // Add "Load more" option if there are more results
        if (data.has_more) {
            const loadMoreItem = document.createElement('div');
            loadMoreItem.className = 'px-3 py-2 hover:bg-gray-50 cursor-pointer border-b border-gray-200 bg-gray-25 text-center';
            loadMoreItem.innerHTML = `<div class="text-sm text-gray-600">📄 Load more carriers...</div>`;
            loadMoreItem.onclick = () => {
                loadMoreItem.innerHTML = '<div class="text-sm text-gray-600">⏳ Loading...</div>';
                searchCarriers(query, currentPage + 1);
            };
            dropdown.appendChild(loadMoreItem);
        }
        
        // Add "Create new" option if no exact match
        if (!data.exact_match && query.trim()) {
            const createItem = document.createElement('div');
            createItem.className = 'px-3 py-2 hover:bg-green-50 cursor-pointer border-t-2 border-green-200 bg-green-25';
            createItem.innerHTML = `
                <div class="font-medium text-green-800">➕ Create "${query}"</div>
                <div class="text-xs text-green-600">Add as new carrier and use immediately</div>
            `;
            createItem.onclick = () => quickCreateCarrier(query);
            dropdown.appendChild(createItem);
        }
        
        dropdown.classList.remove('hidden');
    }
    
    // Select existing carrier
    function selectCarrier(id, name) {
        selectedCarrierId = id;
        carrierIdInput.value = id;
        searchInput.value = name;
        dropdown.classList.add('hidden');
        updateStatus();
    }
    
    // Quick create carrier (immediate API call)
    function quickCreateCarrier(name) {
        // Show loading state
        const createButton = dropdown.querySelector('[onclick*="quickCreateCarrier"]');
        if (createButton) {
            createButton.innerHTML = `
                <div class="font-medium text-green-800">⏳ Creating "${name}"...</div>
                <div class="text-xs text-green-600">Please wait...</div>
            `;
        }
        
        fetch('<?php echo e(route('api.carriers.quick-create')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Select the newly created carrier
                selectCarrier(data.carrier.id, data.carrier.name);
                
                // Show success message briefly
                statusSpan.textContent = '✓';
                statusSpan.className = 'text-xs text-green-600';
                statusSpan.title = data.message;
            } else {
                alert('Failed to create carrier. Please try again.');
                dropdown.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Create failed:', error);
            alert('Failed to create carrier. Please try again.');
            dropdown.classList.add('hidden');
        });
    }
    
    // Create new carrier (fallback - no immediate API call)
    function createNewCarrier(name) {
        selectedCarrierId = null;
        carrierIdInput.value = '';
        searchInput.value = name;
        dropdown.classList.add('hidden');
        updateStatus();
    }
    
    // Search input handler
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Reset selection when typing
        selectedCarrierId = null;
        carrierIdInput.value = '';
        currentPage = 1; // Reset pagination
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchCarriers(query, 1);
        }, 300);
        
        updateStatus();
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    // Show dropdown on focus if there's content
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            searchCarriers(this.value);
        }
    });
    
    // Initial status update
    updateStatus();
});
</script><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/_form.blade.php ENDPATH**/ ?>