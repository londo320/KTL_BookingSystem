<div class="space-y-6">
  {{-- Slot --}}
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
      Time Slot <span class="text-red-500">*</span>
      <span class="text-xs text-gray-500 ml-2">🌐 = Public, 🔒 = Customer Restricted</span>
    </label>
    <select name="slot_id" required class="mt-1 block w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
      <option value="">– Choose depot & booking type first –</option>
      {{-- Slots will be loaded dynamically via JavaScript --}}
    </select>
    @error('slot_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
  </div>

  {{-- Transportation Details Section --}}
  <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-medium text-blue-900 mb-3">🚛 Transportation Details</h3>
    <p class="text-sm text-blue-700 mb-4">Optional vehicle and transport information</p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Vehicle Registration --}}
      <div>
        <label class="block text-sm font-medium text-blue-800 mb-1">Vehicle Registration</label>
        <input type="text" name="vehicle_registration"
               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
               placeholder="e.g., AB12 CDE"
               class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        @error('vehicle_registration')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Container Number --}}
      <div>
        <label class="block text-sm font-medium text-blue-800 mb-1">Container/Trailer Number</label>
        <input type="text" name="container_number"
               value="{{ old('container_number', $booking->container_number) }}"
               placeholder="e.g., CONT123456"
               class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        @error('container_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Seal Number --}}
      <div>
        <label class="block text-sm font-medium text-blue-800 mb-1">Seal Number</label>
        <input type="text" name="seal_number"
               value="{{ old('seal_number', $booking->seal_number) }}"
               placeholder="e.g., SEAL123456"
               class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        @error('seal_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Carrier Company --}}
      <div class="md:col-span-2">
        <label class="block text-sm font-medium text-blue-800 mb-1">
          Carrier Company <span class="text-red-500">*</span>
        </label>
        <div class="relative">
          <input type="text" 
                 id="carrier-search" 
                 name="carrier_name"
                 value="{{ old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company) }}"
                 placeholder="Search or type carrier name..."
                 required
                 autocomplete="off"
                 class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
          
          {{-- Hidden carrier_id field --}}
          <input type="hidden" 
                 id="carrier-id" 
                 name="carrier_id" 
                 value="{{ old('carrier_id', $booking->carrier_id) }}">
          
          {{-- Search dropdown --}}
          <div id="carrier-dropdown" 
               class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            {{-- Results will be populated by JavaScript --}}
          </div>
          
          {{-- Status indicators --}}
          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <span id="carrier-status" class="text-xs"></span>
          </div>
        </div>
        
        @error('carrier_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        @error('carrier_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- PO Numbers Section --}}
  <div id="po-section-container" data-show-sku="{{ $showSkuFields ? 'true' : 'false' }}"
       class="bg-green-50 p-4 rounded-lg border border-green-200">
    <h3 class="text-lg font-medium text-green-900 mb-3">📦 Purchase Orders & Quantities</h3>
    <p class="text-sm text-green-700 mb-4">At least one PO with expected quantities is required</p>
    <x-booking-po-numbers :booking="$booking" :customer_view="true" :hide_actuals="true" :customer_id="auth()->user()->getCustomerId()" :show_sku_fields="$showSkuFields" />
  </div>

  {{-- Notes Section --}}
  <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
    <h3 class="text-lg font-medium text-yellow-900 mb-3">📝 Additional Information</h3>
    
    {{-- Internal Notes --}}
    <div class="mb-4">
      <label class="block text-sm font-medium text-yellow-800 mb-2">Internal Notes</label>
      <textarea name="notes" rows="2"
                placeholder="Any internal notes or comments about this booking..."
                class="mt-1 block w-full border-yellow-300 rounded-lg bg-white focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">{{ old('notes', $booking->notes) }}</textarea>
      @error('notes')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Special Instructions --}}
    <div>
      <label class="block text-sm font-medium text-yellow-800 mb-2">Special Instructions</label>
      <textarea name="special_instructions" rows="2"
                placeholder="Special handling instructions for the driver/operator..."
                class="mt-1 block w-full border-yellow-300 rounded-lg bg-white focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">{{ old('special_instructions', $booking->special_instructions) }}</textarea>
      @error('special_instructions')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
    </div>
  </div>

  @if($booking->exists)
    {{-- Expected Arrival (only show for existing bookings) --}}
    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
      <h3 class="text-lg font-medium text-purple-900 mb-3">📞 Arrival Information</h3>
      
      <div>
        <label class="block text-sm font-medium text-purple-800 mb-2">Expected Arrival Time (if different from slot)</label>
        <input type="datetime-local" name="estimated_arrival"
               value="{{ old('estimated_arrival', $booking->estimated_arrival ? $booking->estimated_arrival->format('Y-m-d\TH:i') : '') }}"
               class="mt-1 block w-full border-purple-300 rounded-lg bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
        <p class="text-xs text-purple-600 mt-1">💡 Update this if your expected arrival time changes from the original slot time</p>
        @error('estimated_arrival')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    @if($booking->arrived_at)
      <div class="bg-green-50 p-4 rounded-lg border border-green-200">
        <h3 class="text-lg font-medium text-green-900 mb-2">✅ Arrival Status</h3>
        <p class="text-sm text-green-800">
          <strong>Vehicle Arrived:</strong> {{ $booking->arrived_at->format('d-M-Y H:i:s') }}
          @if($booking->departed_at)
            <br><strong>Departed:</strong> {{ $booking->departed_at->format('d-M-Y H:i:s') }}
          @endif
        </p>
      </div>
    @endif
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
            statusSpan.title = 'Existing carrier selected';
        } else if (searchInput.value.trim()) {
            statusSpan.textContent = '+';
            statusSpan.className = 'text-xs text-blue-600';
            statusSpan.title = 'Will create new carrier';
        } else {
            statusSpan.textContent = '';
            statusSpan.className = 'text-xs';
            statusSpan.title = '';
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
        
        fetch(`{{ route('api.carriers.search') }}?q=${encodeURIComponent(query)}&page=${page}`)
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
            loadMoreItem.innerHTML = `
                <div class="text-sm text-gray-600">📄 Load more carriers...</div>
            `;
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
            loadMoreItem.innerHTML = `
                <div class="text-sm text-gray-600">📄 Load more carriers...</div>
            `;
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
        
        fetch('{{ route('api.carriers.quick-create') }}', {
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
</script>