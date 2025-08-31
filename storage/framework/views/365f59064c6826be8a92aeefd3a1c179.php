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
      <h2 class="font-semibold text-xl">Edit Factory Booking #<?php echo e($factoryBooking->reference); ?></h2>
      <div class="flex space-x-2">
        <a href="<?php echo e(route('app.factory-booking-workflow.show', $factoryBooking)); ?>"
           class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
          🚛 Manage Workflow
        </a>
        <a href="<?php echo e(route('app.factory-bookings.show', $factoryBooking)); ?>"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          📄 View Details
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
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
    <form method="POST" action="<?php echo e(route('app.factory-bookings.update', $factoryBooking)); ?>" class="space-y-6">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      
      <div class="space-y-6">
        
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <h3 class="text-lg font-medium text-blue-900 mb-3">📋 Required Information</h3>
          
          <div class="grid grid-cols-2 gap-4">
            
            <div class="col-span-2">
              <label class="block text-sm font-medium text-blue-800">Reference</label>
              <div class="mt-1 block w-full border-blue-300 rounded-lg bg-gray-50 px-3 py-2 text-gray-600 font-mono">
                <?php echo e($factoryBooking->reference); ?>

              </div>
              <p class="text-xs text-blue-600 mt-1">Reference cannot be changed</p>
            </div>

            
            <div class="col-span-2">
              <label class="block text-sm font-medium text-blue-800">Customer <span class="text-red-500">*</span></label>
              <select name="customer_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
                <option value="">– Choose customer –</option>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($customer->id); ?>"
                    <?php if(old('customer_id', $factoryBooking->customer_id) == $customer->id): echo 'selected'; endif; ?>
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

            
            <div class="col-span-2">
              <label class="block text-sm font-medium text-blue-800">Carrier Company <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="text" 
                       id="admin-carrier-search" 
                       name="carrier_name"
                       value="<?php echo e(old('carrier_name', $factoryBooking->carrier?->name ?? $factoryBooking->carrier_company)); ?>"
                       placeholder="Search or type carrier name..."
                       required
                       autocomplete="off"
                       class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
                
                
                <input type="hidden" 
                       id="admin-carrier-id" 
                       name="carrier_id" 
                       value="<?php echo e(old('carrier_id', $factoryBooking->carrier_id)); ?>">
                
                
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

            
            <div>
              <label class="block text-sm font-medium text-blue-800">Priority (0-100)</label>
              <input type="number" name="priority" min="0" max="100" 
                     value="<?php echo e(old('priority', $factoryBooking->priority)); ?>"
                     placeholder="50"
                     class="mt-1 block w-full border-blue-300 rounded bg-white">
              <p class="text-xs text-blue-600 mt-1">Higher = more urgent</p>
              <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $factoryBooking,'hideActuals' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($factoryBooking),'hide_actuals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
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
          <h3 class="text-lg font-medium text-gray-700 mb-3">🚛 Transportation Details <span class="text-sm font-normal text-gray-500">(Optional - can be updated later)</span></h3>
          
          <div class="grid grid-cols-2 gap-4">
            
            <div>
              <label class="block text-sm font-medium text-gray-600">Vehicle Registration <span class="text-red-500">*</span></label>
              <input type="text" name="vehicle_registration" required
                     value="<?php echo e(old('vehicle_registration', $factoryBooking->vehicle_registration)); ?>"
                     placeholder="e.g., AB12 CDE"
                     class="mt-1 block w-full border-gray-300 rounded-lg uppercase">
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
              <label class="block text-sm font-medium text-gray-600">Trailer Registration</label>
              <input type="text" name="trailer_registration"
                     value="<?php echo e(old('trailer_registration', $factoryBooking->trailer_registration)); ?>"
                     placeholder="e.g., TR12 345"
                     class="mt-1 block w-full border-gray-300 rounded-lg uppercase">
              <?php $__errorArgs = ['trailer_registration'];
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
                          <?php if(old('trailer_type_id', $factoryBooking->trailer_type_id) == $trailerType->id): echo 'selected'; endif; ?>>
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

          </div>
        </div>

        
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
          <h3 class="text-lg font-medium text-yellow-900 mb-3">📝 Notes & Instructions</h3>
          
          <div class="space-y-4">
            
            <div>
              <label class="block text-sm font-medium text-yellow-800">Delivery Notes</label>
              <textarea name="delivery_notes" rows="2"
                        placeholder="Any relevant information about the delivery..."
                        class="mt-1 block w-full border-yellow-300 rounded bg-white"><?php echo e(old('delivery_notes', $factoryBooking->delivery_notes)); ?></textarea>
              <?php $__errorArgs = ['delivery_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
              <label class="block text-sm font-medium text-yellow-800">Gate Staff Notes</label>
              <textarea name="gate_notes" rows="2"
                        placeholder="Internal notes for gate staff and operations..."
                        class="mt-1 block w-full border-yellow-300 rounded bg-white"><?php echo e(old('gate_notes', $factoryBooking->gate_notes)); ?></textarea>
              <?php $__errorArgs = ['gate_notes'];
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

        
        <div class="bg-green-100 p-4 rounded-lg border border-green-300">
          <h3 class="text-lg font-medium text-green-900 mb-2">✅ Arrival Status</h3>
          <div class="text-sm text-green-800 space-y-1">
            <div><strong>Status:</strong> <?php echo e(ucfirst($factoryBooking->status)); ?></div>
            <div><strong>Vehicle Arrived:</strong> <?php echo e($factoryBooking->arrived_at->format('d-M-Y H:i:s')); ?></div>
            <?php if($factoryBooking->processing_started_at): ?>
              <div><strong>Processing Started:</strong> <?php echo e($factoryBooking->processing_started_at->format('d-M-Y H:i:s')); ?></div>
            <?php endif; ?>
            <?php if($factoryBooking->departed_at): ?>
              <div><strong>Departed:</strong> <?php echo e($factoryBooking->departed_at->format('d-M-Y H:i:s')); ?></div>
            <?php endif; ?>
            <div><strong>Time on Site:</strong> <?php echo e($factoryBooking->getTimeOnSite()); ?></div>
          </div>
        </div>
      </div>
      
      
      <div class="mt-6 flex space-x-3">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
          Update Factory Booking
        </button>
        <a href="<?php echo e(route('app.factory-bookings.show', $factoryBooking)); ?>"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded">
           Cancel
        </a>
      </div>
    </form>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-uppercase for vehicle registrations
      const vehicleReg = document.querySelector('input[name="vehicle_registration"]');
      const trailerReg = document.querySelector('input[name="trailer_registration"]');
      
      if (vehicleReg) {
        vehicleReg.addEventListener('input', function(e) {
          e.target.value = e.target.value.toUpperCase();
        });
      }
      
      if (trailerReg) {
        trailerReg.addEventListener('input', function(e) {
          e.target.value = e.target.value.toUpperCase();
        });
      }
      
      // Carrier search functionality
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/factory-bookings/edit.blade.php ENDPATH**/ ?>