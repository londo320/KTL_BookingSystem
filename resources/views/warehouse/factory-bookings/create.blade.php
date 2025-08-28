<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Register Factory Delivery</h2>
        <p class="text-sm text-gray-600 mt-1">Quick registration for ad-hoc arrivals at the gate</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('app.factory-bookings.index') }}"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          ← Back to Factory Bookings
        </a>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-4xl mx-auto">
    @if($errors->any())
      <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="text-red-800 font-medium mb-2">Please correct the following errors:</h3>
        <ul class="text-red-700 text-sm space-y-1">
          @foreach($errors->all() as $error)
            <li>• {{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <form method="POST" action="{{ route('app.factory-bookings.store') }}" class="space-y-6">
      @csrf
      
      <div class="space-y-6">
        {{-- REQUIRED FIELDS --}}
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
          <h3 class="text-lg font-medium text-blue-900 mb-3">📋 Required Information</h3>
          
          <div class="grid grid-cols-2 gap-4">
            {{-- Depot Selection --}}
            <div class="col-span-2">
              <label class="block text-sm font-medium text-blue-800">Depot <span class="text-red-500">*</span></label>
              <select name="depot_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
                <option value="">– Choose depot –</option>
                @foreach($depots as $depot)
                  <option value="{{ $depot->id }}"
                    @selected(old('depot_id') == $depot->id)
                  >
                    {{ $depot->name }}
                  </option>
                @endforeach
              </select>
              @if($depots->count() === 1)
                <p class="text-xs text-blue-600 mt-1">Auto-selected based on your access</p>
              @endif
              @error('depot_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            {{-- Customer --}}
            <div class="col-span-2">
              <label class="block text-sm font-medium text-blue-800">Customer <span class="text-red-500">*</span></label>
              <select name="customer_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
                <option value="">– Choose customer –</option>
                @foreach($customers as $customer)
                  <option value="{{ $customer->id }}"
                    @selected(old('customer_id') == $customer->id)
                  >
                    {{ $customer->name }}
                  </option>
                @endforeach
              </select>
              @error('customer_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            {{-- Trailer Type --}}
            <div>
              <label class="block text-sm font-medium text-blue-800">Trailer Type <span class="text-red-500">*</span></label>
              <select name="trailer_type_id" required class="mt-1 block w-full border-blue-300 rounded bg-white">
                <option value="">– Choose type –</option>
                @foreach($trailerTypes as $trailerType)
                  <option value="{{ $trailerType->id }}"
                    @selected(old('trailer_type_id') == $trailerType->id)
                  >
                    {{ $trailerType->name }}
                  </option>
                @endforeach
              </select>
              @error('trailer_type_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            {{-- Carrier Company --}}
            <div>
              <label class="block text-sm font-medium text-blue-800">Carrier Company <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="text" 
                       id="admin-carrier-search" 
                       name="carrier_name"
                       value="{{ old('carrier_name') }}"
                       placeholder="Search or type carrier name..."
                       required
                       autocomplete="off"
                       class="mt-1 block w-full border-blue-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10">
                
                {{-- Hidden carrier_id field --}}
                <input type="hidden" 
                       id="admin-carrier-id" 
                       name="carrier_id" 
                       value="{{ old('carrier_id') }}">
                
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

            {{-- Priority --}}
            <div>
              <label class="block text-sm font-medium text-blue-800">Priority (0-100)</label>
              <input type="number" name="priority" min="0" max="100" 
                     value="{{ old('priority', 50) }}"
                     placeholder="50"
                     class="mt-1 block w-full border-blue-300 rounded bg-white">
              <p class="text-xs text-blue-600 mt-1">Higher = more urgent</p>
              @error('priority')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
            </div>

            {{-- Tipping Type --}}
            <div>
              <label class="block text-sm font-medium text-blue-800">🚛 Tipping Type <span class="text-red-500">*</span></label>
              <div class="mt-2 space-y-2">
                <div class="flex items-center">
                  <input type="radio" id="tipping_type_live" name="tipping_type" value="live_tip" 
                         @checked(old('tipping_type') == 'live_tip')
                         class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" required>
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
                         @checked(old('tipping_type') == 'drop')
                         class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300" required>
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
          </div>
        </div>

        {{-- PO NUMBERS SECTION --}}
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
          <h3 class="text-lg font-medium text-green-900 mb-3">📦 PO Numbers & Expected Quantities</h3>
          <p class="text-sm text-green-700 mb-3">At least one PO with expected quantities is required</p>
          <div id="po-container" class="space-y-4">
            {{-- Initial PO Number --}}
            <div class="po-group bg-white p-4 rounded border">
              <div class="flex justify-between items-center mb-3">
                <label class="block text-sm font-medium text-gray-700">PO Number <span class="text-red-500">*</span></label>
                <button type="button" onclick="removePo(this)" class="text-red-600 hover:text-red-800 text-sm hidden">Remove PO</button>
              </div>
              <input type="text" name="po_numbers[0][po_number]" required
                     placeholder="Enter PO number..."
                     class="w-full border-gray-300 rounded mb-3">
              
              <div class="lines-container">
                <div class="line-group mb-3">
                  <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-medium text-gray-600">Line 1</label>
                    <button type="button" onclick="removeLine(this)" class="text-red-600 hover:text-red-800 text-xs hidden">Remove Line</button>
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Expected Cases <span class="text-red-500">*</span></label>
                      <input type="number" name="po_numbers[0][lines][0][expected_cases]" min="0" required
                             class="w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-600 mb-1">Expected Pallets <span class="text-red-500">*</span></label>
                      <input type="number" name="po_numbers[0][lines][0][expected_pallets]" min="0" required
                             class="w-full border-gray-300 rounded text-sm">
                    </div>
                  </div>
                  <input type="hidden" name="po_numbers[0][lines][0][line_number]" value="1">
                </div>
              </div>
              
              <button type="button" onclick="addLine(this)" class="text-blue-600 hover:text-blue-800 text-sm">+ Add Line</button>
            </div>
          </div>
          
          <button type="button" onclick="addPo()" class="mt-4 text-blue-600 hover:text-blue-800 text-sm">+ Add Another PO</button>
        </div>

        {{-- OPTIONAL TRANSPORTATION DETAILS --}}
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
          <h3 class="text-lg font-medium text-gray-700 mb-3">🚛 Transportation Details <span class="text-sm font-normal text-gray-500">(Optional - can be added later)</span></h3>
          
          <div class="grid grid-cols-2 gap-4">
            {{-- Vehicle Registration --}}
            <div>
              <label class="block text-sm font-medium text-gray-600">Vehicle Registration <span class="text-red-500">*</span></label>
              <input type="text" name="vehicle_registration" required
                     value="{{ old('vehicle_registration') }}"
                     placeholder="e.g., AB12 CDE"
                     class="mt-1 block w-full border-gray-300 rounded-lg uppercase">
              @error('vehicle_registration')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
            </div>

            {{-- Container Number --}}
            <div>
              <label class="block text-sm font-medium text-gray-600">Vehicle/Trailer Number</label>
              <input type="text" name="trailer_registration"
                     value="{{ old('trailer_registration') }}"
                     placeholder="e.g., TR12 345"
                     class="mt-1 block w-full border-gray-300 rounded-lg uppercase">
              @error('trailer_registration')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>

        {{-- NOTES & INSTRUCTIONS --}}
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
          <h3 class="text-lg font-medium text-yellow-900 mb-3">📝 Notes & Instructions</h3>
          
          <div class="space-y-4">
            {{-- General Notes --}}
            <div>
              <label class="block text-sm font-medium text-yellow-800">General Notes</label>
              <textarea name="delivery_notes" rows="2"
                        placeholder="Internal notes about this booking..."
                        class="mt-1 block w-full border-yellow-300 rounded bg-white">{{ old('delivery_notes') }}</textarea>
              @error('delivery_notes')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
            </div>

            {{-- Special Instructions --}}
            <div>
              <label class="block text-sm font-medium text-yellow-800">Special Instructions</label>
              <textarea name="gate_notes" rows="2"
                        placeholder="Special handling instructions for the driver/operator..."
                        class="mt-1 block w-full border-yellow-300 rounded bg-white">{{ old('gate_notes') }}</textarea>
              @error('gate_notes')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
            </div>
          </div>
        </div>
      </div>
      {{-- Important Information Box --}}
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
      {{-- Action Buttons --}}
      <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <a href="{{ route('app.factory-bookings.index') }}" 
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
      
      // Auto-select depot if only one available
      const depotSelect = document.querySelector('select[name="depot_id"]');
      if (depotSelect && depotSelect.options.length === 2) { // Only "Select Depot" + one depot
        depotSelect.selectedIndex = 1;
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
      
      // Search carriers function (same as in edit form)
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
      
      // Populate dropdown with results (same as in edit form)
      function populateDropdown(data, query) {
          dropdown.innerHTML = '';
          
          if (data.total > data.carriers.length) {
              const headerItem = document.createElement('div');
              headerItem.className = 'px-3 py-2 bg-gray-100 border-b border-gray-200 text-xs text-gray-600';
              headerItem.innerHTML = `Showing ${data.carriers.length} of ${data.total} carriers`;
              dropdown.appendChild(headerItem);
          }
          
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
                  selectCarrier(data.carrier.id, data.carrier.name);
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
      
      // Initial status update
      updateStatus();
    });

    // PO Management Functions
    let poCounter = 1;
    let lineCounters = {0: 1};

    function addPo() {
        const container = document.getElementById('po-container');
        const poDiv = document.createElement('div');
        poDiv.className = 'po-group bg-white p-4 rounded border';
        poDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <label class="block text-sm font-medium text-gray-700">PO Number <span class="text-red-500">*</span></label>
                <button type="button" onclick="removePo(this)" class="text-red-600 hover:text-red-800 text-sm">Remove PO</button>
            </div>
            <input type="text" name="po_numbers[${poCounter}][po_number]" required
                   placeholder="Enter PO number..."
                   class="w-full border-gray-300 rounded mb-3">
            
            <div class="lines-container">
                <div class="line-group mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-sm font-medium text-gray-600">Line 1</label>
                        <button type="button" onclick="removeLine(this)" class="text-red-600 hover:text-red-800 text-xs hidden">Remove Line</button>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Expected Cases <span class="text-red-500">*</span></label>
                            <input type="number" name="po_numbers[${poCounter}][lines][0][expected_cases]" min="0" required
                                   class="w-full border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Expected Pallets <span class="text-red-500">*</span></label>
                            <input type="number" name="po_numbers[${poCounter}][lines][0][expected_pallets]" min="0" required
                                   class="w-full border-gray-300 rounded text-sm">
                        </div>
                    </div>
                    <input type="hidden" name="po_numbers[${poCounter}][lines][0][line_number]" value="1">
                </div>
            </div>
            
            <button type="button" onclick="addLine(this)" class="text-blue-600 hover:text-blue-800 text-sm">+ Add Line</button>
        `;
        
        container.appendChild(poDiv);
        lineCounters[poCounter] = 1;
        poCounter++;
        updateRemoveButtons();
    }

    function removePo(button) {
        button.closest('.po-group').remove();
        updateRemoveButtons();
    }

    function addLine(button) {
        const poGroup = button.closest('.po-group');
        const linesContainer = poGroup.querySelector('.lines-container');
        const poIndex = Array.from(poGroup.parentNode.children).indexOf(poGroup);
        const lineIndex = lineCounters[poIndex] || 0;
        
        const lineDiv = document.createElement('div');
        lineDiv.className = 'line-group mb-3';
        lineDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <label class="text-sm font-medium text-gray-600">Line ${lineIndex + 1}</label>
                <button type="button" onclick="removeLine(this)" class="text-red-600 hover:text-red-800 text-xs">Remove Line</button>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Expected Cases <span class="text-red-500">*</span></label>
                    <input type="number" name="po_numbers[${poIndex}][lines][${lineIndex}][expected_cases]" min="0" required
                           class="w-full border-gray-300 rounded text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Expected Pallets <span class="text-red-500">*</span></label>
                    <input type="number" name="po_numbers[${poIndex}][lines][${lineIndex}][expected_pallets]" min="0" required
                           class="w-full border-gray-300 rounded text-sm">
                </div>
            </div>
            <input type="hidden" name="po_numbers[${poIndex}][lines][${lineIndex}][line_number]" value="${lineIndex + 1}">
        `;
        
        linesContainer.appendChild(lineDiv);
        lineCounters[poIndex] = (lineCounters[poIndex] || 0) + 1;
        updateLineRemoveButtons(poGroup);
    }

    function removeLine(button) {
        const poGroup = button.closest('.po-group');
        button.closest('.line-group').remove();
        updateLineRemoveButtons(poGroup);
        
        // Re-number remaining lines
        const lines = poGroup.querySelectorAll('.line-group');
        lines.forEach((line, index) => {
            line.querySelector('label').textContent = `Line ${index + 1}`;
        });
    }

    function updateRemoveButtons() {
        const poGroups = document.querySelectorAll('.po-group');
        poGroups.forEach((group, index) => {
            const removeButton = group.querySelector('button[onclick="removePo(this)"]');
            if (poGroups.length > 1) {
                removeButton.classList.remove('hidden');
            } else {
                removeButton.classList.add('hidden');
            }
        });
    }

    function updateLineRemoveButtons(poGroup) {
        const lines = poGroup.querySelectorAll('.line-group');
        lines.forEach((line, index) => {
            const removeButton = line.querySelector('button[onclick="removeLine(this)"]');
            if (lines.length > 1) {
                removeButton.classList.remove('hidden');
            } else {
                removeButton.classList.add('hidden');
            }
        });
    }
  </script>
</x-warehouse-layout>