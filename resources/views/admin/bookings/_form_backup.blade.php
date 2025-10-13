{{-- FULL-WIDTH GRID LAYOUT --}}
<div class="space-y-4">
  {{-- TOP ROW: 4 Column Layout --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    {{-- Customer --}}
    @if(auth()->user()->hasRole('admin') || auth()->user()->hasFunction('customers.view') || auth()->user()->hasFunction('bookings.create') || request()->routeIs('app.*'))
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
        <select name="customer_id" required class="block w-full border-gray-300 rounded bg-white text-sm py-2">
          <option value="">– Choose customer –</option>
          @foreach($customers as $customer)
            <option value="{{ $customer->id }}"
              @selected(old('customer_id', $booking->customer_id) == $customer->id)
            >
              {{ $customer->name }}
            </option>
          @endforeach
        </select>
        @error('customer_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>
    @endif

    {{-- Booking Type --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type <span class="text-red-500">*</span></label>
      <select name="booking_type_id" required class="block w-full border-gray-300 rounded bg-white text-sm py-2">
        <option value="">– Choose type –</option>
        @foreach($types as $type)
          <option value="{{ $type->id }}"
            @selected(old('booking_type_id', $booking->booking_type_id) == $type->id)
          >
            {{ $type->name }}
          </option>
        @endforeach
      </select>
      @error('booking_type_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
    </div>

    {{-- Slot --}}
    <div class="xl:col-span-2">
      <label class="block text-sm font-medium text-gray-700 mb-1">Slot <span class="text-red-500">*</span>
        @if($booking->exists)
          <span class="text-xs text-red-600 ml-1">⚠️ Use Rebook</span>
        @endif
      </label>
      <select name="slot_id" required @if($booking->exists) disabled @endif class="block w-full border-gray-300 rounded text-sm py-2 @if($booking->exists) bg-gray-100 text-gray-500 cursor-not-allowed @else bg-white @endif">
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

      @error('slot_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
    </div>
  </div>

  {{-- SECOND ROW: Supplier, Contact, Haulier, Trailer --}}
  <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    {{-- Supplier --}}
        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Supplier <span class="text-red-500">*</span></label>
          <div class="relative">
            <input type="text"
                   id="admin-supplier-search"
                   name="supplier_name"
                   value="{{ old('supplier_name', $booking->supplier?->name ?? $booking->supplier) }}"
                   placeholder="Search or type supplier..."
                   required
                   autocomplete="off"
                   class="block w-full border-blue-300 rounded bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10 text-sm py-2">

            {{-- Hidden supplier_id field --}}
            <input type="hidden"
                   id="admin-supplier-id"
                   name="supplier_id"
                   value="{{ old('supplier_id', $booking->supplier_id) }}">

            {{-- Search dropdown --}}
            <div id="admin-supplier-dropdown"
                 class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
              {{-- Results will be populated by JavaScript --}}
            </div>

            {{-- Status indicators --}}
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
              <span id="admin-supplier-status" class="text-xs"></span>
            </div>
          </div>

          <div class="mt-1">
            <a href="{{ route('app.suppliers.index') }}" target="_blank"
               class="text-[10px] text-blue-600 hover:text-blue-800 underline">
              📦 Manage suppliers
            </a>
          </div>

          @error('supplier_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
          @error('supplier_name')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Contact Name with autocomplete --}}
        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Contact Name</label>
          <div class="relative">
            <input type="text"
                   id="admin-contact-name-input"
                   name="contact_name"
                   value="{{ old('contact_name', $booking->contact_name) }}"
                   placeholder="Search or type contact..."
                   autocomplete="off"
                   class="block w-full border-blue-300 rounded pr-10 text-sm py-2">

            {{-- Search dropdown --}}
            <div id="admin-contact-dropdown"
                 class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
              {{-- Results will be populated by JavaScript --}}
            </div>

            {{-- Status indicator --}}
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
              <span id="admin-contact-status" class="text-xs"></span>
            </div>
          </div>
          @error('contact_name')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Contact Phone --}}
        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Contact Phone</label>
          <input type="text"
                 id="admin-contact-phone-input"
                 name="contact_phone"
                 value="{{ old('contact_phone', $booking->contact_phone) }}"
                 placeholder="e.g., 07123456789"
                 class="block w-full border-blue-300 rounded text-sm py-2">
          @error('contact_phone')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>
      </div>
    </div>

    {{-- RIGHT: TRANSPORTATION DETAILS --}}
    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
      <h3 class="text-base font-semibold text-gray-700 mb-3">🚛 Transportation <span class="text-sm font-normal text-gray-500">(Optional)</span></h3>

      <div class="space-y-3">
        {{-- Haulier --}}
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Haulier</label>
          <div class="relative">
            <input type="text"
                   id="admin-carrier-search"
                   name="carrier_name"
                   value="{{ old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company) }}"
                   placeholder="Search or type haulier..."
                   autocomplete="off"
                   class="block w-full border-gray-300 rounded bg-white focus:ring-2 focus:ring-gray-500 focus:border-gray-500 pr-10 text-sm py-2">

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
            <div class="absolute inset-y-0 right-0 flex items-center pr-2">
              <span id="admin-carrier-status" class="text-xs"></span>
            </div>
          </div>

          <div class="mt-1">
            <a href="{{ route('app.carriers.create') }}" target="_blank"
               class="text-[10px] text-gray-600 hover:text-gray-800 underline">
              🚚 Manage hauliers
            </a>
          </div>

          @error('carrier_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
          @error('carrier_name')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Trailer Type --}}
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Trailer Type</label>
          <select name="trailer_type_id" class="block w-full border-gray-300 rounded text-sm py-2">
            <option value="">– Select –</option>
            @foreach($trailerTypes as $trailerType)
              <option value="{{ $trailerType->id }}"
                      @selected(old('trailer_type_id', $booking->trailer_type_id) == $trailerType->id)>
                {{ $trailerType->name }}
              </option>
            @endforeach
          </select>
          @error('trailer_type_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Vehicle Registration --}}
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Vehicle Registration</label>
          <input type="text" name="vehicle_registration"
                 value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
                 placeholder="e.g., AB12 CDE"
                 class="block w-full border-gray-300 rounded text-sm py-2">
          @error('vehicle_registration')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Container Number --}}
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Container/Trailer Number</label>
          <input type="text" name="container_number"
                 value="{{ old('container_number', $booking->container_number) }}"
                 placeholder="e.g., CONT123456"
                 class="block w-full border-gray-300 rounded text-sm py-2">
          @error('container_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Seal Number --}}
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Seal Number</label>
          <input type="text" name="seal_number"
                 value="{{ old('seal_number', $booking->seal_number) }}"
                 placeholder="e.g., SEAL123456"
                 class="block w-full border-gray-300 rounded text-sm py-2">
          @error('seal_number')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Tipping Type --}}
        <div>
          <label class="block text-sm font-medium text-gray-600 mb-1">Tipping Type</label>
          <div class="space-y-2">
            <div class="flex items-center">
              <input type="radio" id="tipping_type_live" name="tipping_type" value="live_tip"
                     @checked(old('tipping_type', $booking->tipping_type) == 'live_tip')
                     class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
              <label for="tipping_type_live" class="ml-2 flex items-center">
                <span class="text-base mr-2">🚛📦</span>
                <div>
                  <div class="text-sm font-medium text-gray-900">Live Tip</div>
                </div>
              </label>
            </div>
            <div class="flex items-center">
              <input type="radio" id="tipping_type_drop" name="tipping_type" value="drop"
                     @checked(old('tipping_type', $booking->tipping_type) == 'drop')
                     class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
              <label for="tipping_type_drop" class="ml-2 flex items-center">
                <span class="text-base mr-2">📦</span>
                <div>
                  <div class="text-sm font-medium text-gray-900">Drop</div>
                </div>
              </label>
            </div>
          </div>
          @error('tipping_type')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        </div>

        {{-- Tipping Bay (only show for existing bookings) --}}
        @if($booking->exists)
          <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Tipping Bay</label>
            <select name="tipping_bay_id" class="block w-full border-gray-300 rounded text-sm py-2">
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
                      - Current
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
  </div>

  {{-- PO NUMBERS SECTION - FULL WIDTH --}}
  <div id="po-numbers-section" class="bg-green-50 p-4 rounded-lg border border-green-200" style="display: {{ old('customer_id', $booking->customer_id) ? 'block' : 'none' }};">
    <div class="flex justify-between items-center mb-3">
      <h3 class="text-base font-semibold text-green-900">📦 PO Numbers & Expected Quantities</h3>
      <a href="{{ route('app.products.index') }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
        📦 Manage Products
      </a>
    </div>
    <x-booking-po-numbers :booking="$booking" :hide_actuals="!$booking->exists" :customer_id="old('customer_id', $booking->customer_id)" />
  </div>

  {{-- Customer Selection Notice --}}
  <div id="customer-selection-notice" class="bg-yellow-50 p-4 rounded-lg border border-yellow-200" style="display: {{ old('customer_id', $booking->customer_id) ? 'none' : 'block' }};">
    <h3 class="text-base font-semibold text-yellow-800 mb-2">📦 PO Numbers & Expected Quantities</h3>
    <p class="text-sm text-yellow-700">Please select a customer first to add PO numbers and products.</p>
  </div>

  {{-- BOTTOM ROW: Notes --}}
  <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
    <h3 class="text-base font-semibold text-yellow-900 mb-3">📝 Notes & Instructions</h3>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- General Notes --}}
      <div>
        <label class="block text-sm font-medium text-yellow-800 mb-1">General Notes</label>
        <textarea name="notes" rows="3"
                  placeholder="Internal notes about this booking..."
                  class="block w-full border-yellow-300 rounded bg-white text-sm py-2">{{ old('notes', $booking->notes) }}</textarea>
        @error('notes')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>

      {{-- Special Instructions --}}
      <div>
        <label class="block text-sm font-medium text-yellow-800 mb-1">Special Instructions</label>
        <textarea name="special_instructions" rows="3"
                  placeholder="Special handling instructions..."
                  class="block w-full border-yellow-300 rounded bg-white text-sm py-2">{{ old('special_instructions', $booking->special_instructions) }}</textarea>
        @error('special_instructions')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
      </div>
    </div>
  </div>

  {{-- ARRIVAL STATUS (if arrived) --}}
  @if($booking->exists && $booking->arrived_at)
    <div class="bg-green-100 p-4 rounded-lg border border-green-300">
      <h3 class="text-base font-semibold text-green-900 mb-2">✅ Arrival Status</h3>
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
    // Supplier Search & Autocomplete
    // ============================================
    const supplierSearchInput = document.getElementById('admin-supplier-search');
    const supplierIdInput = document.getElementById('admin-supplier-id');
    const supplierDropdown = document.getElementById('admin-supplier-dropdown');
    const supplierStatus = document.getElementById('admin-supplier-status');

    let selectedSupplierId = supplierIdInput.value || null;
    let supplierSearchTimeout;
    let supplierCurrentPage = 1;

    function updateSupplierStatus() {
        if (supplierSearchInput.value && selectedSupplierId) {
            supplierStatus.textContent = '✓';
            supplierStatus.classList.add('text-green-600');
            supplierStatus.classList.remove('text-yellow-600');
        } else if (supplierSearchInput.value) {
            supplierStatus.textContent = '⚠';
            supplierStatus.classList.add('text-yellow-600');
            supplierStatus.classList.remove('text-green-600');
        } else {
            supplierStatus.textContent = '';
            supplierStatus.classList.remove('text-green-600', 'text-yellow-600');
        }
    }

    function searchSuppliers(query, page = 1) {
        if (query.length < 2) {
            supplierDropdown.classList.add('hidden');
            return;
        }

        fetch(`{{ route('api.suppliers.search') }}?q=${encodeURIComponent(query)}&page=${page}`)
            .then(response => response.json())
            .then(data => {
                populateSupplierDropdown(data.suppliers, data.has_more, data.exact_match, query);
            })
            .catch(error => {
                console.error('Error searching suppliers:', error);
            });
    }

    function populateSupplierDropdown(suppliers, hasMore, exactMatch, query) {
        supplierDropdown.innerHTML = '';

        if (suppliers.length === 0) {
            const noResults = document.createElement('div');
            noResults.className = 'p-3 text-sm text-gray-500 text-center';
            noResults.textContent = 'No suppliers found';
            supplierDropdown.appendChild(noResults);
        } else {
            suppliers.forEach(supplier => {
                const item = document.createElement('div');
                item.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200';

                let statusBadge = '';
                if (!supplier.is_active) {
                    statusBadge = '<span class="text-xs text-yellow-600 ml-2">(inactive)</span>';
                }

                item.innerHTML = `
                    <div class="font-medium">${supplier.name}${statusBadge}</div>
                `;
                item.onclick = () => selectSupplier(supplier.id, supplier.name);
                supplierDropdown.appendChild(item);
            });
        }

        // Add "Create new" option if no exact match
        if (!exactMatch && query.length >= 2) {
            const createItem = document.createElement('div');
            createItem.className = 'p-3 bg-green-50 hover:bg-green-100 cursor-pointer border-t-2 border-green-200';
            createItem.innerHTML = `
                <div class="font-medium text-green-800">➕ Create "${query}"</div>
                <div class="text-xs text-green-600">Click to add new supplier</div>
            `;
            createItem.onclick = () => quickCreateSupplier(query);
            supplierDropdown.appendChild(createItem);
        }

        supplierDropdown.classList.remove('hidden');
    }

    function selectSupplier(id, name) {
        selectedSupplierId = id;
        supplierIdInput.value = id;
        supplierSearchInput.value = name;
        supplierDropdown.classList.add('hidden');
        updateSupplierStatus();
    }

    function quickCreateSupplier(name) {
        const createButton = supplierDropdown.querySelector('[onclick*="quickCreateSupplier"]');
        if (createButton) {
            createButton.innerHTML = `
                <div class="font-medium text-green-800">⏳ Creating "${name}"...</div>
                <div class="text-xs text-green-600">Please wait...</div>
            `;
        }

        fetch('{{ route('api.suppliers.quick-create') }}', {
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
                selectSupplier(data.supplier.id, data.supplier.name);
            } else {
                alert('Error creating supplier');
                supplierDropdown.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error creating supplier:', error);
            alert('Error creating supplier');
            supplierDropdown.classList.add('hidden');
        });
    }

    supplierSearchInput.addEventListener('input', function() {
        const query = this.value.trim();

        selectedSupplierId = null;
        supplierIdInput.value = '';
        supplierCurrentPage = 1;

        clearTimeout(supplierSearchTimeout);
        supplierSearchTimeout = setTimeout(() => {
            searchSuppliers(query, 1);
        }, 300);

        updateSupplierStatus();
    });

    document.addEventListener('click', function(e) {
        if (!supplierSearchInput.contains(e.target) && !supplierDropdown.contains(e.target)) {
            supplierDropdown.classList.add('hidden');
        }
    });

    supplierSearchInput.addEventListener('focus', function() {
        if (this.value.length >= 2) {
            searchSuppliers(this.value);
        }
    });

    updateSupplierStatus();

    // ============================================
    // Contact Name Autocomplete with Phone Lookup
    // ============================================
    const contactNameInput = document.getElementById('admin-contact-name-input');
    const contactPhoneInput = document.getElementById('admin-contact-phone-input');
    const contactDropdown = document.getElementById('admin-contact-dropdown');
    const contactStatus = document.getElementById('admin-contact-status');
    const contactSupplierInput = document.getElementById('admin-supplier-search');
    const haulierInput = document.getElementById('admin-carrier-search'); // Carrier field is now Haulier
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
            if (!contactSupplierInput.value && contact.supplier) {
                contactSupplierInput.value = contact.supplier;
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

    // Toggle PO Numbers section visibility based on customer selection
    const customerSelect = document.querySelector('select[name="customer_id"]');
    const poNumbersSection = document.getElementById('po-numbers-section');
    const customerNotice = document.getElementById('customer-selection-notice');

    if (customerSelect && poNumbersSection && customerNotice) {
        customerSelect.addEventListener('change', function() {
            if (this.value) {
                poNumbersSection.style.display = 'block';
                customerNotice.style.display = 'none';
            } else {
                poNumbersSection.style.display = 'none';
                customerNotice.style.display = 'block';
            }
        });
    }
});
</script>
