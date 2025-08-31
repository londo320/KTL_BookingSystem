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
        <h2 class="font-semibold text-xl text-gray-800">Register Factory Delivery</h2>
        <p class="text-sm text-gray-600 mt-1">Quick registration for ad-hoc arrivals at the gate</p>
      </div>
      <div class="flex gap-2">
        <a href="<?php echo e(route('app.factory-bookings.index')); ?>"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          ← Back to Factory Bookings
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-4xl mx-auto">
    <?php if($errors->any()): ?>
      <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="text-red-800 font-medium mb-2">Please correct the following errors:</h3>
        <ul class="text-red-700 text-sm space-y-1">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>• <?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('app.factory-bookings.store')); ?>" class="space-y-6">
      <?php echo csrf_field(); ?>
      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🚪</span>
          Gate Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">
              Depot <span class="text-red-500">*</span>
            </label>
            <select name="depot_id" id="depot_id" required 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Depot</option>
              <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($depot->id); ?>" <?php echo e(old('depot_id') == $depot->id ? 'selected' : ''); ?>>
                  <?php echo e($depot->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php if($depots->count() === 1): ?>
              <p class="mt-1 text-xs text-gray-500">Auto-selected based on your access</p>
            <?php endif; ?>
          </div>
          
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
              Priority (0-100)
            </label>
            <div class="relative">
              <input type="number" name="priority" id="priority" min="0" max="100" 
                     value="<?php echo e(old('priority', 50)); ?>"
                     class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <span class="text-gray-400 text-sm">Default: 50</span>
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Higher numbers = higher priority (80+ urgent, 50 normal, 20- low)
            </p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🏢</span>
          Customer & Carrier Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
              Customer <span class="text-red-500">*</span>
            </label>
            <select name="customer_id" id="customer_id" required 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Customer</option>
              <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($customer->id); ?>" <?php echo e(old('customer_id') == $customer->id ? 'selected' : ''); ?>>
                  <?php echo e($customer->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div>
            <label for="carrier_search" class="block text-sm font-medium text-gray-700 mb-2">
              Carrier Company <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <input type="text" 
                     id="carrier-search" 
                     name="carrier_name"
                     value="<?php echo e(old('carrier_name')); ?>"
                     placeholder="Search or type carrier name..."
                     required
                     autocomplete="off"
                     class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
              
              <input type="hidden" 
                     id="carrier-id" 
                     name="carrier_id" 
                     value="<?php echo e(old('carrier_id')); ?>">
              
              <div id="carrier-dropdown" 
                   class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                
              </div>
              
              <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                <span id="carrier-status" class="text-xs"></span>
              </div>
            </div>
            <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🚛</span>
          Vehicle Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label for="vehicle_registration" class="block text-sm font-medium text-gray-700 mb-2">
              Vehicle Registration <span class="text-red-500">*</span>
            </label>
            <input type="text" name="vehicle_registration" id="vehicle_registration" required 
                   value="<?php echo e(old('vehicle_registration')); ?>"
                   placeholder="e.g., AB12 XYZ"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
          </div>
          
          <div>
            <label for="trailer_registration" class="block text-sm font-medium text-gray-700 mb-2">
              Trailer Registration (Optional)
            </label>
            <input type="text" name="trailer_registration" id="trailer_registration" 
                   value="<?php echo e(old('trailer_registration')); ?>"
                   placeholder="e.g., TR12 345"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
          </div>
          
          <div>
            <label for="trailer_type_id" class="block text-sm font-medium text-gray-700 mb-2">
              🚛 Trailer Type <span class="text-red-500">*</span>
            </label>
            <select name="trailer_type_id" id="trailer_type_id" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">– Select Trailer Type –</option>
              <?php $__currentLoopData = $trailerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailerType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($trailerType->id); ?>" <?php echo e(old('trailer_type_id') == $trailerType->id ? 'selected' : ''); ?>>
                  <?php echo e($trailerType->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">Required: Type and size of trailer/container</p>
          </div>
          
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-3">🚛 Tipping Type <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <label class="tipping-type-option flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:shadow-md transition-all duration-200" data-value="live_tip">
                <input type="radio" name="tipping_type" value="live_tip" 
                       <?php if(old('tipping_type') == 'live_tip'): echo 'checked'; endif; ?>
                       class="sr-only" required>
                <div class="flex items-center w-full">
                  <span class="text-3xl mr-4">🚛📦</span>
                  <div class="flex-1">
                    <div class="font-semibold text-lg text-gray-900">Live Tip</div>
                    <div class="text-sm text-gray-600 mt-1">Vehicle stays connected during tipping</div>
                    <div class="text-xs text-blue-600 mt-2 font-medium">Best for: Quick turnaround, driver waiting</div>
                  </div>
                  <div class="selection-indicator ml-3 w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                    <div class="w-3 h-3 bg-blue-500 rounded-full opacity-0 transition-opacity"></div>
                  </div>
                </div>
              </label>
              <label class="tipping-type-option flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 hover:shadow-md transition-all duration-200" data-value="drop">
                <input type="radio" name="tipping_type" value="drop" 
                       <?php if(old('tipping_type') == 'drop'): echo 'checked'; endif; ?>
                       class="sr-only" required>
                <div class="flex items-center w-full">
                  <span class="text-3xl mr-4">📦🚚</span>
                  <div class="flex-1">
                    <div class="font-semibold text-lg text-gray-900">Drop</div>
                    <div class="text-sm text-gray-600 mt-1">Vehicle leaves, trailer handled separately</div>
                    <div class="text-xs text-green-600 mt-2 font-medium">Best for: Long jobs, trailer swaps</div>
                  </div>
                  <div class="selection-indicator ml-3 w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full opacity-0 transition-opacity"></div>
                  </div>
                </div>
              </label>
            </div>
            <p class="text-xs text-gray-500 mt-1">Required: How will this delivery be handled during tipping?</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">📝</span>
          Additional Information
        </h3>
        <div class="space-y-4">
          
          <div>
            <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Delivery Notes (Optional)
            </label>
            <textarea name="delivery_notes" id="delivery_notes" rows="3" 
                      placeholder="Any relevant information about the delivery..."
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('delivery_notes')); ?></textarea>
          </div>
          
          <div>
            <label for="gate_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Gate Staff Notes (Optional)
            </label>
            <textarea name="gate_notes" id="gate_notes" rows="3" 
                      placeholder="Internal notes for gate staff and operations..."
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('gate_notes')); ?></textarea>
          </div>
        </div>
      </div>
      
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-blue-800 font-medium mb-2">📋 What happens next?</h4>
        <ul class="text-blue-700 text-sm space-y-1">
          <li>• A unique reference (FAC-YYYY-XXX) will be automatically generated</li>
          <li>• A PO number matching the reference will be created for tracking</li>
          <li>• The delivery will immediately appear in tipping workflow queues</li>
          <li>• Factory vehicles must be tipped within 60 minutes of arrival</li>
          <li>• Complete tracking and history will be maintained</li>
        </ul>
      </div>
      
      <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <a href="<?php echo e(route('app.factory-bookings.index')); ?>" 
           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
          Cancel
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          🚛 Register Factory Delivery
        </button>
      </div>
    </form>
  </div>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-uppercase script for vehicle registrations
      document.getElementById('vehicle_registration').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
      });
      document.getElementById('trailer_registration').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
      });
      // Auto-select depot if only one available
      const depotSelect = document.getElementById('depot_id');
      if (depotSelect.options.length === 2) { // Only "Select Depot" + one depot
        depotSelect.selectedIndex = 1;
      }
      // Carrier search functionality
      const searchInput = document.getElementById('carrier-search');
      const carrierIdInput = document.getElementById('carrier-id');
      const dropdown = document.getElementById('carrier-dropdown');
      const statusSpan = document.getElementById('carrier-status');
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
          createItem.onclick = () => selectNewCarrier(query);
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
      // Select new carrier (just set the name, creation happens on form submit)
      function selectNewCarrier(name) {
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
        currentPage = 1;
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
      // Carrier name capitalization
      searchInput.addEventListener('blur', function() {
        if (this.value.trim()) {
          this.value = capitalizeWords(this.value.trim());
        }
      });
      function capitalizeWords(str) {
        return str.replace(/\b\w+/g, function(word) {
          return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        });
      }
      // Tipping Type Visual Selection
      const tippingTypeOptions = document.querySelectorAll('.tipping-type-option');
      const tippingTypeInputs = document.querySelectorAll('input[name="tipping_type"]');
      function updateTippingTypeSelection() {
        // Reset all options
        tippingTypeOptions.forEach(option => {
          const indicator = option.querySelector('.selection-indicator div');
          const border = option.querySelector('.selection-indicator');
          // Reset styles
          option.classList.remove('border-blue-500', 'border-green-500', 'bg-blue-50', 'bg-green-50', 'ring-2', 'ring-blue-200', 'ring-green-200');
          option.classList.add('border-gray-200');
          border.classList.remove('border-blue-500', 'border-green-500');
          border.classList.add('border-gray-300');
          indicator.classList.remove('opacity-100');
          indicator.classList.add('opacity-0');
        });
        // Highlight selected option
        const selectedInput = document.querySelector('input[name="tipping_type"]:checked');
        if (selectedInput) {
          const selectedOption = selectedInput.closest('.tipping-type-option');
          const indicator = selectedOption.querySelector('.selection-indicator div');
          const border = selectedOption.querySelector('.selection-indicator');
          const value = selectedInput.value;
          if (value === 'live_tip') {
            selectedOption.classList.remove('border-gray-200');
            selectedOption.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'ring-blue-200');
            border.classList.remove('border-gray-300');
            border.classList.add('border-blue-500');
            indicator.classList.remove('opacity-0');
            indicator.classList.add('opacity-100');
          } else if (value === 'drop') {
            selectedOption.classList.remove('border-gray-200');
            selectedOption.classList.add('border-green-500', 'bg-green-50', 'ring-2', 'ring-green-200');
            border.classList.remove('border-gray-300');
            border.classList.add('border-green-500');
            indicator.classList.remove('opacity-0');
            indicator.classList.add('opacity-100');
          }
        }
      }
      // Add click handlers to tipping type options
      tippingTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
          const input = this.querySelector('input[type="radio"]');
          input.checked = true;
          updateTippingTypeSelection();
        });
      });
      // Add change handlers to radio inputs (for keyboard navigation)
      tippingTypeInputs.forEach(input => {
        input.addEventListener('change', updateTippingTypeSelection);
      });
      // Initial updates
      updateStatus();
      updateTippingTypeSelection();
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-bookings/create.blade.php ENDPATH**/ ?>