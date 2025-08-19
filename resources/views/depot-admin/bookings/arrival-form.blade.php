<x-app-layout>
    @include('layouts.admin-nav')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-800">
            🚛 Vehicle Arrival - {{ $booking->booking_reference }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            
            <!-- Booking Summary -->
            <div class="px-6 py-4 border-b bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Customer:</strong> {{ $booking->customer->name ?? 'N/A' }}<br>
                        <strong>Booking Type:</strong> {{ $booking->bookingType->name ?? 'N/A' }}
                    </div>
                    <div>
                        <strong>Depot:</strong> {{ $booking->slot->depot->name }}<br>
                        <strong>Scheduled:</strong> {{ $booking->slot->start_at->format('d-M-Y H:i') }}
                    </div>
                    <div>
                        <strong>Expected:</strong> {{ $booking->total_expected_cases ?? 0 }} cases, {{ $booking->total_expected_pallets ?? 0 }} pallets<br>
                        @if($booking->estimated_arrival)
                            <strong>Est. Arrival:</strong> {{ $booking->estimated_arrival->format('d-M-Y H:i') }}
                        @endif
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('depot.bookings.arrival', $booking) }}" class="p-6">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-800 mb-2">❌ Please fix the following errors:</h4>
                        <ul class="list-disc list-inside text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-green-800">✅ {{ session('success') }}</p>
                    </div>
                @endif
                
                <h3 class="text-lg font-medium text-gray-900 mb-6">🚛 Vehicle Arrival Details</h3>
                
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Required Vehicle Registration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('vehicle_registration')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                    </div>

                    <!-- Container/Trailer Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Container/Trailer Number
                        </label>
                        <input type="text" 
                               id="container-number-input"
                               name="container_number"
                               value="{{ old('container_number', $booking->container_number) }}"
                               placeholder="e.g., CONT123456 or TR123456"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('container_number')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
                    </div>

                    <!-- Transport Details -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Carrier Company <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="depot-carrier-search" 
                                   name="carrier_name"
                                   value="{{ old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company) }}"
                                   placeholder="Search or type carrier name..."
                                   required
                                   autocomplete="off"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 pr-10">
                            
                            {{-- Hidden carrier_id field --}}
                            <input type="hidden" 
                                   id="depot-carrier-id" 
                                   name="carrier_id" 
                                   value="{{ old('carrier_id', $booking->carrier_id) }}">
                            
                            {{-- Search dropdown --}}
                            <div id="depot-carrier-dropdown" 
                                 class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                              {{-- Results will be populated by JavaScript --}}
                            </div>
                            
                            {{-- Status indicators --}}
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                              <span id="depot-carrier-status" class="text-xs"></span>
                            </div>
                        </div>
                        @error('carrier_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        @error('carrier_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
                    </div>


                    <!-- Tipping Location Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🚛 Tipping Drop Location</label>
                        <select name="tipping_location_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">– Assign Drop Location –</option>
                            @if(isset($tippingLocations))
                                @foreach($tippingLocations as $location)
                                    <option value="{{ $location->id }}" 
                                            @selected(old('tipping_location_id', $booking->tipping_location_id) == $location->id)>
                                        {{ $location->name }} ({{ $location->getAvailableCapacity() }}/{{ $location->capacity }} available)
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('tipping_location_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle to drop zone</p>
                    </div>


                    <!-- Tipping Bay Assignment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🏗️ Tipping Bay (Direct Assignment)</label>
                        <select name="tipping_bay_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <option value="">– Skip to bay directly –</option>
                            @if(isset($tippingBays))
                                @foreach($tippingBays as $bay)
                                    <option value="{{ $bay->id }}" 
                                            @selected(old('tipping_bay_id', $booking->tipping_bay_id) == $bay->id)
                                            @disabled($bay->is_occupied)>
                                        {{ $bay->name }} ({{ $bay->depot->name }}) 
                                        @if($bay->is_occupied)
                                            - Occupied
                                        @else
                                            - Available
                                        @endif
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('tipping_bay_id')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional: Skip drop zone and go straight to bay</p>
                    </div>

                </div>

                @if($booking->special_instructions)
                    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p class="text-yellow-700">{{ $booking->special_instructions }}</p>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('depot.bookings.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        ✅ Mark as Arrived
                    </button>
                </div>
            </form>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Depot carrier search functionality (adapted from admin form)
    const searchInput = document.getElementById('depot-carrier-search');
    const carrierIdInput = document.getElementById('depot-carrier-id');
    const dropdown = document.getElementById('depot-carrier-dropdown');
    const statusSpan = document.getElementById('depot-carrier-status');
    
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
    
    // Input normalization functions
    function capitalizeWords(str) {
        return str.replace(/\b\w+/g, function(word) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        });
    }
    
    function toUpperCase(str) {
        return str.toUpperCase();
    }
    
    // Carrier name capitalization
    searchInput.addEventListener('blur', function() {
        if (this.value.trim()) {
            this.value = capitalizeWords(this.value.trim());
        }
    });
    
    // Container/trailer number uppercase
    const containerInput = document.getElementById('container-number-input');
    if (containerInput) {
        console.log('Container input found, adding uppercase listener');
        containerInput.addEventListener('input', function() {
            const cursorPos = this.selectionStart;
            const oldValue = this.value;
            this.value = toUpperCase(this.value);
            console.log('Container input changed:', oldValue, '->', this.value);
            this.setSelectionRange(cursorPos, cursorPos);
        });
    } else {
        console.log('Container input NOT found');
    }
});
</script>
</x-app-layout>