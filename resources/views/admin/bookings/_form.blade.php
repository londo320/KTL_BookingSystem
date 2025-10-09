<div class="space-y-6">
  {{-- REQUIRED FIELDS --}}
  <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
    <h3 class="text-lg font-medium text-blue-900 mb-3">📋 Required Information</h3>
    
    <div class="grid grid-cols-2 gap-4">
      {{-- Customer --}}
      @if(auth()->user()->hasRole('admin') || auth()->user()->hasFunction('customers.view') || auth()->user()->hasFunction('bookings.create') || request()->routeIs('app.*'))
        <div class="col-span-2">
          <label class="block text-sm font-medium text-blue-800">Customer <span class="text-red-500">*</span></label>
          <select name="customer_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
            <option value="">– Choose customer –</option>
            @foreach($customers as $customer)
              <option value="{{ $customer->id }}"
                @selected(old('customer_id', $booking->customer_id) == $customer->id)
              >
                {{ $customer->name }}
              </option>
            @endforeach
          </select>
          @error('customer_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
        </div>
      @endif

      {{-- Slot --}}
      <div class="col-span-2">
        <label class="block text-sm font-medium text-blue-800">Slot <span class="text-red-500">*</span>
          @if($booking->exists)
            <span class="text-xs text-red-600 ml-2">⚠️ Slot changes disabled - Use Rebook button instead</span>
          @endif
        </label>
        <select name="slot_id" required @if($booking->exists) disabled @endif class="mt-1 block w-full border-blue-300 rounded @if($booking->exists) bg-gray-100 text-gray-500 cursor-not-allowed @else bg-white @endif">
          @if($booking->exists && $booking->slot)
            <option value="{{ $booking->slot->id }}" selected>
              {{ $booking->slot->depot->name }} - 
              {{ $booking->slot->start_at->format('D d-M H:i') }} → {{ $booking->slot->end_at->format('H:i') }}
            </option>
          @else
            <option value="">– Choose slot –</option>
            @php
              $groupedSlots = $slots->sortBy('start_at')->groupBy(fn($slot) => $slot->depot->name);
            @endphp
            @foreach($groupedSlots as $depotName => $depotSlots)
              <optgroup label="{{ $depotName }}">
                @foreach($depotSlots as $slot)
                  @php
                    $isRestricted = $slot->allowed_customers->count() > 0;
                  @endphp
                  <option value="{{ $slot->id }}"
                    @selected(old('slot_id', $booking->slot_id) == $slot->id)>
                    {{ $isRestricted ? '🔒' : '🌐' }} {{ $slot->start_at->format('D d-M H:i') }} → {{ $slot->end_at->format('H:i') }}
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          @endif
        </select>
        
        {{-- Hidden input to preserve slot_id when editing existing booking --}}
        @if($booking->exists && $booking->slot)
          <input type="hidden" name="slot_id" value="{{ $booking->slot->id }}">
        @endif
        
        @error('slot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      {{-- Booking Type --}}
      <div>
        <label class="block text-sm font-medium text-blue-800">Booking Type <span class="text-red-500">*</span></label>
        <select name="booking_type_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
          <option value="">– Choose type –</option>
          @foreach($types as $type)
            <option value="{{ $type->id }}"
              @selected(old('booking_type_id', $booking->booking_type_id) == $type->id)
            >
              {{ $type->name }}
            </option>
          @endforeach
        </select>
        @error('booking_type_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
      </div>

      {{-- Carrier Company --}}
      <div>
        <label class="block text-sm font-medium text-blue-800">Carrier Company <span class="text-red-500">*</span></label>
        <div class="relative">
          <input type="text" 
                 id="admin-carrier-search" 
                 name="carrier_name"
                 value="{{ old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company) }}"
                 placeholder="Search or type carrier name..."
                 required
                 autocomplete="off"
                 class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
          
          {{-- Hidden carrier_id field --}}
          <input type="hidden" 
                 id="admin-carrier-id" 
                 name="carrier_id" 
                 value="{{ old('carrier_id', $booking->carrier_id) }}">
          
          {{-- Search dropdown --}}
          <div id="admin-carrier-dropdown" 
               class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            {{-- Results will be populated by JavaScript --}}
          </div>
          
          {{-- Status indicators --}}
          <div class="absolute inset-y-0 right-0 flex items-center pr-3">
            <span id="admin-carrier-status" class="text-xs"></span>
          </div>
        </div>
        
        <div class="mt-2">
          <a href="{{ route('app.carriers.create') }}" target="_blank"
             class="text-xs text-blue-600 hover:text-blue-800 underline">
            🏢 Manage carriers
          </a>
        </div>
        
        @error('carrier_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        @error('carrier_name')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- PO NUMBERS SECTION --}}
  <div class="bg-green-50 p-4 rounded-lg border border-green-200">
    <h3 class="text-lg font-medium text-green-900 mb-3">📦 PO Numbers & Expected Quantities</h3>
    <p class="text-sm text-green-700 mb-3">At least one PO with expected quantities is required</p>
    <x-booking-po-numbers :booking="$booking" :hide_actuals="!$booking->exists" />
  </div>

  {{-- OPTIONAL TRANSPORTATION DETAILS --}}
  <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
    <h3 class="text-lg font-medium text-gray-700 mb-3">🚛 Transportation Details <span class="text-sm font-normal text-gray-500">(Optional - can be added later)</span></h3>
    
    <div class="grid grid-cols-2 gap-4">
      {{-- Vehicle Registration --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Vehicle Registration</label>
        <input type="text" name="vehicle_registration"
               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
               placeholder="e.g., AB12 CDE"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        @error('vehicle_registration')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Container Number --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Vehicle/Trailer Number</label>
        <input type="text" name="container_number"
               value="{{ old('container_number', $booking->container_number) }}"
               placeholder="e.g., CONT123456"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        @error('container_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Seal Number --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Seal Number</label>
        <input type="text" name="seal_number"
               value="{{ old('seal_number', $booking->seal_number) }}"
               placeholder="e.g., SEAL123456"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        @error('seal_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Supplier --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Supplier</label>
        <input type="text"
               id="admin-supplier-input"
               name="supplier"
               value="{{ old('supplier', $booking->supplier) }}"
               placeholder="Enter supplier name..."
               class="mt-1 block w-full border-gray-300 rounded-lg">
        @error('supplier')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Haulier --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Haulier</label>
        <input type="text"
               id="admin-haulier-input"
               name="haulier"
               value="{{ old('haulier', $booking->haulier) }}"
               placeholder="Enter haulier name..."
               class="mt-1 block w-full border-gray-300 rounded-lg">
        @error('haulier')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Contact Name with autocomplete --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Contact Name</label>
        <div class="relative">
          <input type="text"
                 id="admin-contact-name-input"
                 name="contact_name"
                 value="{{ old('contact_name', $booking->contact_name) }}"
                 placeholder="Search or type contact name..."
                 autocomplete="off"
                 class="mt-1 block w-full border-gray-300 rounded-lg pr-10">

          {{-- Search dropdown --}}
          <div id="admin-contact-dropdown"
               class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
            {{-- Results will be populated by JavaScript --}}
          </div>

          {{-- Status indicator --}}
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 mt-1">
            <span id="admin-contact-status" class="text-xs"></span>
          </div>
        </div>
        @error('contact_name')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Contact Phone --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Contact Phone</label>
        <input type="text"
               id="admin-contact-phone-input"
               name="contact_phone"
               value="{{ old('contact_phone', $booking->contact_phone) }}"
               placeholder="e.g., 07123456789"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        @error('contact_phone')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Trailer Type --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">Trailer Type</label>
        <select name="trailer_type_id" class="mt-1 block w-full border-gray-300 rounded-lg">
          <option value="">– Select Trailer Type –</option>
          @foreach($trailerTypes as $trailerType)
            <option value="{{ $trailerType->id }}" 
                    @selected(old('trailer_type_id', $booking->trailer_type_id) == $trailerType->id)>
              {{ $trailerType->name }}
            </option>
          @endforeach
        </select>
        @error('trailer_type_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>


      {{-- Tipping Type --}}
      <div>
        <label class="block text-sm font-medium text-gray-600">🚛 Tipping Type</label>
        <div class="mt-2 space-y-2">
          <div class="flex items-center">
            <input type="radio" id="tipping_type_live" name="tipping_type" value="live_tip" 
                   @checked(old('tipping_type', $booking->tipping_type) == 'live_tip')
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
                   @checked(old('tipping_type', $booking->tipping_type) == 'drop')
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
        @error('tipping_type')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Tipping Bay (only show for existing bookings) --}}
      @if($booking->exists)
        <div>
          <label class="block text-sm font-medium text-gray-600">🏗️ Tipping Bay</label>
          <select name="tipping_bay_id" class="mt-1 block w-full border-gray-300 rounded-lg">
            <option value="">– Select Bay –</option>
            @if(isset($tippingBays))
              @foreach($tippingBays as $bay)
                <option value="{{ $bay->id }}" 
                        @selected(old('tipping_bay_id', $booking->tipping_bay_id) == $bay->id)
                        @disabled($bay->is_occupied && $bay->id != $booking->tipping_bay_id)>
                  {{ $bay->name }} ({{ $bay->depot->name }}) 
                  @if($bay->is_occupied && $bay->id != $booking->tipping_bay_id)
                    - Occupied
                  @elseif($bay->is_occupied)
                    - Current Bay
                  @else
                    - Available
                  @endif
                </option>
              @endforeach
            @endif
          </select>
          @error('tipping_bay_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>
      @endif
    </div>
  </div>

  {{-- NOTES & INSTRUCTIONS --}}
  <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
    <h3 class="text-lg font-medium text-yellow-900 mb-3">📝 Notes & Instructions</h3>
    
    <div class="space-y-4">
      {{-- General Notes --}}
      <div>
        <label class="block text-sm font-medium text-yellow-800">General Notes</label>
        <textarea name="notes" rows="2"
                  placeholder="Internal notes about this booking..."
                  class="mt-1 block w-full border-yellow-300 rounded bg-white">{{ old('notes', $booking->notes) }}</textarea>
        @error('notes')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Special Instructions --}}
      <div>
        <label class="block text-sm font-medium text-yellow-800">Special Instructions</label>
        <textarea name="special_instructions" rows="2"
                  placeholder="Special handling instructions for the driver/operator..."
                  class="mt-1 block w-full border-yellow-300 rounded bg-white">{{ old('special_instructions', $booking->special_instructions) }}</textarea>
        @error('special_instructions')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- ARRIVAL STATUS (if arrived) --}}
  @if($booking->exists && $booking->arrived_at)
    <div class="bg-green-100 p-4 rounded-lg border border-green-300">
      <h3 class="text-lg font-medium text-green-900 mb-2">✅ Arrival Status</h3>
      <p class="text-sm text-green-800">
        <strong>Vehicle Arrived:</strong> {{ $booking->arrived_at->format('d-M-Y H:i:s') }}
        @if($booking->departed_at)
          <br><strong>Departed:</strong> {{ $booking->departed_at->format('d-M-Y H:i:s') }}
        @endif
      </p>
    </div>
  @endif
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

    // ============================================
    // Contact Name Autocomplete with Phone Lookup
    // ============================================
    const contactNameInput = document.getElementById('admin-contact-name-input');
    const contactPhoneInput = document.getElementById('admin-contact-phone-input');
    const contactDropdown = document.getElementById('admin-contact-dropdown');
    const contactStatus = document.getElementById('admin-contact-status');
    const supplierInput = document.getElementById('admin-supplier-input');
    const haulierInput = document.getElementById('admin-haulier-input');
    const slotSelect = document.querySelector('select[name="slot_id"]');

    if (contactNameInput) {
        let contactSearchTimeout;

        // Search contacts as user types
        contactNameInput.addEventListener('input', function() {
            const query = this.value.trim();

            clearTimeout(contactSearchTimeout);

            if (query.length < 2) {
                contactDropdown.classList.add('hidden');
                return;
            }

            contactStatus.textContent = '⏳';
            contactStatus.className = 'text-xs text-gray-400';

            contactSearchTimeout = setTimeout(() => {
                searchContacts(query);
            }, 300);
        });

        // Search contacts via API
        function searchContacts(query) {
            const depot_id = slotSelect?.value ? getDepotFromSlot(slotSelect.value) : null;
            const supplier = supplierInput?.value || '';
            const haulier = haulierInput?.value || '';

            const params = new URLSearchParams({
                query: query,
                ...(depot_id && { depot_id }),
                ...(supplier && { supplier }),
                ...(haulier && { haulier })
            });

            fetch(`{{ route('api.contacts.search') }}?${params}`)
                .then(response => response.json())
                .then(contacts => {
                    populateContactDropdown(contacts);
                    contactStatus.textContent = '';
                })
                .catch(error => {
                    console.error('Contact search failed:', error);
                    contactDropdown.classList.add('hidden');
                    contactStatus.textContent = '';
                });
        }

        // Populate contact dropdown
        function populateContactDropdown(contacts) {
            contactDropdown.innerHTML = '';

            if (contacts.length === 0) {
                contactDropdown.classList.add('hidden');
                return;
            }

            contacts.forEach(contact => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                item.innerHTML = `
                    <div class="font-medium text-gray-900">${contact.name}</div>
                    <div class="text-xs text-gray-600">${contact.phone}</div>
                    ${contact.supplier || contact.haulier ? `
                        <div class="text-xs text-gray-500">
                            ${contact.supplier ? 'Supplier: ' + contact.supplier : ''}
                            ${contact.haulier ? ' Haulier: ' + contact.haulier : ''}
                        </div>
                    ` : ''}
                `;
                item.onclick = () => selectContact(contact);
                contactDropdown.appendChild(item);
            });

            contactDropdown.classList.remove('hidden');
        }

        // Select a contact from dropdown
        function selectContact(contact) {
            contactNameInput.value = contact.name;
            contactPhoneInput.value = contact.phone;

            // Optionally fill supplier/haulier if they're empty
            if (!supplierInput.value && contact.supplier) {
                supplierInput.value = contact.supplier;
            }
            if (!haulierInput.value && contact.haulier) {
                haulierInput.value = contact.haulier;
            }

            contactDropdown.classList.add('hidden');
            contactStatus.textContent = '✓';
            contactStatus.className = 'text-xs text-green-600';
        }

        // Lookup phone when contact name loses focus
        contactNameInput.addEventListener('blur', function() {
            setTimeout(() => {
                if (this.value.trim() && !contactPhoneInput.value) {
                    lookupContactPhone(this.value.trim());
                }
                contactDropdown.classList.add('hidden');
            }, 200);
        });

        // Lookup contact phone by name
        function lookupContactPhone(name) {
            const depot_id = slotSelect?.value ? getDepotFromSlot(slotSelect.value) : null;
            const supplier = supplierInput?.value || '';
            const haulier = haulierInput?.value || '';

            const params = new URLSearchParams({
                name: name,
                ...(depot_id && { depot_id }),
                ...(supplier && { supplier }),
                ...(haulier && { haulier })
            });

            fetch(`{{ route('api.contacts.phone') }}?${params}`)
                .then(response => response.json())
                .then(data => {
                    if (data.phone) {
                        contactPhoneInput.value = data.phone;
                        contactStatus.textContent = '✓';
                        contactStatus.className = 'text-xs text-green-600';
                        contactStatus.title = 'Phone number found';
                    }
                })
                .catch(error => {
                    console.error('Phone lookup failed:', error);
                });
        }

        // Helper function to extract depot ID from slot select
        function getDepotFromSlot(slotId) {
            // This would need to be implemented based on your slot data structure
            // For now, we'll return null and rely on supplier/haulier filtering
            return null;
        }

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!contactNameInput.contains(e.target) && !contactDropdown.contains(e.target)) {
                contactDropdown.classList.add('hidden');
            }
        });
    }
});
</script>