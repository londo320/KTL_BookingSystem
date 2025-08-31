
<?php
    $hasArrived = $booking->arrived_at;
    $cutoffPassed = $booking->slot && $booking->slot->locked_at && $booking->slot->locked_at->isPast();
    $canEditPO = !$hasArrived && !$cutoffPassed;
?>

<div class="space-y-6">
    
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
        <h3 class="text-lg font-medium text-blue-900 mb-2">📅 Booking Slot</h3>
        <div class="bg-white p-3 rounded border">
            <strong><?php echo e($booking->slot->depot->name); ?></strong><br>
            <span class="text-gray-600">
                <?php echo e($booking->slot->start_at->format('l, j F Y')); ?><br>
                <?php echo e($booking->slot->start_at->format('H:i')); ?> → <?php echo e($booking->slot->end_at->format('H:i')); ?>

            </span>
        </div>
        <p class="text-sm text-blue-700 mt-2">
            🔄 Need to change your slot? Use the "Rebook" button above instead.
        </p>
        <input type="hidden" name="slot_id" value="<?php echo e($booking->slot->id); ?>">
    </div>

    
    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
        <h3 class="text-lg font-medium text-green-900 mb-3">📦 Purchase Orders</h3>
        <?php if($canEditPO): ?>
            <p class="text-sm text-green-700 mb-4">You can update your PO numbers and expected quantities until your vehicle arrives or the cutoff period passes.</p>
            <?php if (isset($component)) { $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $booking,'customerView' => true,'hideActuals' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking),'customer_view' => true,'hide_actuals' => true]); ?>
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
        <?php else: ?>
            <div class="bg-gray-100 p-3 rounded border">
                <p class="text-sm text-gray-600 mb-3">
                    <?php if($hasArrived): ?>
                        ⚠️ PO editing disabled - Vehicle has already arrived
                    <?php else: ?>
                        ⚠️ PO editing disabled - Cutoff period has passed
                    <?php endif; ?>
                </p>
                <?php if (isset($component)) { $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $booking,'readonly' => true,'customerView' => true,'hideActuals' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking),'readonly' => true,'customer_view' => true,'hide_actuals' => true]); ?>
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
        <?php endif; ?>
    </div>

    
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
        <h3 class="text-lg font-medium text-blue-900 mb-3">🚛 Transportation Details</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <div>
                <label class="block text-sm font-medium text-blue-800 mb-1">Vehicle Registration</label>
                <input type="text" name="vehicle_registration"
                       value="<?php echo e(old('vehicle_registration', $booking->vehicle_registration)); ?>"
                       placeholder="e.g., AB12 CDE"
                       class="w-full border-blue-300 rounded-lg bg-white">
                <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-blue-800 mb-1">Container/Trailer Number</label>
                <input type="text" name="container_number"
                       value="<?php echo e(old('container_number', $booking->container_number)); ?>"
                       placeholder="e.g., CONT123456"
                       class="w-full border-blue-300 rounded-lg bg-white">
                <?php $__errorArgs = ['container_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-blue-800 mb-1">Carrier Company</label>
                <div class="relative">
                    <input type="text" 
                           id="customer-carrier-search" 
                           name="carrier_name"
                           value="<?php echo e(old('carrier_name', $booking->carrier?->name ?? $booking->carrier_name)); ?>"
                           placeholder="Search or type carrier name..."
                           autocomplete="off"
                           class="w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
                    
                    
                    <input type="hidden" 
                           id="customer-carrier-id" 
                           name="carrier_id" 
                           value="<?php echo e(old('carrier_id', $booking->carrier_id)); ?>">
                    
                    
                    <div id="customer-carrier-dropdown" 
                         class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                      
                    </div>
                    
                    
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                      <span id="customer-carrier-status" class="text-xs"></span>
                    </div>
                </div>
                <?php $__errorArgs = ['carrier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['carrier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="text-xs text-blue-600 mt-1">Search existing carriers or type to create new</p>
            </div>
        </div>
    </div>

    
    <?php if(!$hasArrived): ?>
    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
        <h3 class="text-lg font-medium text-purple-900 mb-3">📞 Expected Arrival Time</h3>
        <div>
            <label class="block text-sm font-medium text-purple-800 mb-2">Expected Arrival Time (if different from slot)</label>
            <input type="datetime-local" name="estimated_arrival"
                   value="<?php echo e(old('estimated_arrival', $booking->estimated_arrival ? $booking->estimated_arrival->format('Y-m-d\TH:i') : '')); ?>"
                   class="w-full border-purple-300 rounded-lg bg-white">
            <p class="text-xs text-purple-600 mt-1">💡 Update this if your expected arrival time changes from the original slot time</p>
            <?php $__errorArgs = ['estimated_arrival'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
        <h3 class="text-lg font-medium text-yellow-900 mb-3">📝 Special Instructions</h3>
        <div>
            <label class="block text-sm font-medium text-yellow-800 mb-2">Special Instructions for Driver/Operator</label>
            <textarea name="special_instructions" rows="3"
                      placeholder="Any special handling instructions..."
                      class="w-full border-yellow-300 rounded-lg bg-white"><?php echo e(old('special_instructions', $booking->special_instructions)); ?></textarea>
            <?php $__errorArgs = ['special_instructions'];
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Customer Carrier Search
    const searchInput = document.getElementById('customer-carrier-search');
    const carrierIdInput = document.getElementById('customer-carrier-id');
    const dropdown = document.getElementById('customer-carrier-dropdown');
    const statusSpan = document.getElementById('customer-carrier-status');
    
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
        
        fetch(`/api/carriers/search?query=${encodeURIComponent(query)}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                currentPage = page;
                if (page === 1) {
                    populateDropdown(data, query);
                } else {
                    appendToDropdown(data, query);
                }
            })
            .catch(console.error)
            .finally(() => {
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
            item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-200';
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
        
        // Add carrier results
        data.carriers.forEach(carrier => {
            const item = document.createElement('div');
            item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-200';
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
        
        fetch('/api/carriers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                alert('Error creating carrier: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error creating carrier:', error);
            alert('Error creating carrier');
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
        if (!e.target.closest('#customer-carrier-search') && !e.target.closest('#customer-carrier-dropdown')) {
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
    
    // Carrier name capitalization
    function capitalizeWords(str) {
        return str.replace(/\w\S*/g, function(txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }
    
    searchInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.value = capitalizeWords(this.value.trim());
        }
    });
});
</script><?php /**PATH /Users/londo/Herd/test/resources/views/customer/bookings/_form_customer_edit.blade.php ENDPATH**/ ?>