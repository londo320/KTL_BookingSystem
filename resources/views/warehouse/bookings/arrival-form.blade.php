<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Company Logo -->
                <div class="flex-shrink-0">
                    <img src="{{ asset('images/ktl_logo.svg') }}" 
                         alt="Knowles Logistics Logo" 
                         class="h-10 w-auto object-contain"
                         onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='block';">
                    <div id="fallback-logo" class="hidden w-10 h-10 bg-blue-600 rounded flex items-center justify-center">
                        <span class="text-white font-bold text-xs">KL</span>
                    </div>
                </div>
                <!-- Header Text -->
                <div>
                    <h2 class="font-semibold text-xl text-gray-800
                        🚛 Vehicle Arrival - {{ $booking->booking_reference }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $booking->slot->depot->name }} • {{ $booking->slot->start_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
            <!-- Quick Info Badge -->
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    {{ $booking->customer->name ?? 'Walk-in' }}
                </span>
                @if($booking->bookingType)
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                        {{ $booking->bookingType->name }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>
    <div class="py-12">
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
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 p-6">
                    <!-- Main Form Column -->
                    <div class="lg:col-span-2">
                        <form method="POST" action="{{ route('app.bookings.arrival', $booking) }}">
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
                                   id="admin-container-number-input"
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
                                       id="arrival-carrier-search" 
                                       name="carrier_name"
                                       value="{{ old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company) }}"
                                       placeholder="Search or type carrier name..."
                                       required
                                       autocomplete="off"
                                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 pr-10">
                                {{-- Hidden carrier_id field --}}
                                <input type="hidden" 
                                       id="arrival-carrier-id" 
                                       name="carrier_id" 
                                       value="{{ old('carrier_id', $booking->carrier_id) }}">
                                {{-- Search dropdown --}}
                                <div id="arrival-carrier-dropdown" 
                                     class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                  {{-- Results will be populated by JavaScript --}}
                                </div>
                                {{-- Status indicators --}}
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                  <span id="arrival-carrier-status" class="text-xs"></span>
                                </div>
                            </div>
                            @error('carrier_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            @error('carrier_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
                        </div>
                        <!-- Trailer Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🚛 Trailer Type <span class="text-red-500">*</span></label>
                            <select name="trailer_type_id" required class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="">– Select Trailer Type –</option>
                                @if(isset($trailerTypes))
                                    @foreach($trailerTypes as $trailerType)
                                        <option value="{{ $trailerType->id }}" 
                                                @selected(old('trailer_type_id', $booking->trailer_type_id) == $trailerType->id)>
                                            {{ $trailerType->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('trailer_type_id')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Required: Type and size of trailer/container</p>
                        </div>
                        <!-- Tipping Type Selection -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">🚛 Tipping Type <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="tipping-type-option flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 hover:shadow-md transition-all duration-200" data-value="live_tip">
                                    <input type="radio" name="tipping_type" value="live_tip" 
                                           @checked(old('tipping_type', $booking->tipping_type) == 'live_tip')
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
                                           @checked(old('tipping_type', $booking->tipping_type) == 'drop')
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
                            @error('tipping_type')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Required: How will this booking be handled during tipping?</p>
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
                            <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle directly to tipping bay</p>
                        </div>
                    </div>
                    @if($booking->special_instructions)
                        <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                            <p class="text-yellow-700">{{ $booking->special_instructions }}</p>
                        </div>
                    @endif
                            <div class="mt-8 flex justify-end space-x-4">
                                <a href="{{ url()->previous() }}" 
                                   class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                                    🚛 Mark Vehicle Arrived
                                </button>
                            </div>
                        </form>
                    </div>
                    <!-- PO Breakdown Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 sticky top-6">
                            <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                                👁️ Expected Load Info
                            </h4>
                            <p class="text-xs text-blue-600 mb-4">Reference only - actual quantities recorded during tipping</p>
                            @if($booking->poNumbers && $booking->poNumbers->count() > 0)
                                <!-- PO Numbers Detail -->
                                <div class="space-y-3 mb-4">
                                    <div class="text-sm font-medium text-blue-700 mb-2">
                                        {{ $booking->poNumbers->count() }} PO Number{{ $booking->poNumbers->count() > 1 ? 's' : '' }}:
                                    </div>
                                    @foreach($booking->poNumbers as $po)
                                        <div class="bg-white rounded-lg p-3 border border-blue-200">
                                            <div class="font-mono text-sm font-semibold text-blue-600 mb-2">
                                                📋 {{ $po->po_number }}
                                            </div>
                                            @if($po->total_expected_cases > 0 || $po->total_expected_pallets > 0)
                                                <!-- Expected Quantities -->
                                                <div class="grid grid-cols-2 gap-2 text-xs">
                                                    <div class="bg-gray-50 p-2 rounded">
                                                        <div class="text-gray-600">Expected Cases</div>
                                                        <div class="font-semibold text-lg">{{ number_format($po->total_expected_cases) }}</div>
                                                    </div>
                                                    <div class="bg-gray-50 p-2 rounded">
                                                        <div class="text-gray-600">Expected Pallets</div>
                                                        <div class="font-semibold text-lg">{{ number_format($po->total_expected_pallets) }}</div>
                                                    </div>
                                                </div>
                                                @if($po->lines->count() > 0)
                                                    <div class="mt-2 text-xs text-gray-500">
                                                        {{ $po->lines->count() }} line{{ $po->lines->count() > 1 ? 's' : '' }}
                                                    </div>
                                                @endif
                                            @else
                                                <!-- No line data available -->
                                                <div class="bg-yellow-50 border border-yellow-200 rounded p-2">
                                                    <div class="text-xs text-yellow-700">
                                                        📝 PO registered but no line details entered yet
                                                    </div>
                                                    @if($po->lines->count() === 0)
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            No lines created - quantities will be recorded during tipping
                                                        </div>
                                                    @else
                                                        <div class="text-xs text-gray-500 mt-1">
                                                            {{ $po->lines->count() }} line{{ $po->lines->count() > 1 ? 's' : '' }} with no quantities
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <!-- Totals Summary -->
                                <div class="border-t border-blue-300 pt-3">
                                    <div class="text-sm font-semibold text-blue-800 mb-2">📊 Total Expected:</div>
                                    <div class="bg-blue-100 rounded-lg p-3 space-y-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm">📦 Cases:</span>
                                            <span class="font-bold text-lg text-blue-700">{{ number_format($booking->total_expected_cases) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm">🎯 Pallets:</span>
                                            <span class="font-bold text-lg text-blue-700">{{ number_format($booking->total_expected_pallets) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- No PO Numbers -->
                                <div class="text-center py-6 text-gray-500">
                                    <div class="text-4xl mb-2">📋</div>
                                    <div class="text-sm">No PO numbers available</div>
                                    <div class="text-xs mt-1">Expected: {{ number_format($booking->total_expected_cases) }} cases, {{ number_format($booking->total_expected_pallets) }} pallets</div>
                                </div>
                            @endif
                            <!-- Info Note -->
                            <div class="mt-4 pt-4 border-t border-blue-300">
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                                    <div class="text-xs text-yellow-800">
                                        <strong>ℹ️ Note:</strong> This information helps you verify the expected load. Actual quantities will be recorded during the tipping process.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Footer with Logo -->
                <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/ktl_logo.svg') }}" 
                                 alt="Knowles Logistics Logo" 
                                 class="h-16 w-auto object-contain"
                                 onerror="this.style.display='none';">
                            <span class="text-base text-gray-700 font-medium">Knowles Logistics - Vehicle Arrival System</span>
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ now()->format('Y') }} © Knowles Logistics - All rights reserved
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Arrival carrier search functionality (adapted from admin form)
    const searchInput = document.getElementById('arrival-carrier-search');
    const carrierIdInput = document.getElementById('arrival-carrier-id');
    const dropdown = document.getElementById('arrival-carrier-dropdown');
    const statusSpan = document.getElementById('arrival-carrier-status');
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
    const containerInput = document.getElementById('admin-container-number-input');
    if (containerInput) {
        containerInput.addEventListener('input', function() {
            const cursorPos = this.selectionStart;
            this.value = toUpperCase(this.value);
            this.setSelectionRange(cursorPos, cursorPos);
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
    // Initial selection update (for pre-selected values)
    updateTippingTypeSelection();
});
</script>
</x-warehouse-layout>