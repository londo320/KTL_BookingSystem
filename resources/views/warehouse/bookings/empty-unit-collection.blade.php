<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">🚛 Empty Unit Collection</h2>
      <a href="{{ route('app.bookings.index') }}"
         class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to Bookings
      </a>
    </div>
  </x-slot>
  <div class="py-6 max-w-6xl mx-auto">
    <!-- Collection Form -->
    <div class="bg-white p-6 rounded shadow mb-6">
      <div class="flex items-center space-x-3 mb-4">
        <img src="{{ asset('images/ktl_logo.svg') }}" 
             alt="Knowles Logistics Logo" 
             class="h-16 w-auto object-contain"
             onerror="this.style.display='none';">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Record Unit Collection</h3>
          <p class="text-sm text-gray-600">
            Use this form when a vehicle arrives to collect a trailer from either collection zones or tipping bays (no booking reference required).
          </p>
        </div>
      </div>
      <form action="{{ route('app.empty-unit-collection.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Vehicle Details -->
          <div class="col-span-2 border-t pt-4">
            <h4 class="font-medium text-gray-800 mb-3">🚛 Vehicle Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Vehicle Registration <span class="text-red-500">*</span>
                </label>
                <input type="text" name="vehicle_registration" required
                       value="{{ old('vehicle_registration') }}"
                       placeholder="e.g., AB12 CDE"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('vehicle_registration')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Carrier Company</label>
                <div class="relative">
                  <input type="text" 
                         id="collection-carrier-search" 
                         name="carrier_name"
                         value="{{ old('carrier_name') }}"
                         placeholder="Search or type carrier name..."
                         autocomplete="off"
                         class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 pr-10">
                  {{-- Hidden carrier_id field --}}
                  <input type="hidden" 
                         id="collection-carrier-id" 
                         name="carrier_id" 
                         value="{{ old('carrier_id') }}">
                  {{-- Search dropdown --}}
                  <div id="collection-carrier-dropdown" 
                       class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    {{-- Results will be populated by JavaScript --}}
                  </div>
                  {{-- Status indicators --}}
                  <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <span id="collection-carrier-status" class="text-xs"></span>
                  </div>
                </div>
                @error('carrier_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                @error('carrier_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
              </div>
            </div>
          </div>
          <!-- Collection Details -->
          <div class="col-span-2 border-t pt-4">
            <h4 class="font-medium text-gray-800 mb-3">📦 Collection Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Trailer Number/ID
                </label>
                <input type="text" name="collected_trailer_number" readonly
                       value="{{ old('collected_trailer_number') }}"
                       placeholder="Select a trailer below to auto-fill"
                       class="w-full border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">This will be filled automatically when you select a trailer</p>
                @error('collected_trailer_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Trailer to Collect <span class="text-red-500">*</span></label>
                <select name="collected_from_booking_id" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <option value="">– Choose a trailer –</option>
                  @foreach($availableTrailers as $booking)
                    <option value="{{ $booking->id }}" {{ old('collected_from_booking_id') == $booking->id ? 'selected' : '' }}>
                      {{ $booking->trailer_display_number }}
                      - {{ $booking->current_location }}
                      - @if($booking->movement_status === 'empty') Empty @elseif($booking->movement_status === 'trailer_dropped') Full @else {{ ucwords(str_replace('_', ' ', $booking->movement_status)) }} @endif
                    </option>
                  @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">Select trailer by number - sorted alphabetically with location and status</p>
                @error('collected_from_booking_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>
          <!-- Selected Trailer Details (Hidden by default) -->
          <div id="trailer-details" class="col-span-2 border-t pt-4 hidden">
            <h4 class="font-medium text-gray-800 mb-3">📋 Selected Trailer Details</h4>
            <div class="bg-gray-50 p-4 rounded-lg">
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                  <span class="font-medium text-gray-700">Booking Reference:</span>
                  <div id="detail-booking-ref" class="text-gray-900">-</div>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Current Location:</span>
                  <div id="detail-location" class="text-gray-900">-</div>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Status:</span>
                  <div id="detail-status" class="text-gray-900">-</div>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Customer:</span>
                  <div id="detail-customer" class="text-gray-900">-</div>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Container Number:</span>
                  <div id="detail-container" class="text-gray-900">-</div>
                </div>
                <div>
                  <span class="font-medium text-gray-700">Carrier Company:</span>
                  <div id="detail-carrier" class="text-gray-900">-</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Form Actions -->
        <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
          <a href="{{ route('app.bookings.index') }}"
             class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
            Cancel
          </a>
          <button type="submit"
                  class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
            🚛 Record Collection
          </button>
        </div>
      </form>
    </div>
    <!-- Available Trailers -->
    @if($availableTrailers->count() > 0)
    <div class="bg-white p-6 rounded shadow">
      <h3 class="text-lg font-medium text-gray-900 mb-4">📦 Trailers Available for Collection (Empty & Full)</h3>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trailer Number</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Ref</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Collection</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($availableTrailers as $booking)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ $booking->trailer_display_number }}
                @if($booking->trailerType)
                  <br><span class="text-xs text-gray-500">{{ $booking->trailerType->name }}</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->current_location ?? 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs rounded-full
                  @if($booking->movement_status === 'empty') bg-green-100 text-green-800
                  @elseif($booking->movement_status === 'awaiting_collection') bg-orange-100 text-orange-800
                  @elseif($booking->movement_status === 'trailer_dropped') bg-blue-100 text-blue-800
                  @else bg-gray-100 text-gray-800 @endif">
                  @if($booking->movement_status === 'empty') Empty - Ready
                  @elseif($booking->movement_status === 'awaiting_collection') Awaiting Collection
                  @elseif($booking->movement_status === 'trailer_dropped') Full - Dropped
                  @else {{ ucwords(str_replace('_', ' ', $booking->movement_status ?? 'Unknown')) }}
                  @endif
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->customer_name ?? 'Unknown' }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->booking_reference }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                @if($booking->trailer_collection_scheduled)
                  {{ $booking->trailer_collection_scheduled->format('d-M-Y H:i') }}
                @else
                  <span class="text-gray-400">Not scheduled</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @else
    <div class="bg-white p-6 rounded shadow">
      <div class="text-center py-8">
        <div class="text-gray-400 text-6xl mb-4">📭</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Trailers Available for Collection</h3>
        <p class="text-gray-600">There are currently no trailers left on site awaiting collection.</p>
      </div>
    </div>
    @endif
    <!-- Footer with Logo -->
    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4 rounded-b shadow">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <img src="{{ asset('images/ktl_logo.svg') }}" 
               alt="Knowles Logistics Logo" 
               class="h-16 w-auto object-contain"
               onerror="this.style.display='none';">
          <span class="text-base text-gray-700 font-medium">Knowles Logistics - Empty Unit Collection System</span>
        </div>
        <div class="text-xs text-gray-500">
          {{ now()->format('Y') }} © Knowles Logistics - All rights reserved
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const bookingSelect = document.querySelector('select[name="collected_from_booking_id"]');
        const trailerNumberInput = document.querySelector('input[name="collected_trailer_number"]');
        const carrierSearchInput = document.querySelector('#collection-carrier-search');
        const trailerDetails = document.getElementById('trailer-details');
        // Booking data for auto-population
        const bookingData = {
            @foreach($availableTrailers as $booking)
            {{ $booking->id }}: {
                booking_reference: '{{ $booking->booking_reference }}',
                trailer_number: '{{ $booking->trailer_display_number }}',
                carrier_name: '{{ addslashes($booking->carrier_company ?? '') }}',
                current_location: '{{ addslashes($booking->current_location ?? '') }}',
                customer_name: '{{ addslashes($booking->customer_name ?? '') }}',
                status: '@if($booking->movement_status === "empty") Empty - Ready @elseif($booking->movement_status === "trailer_dropped") Full - Dropped @else {{ ucwords(str_replace("_", " ", $booking->movement_status)) }} @endif',
                depot_name: '{{ addslashes($booking->depot_name ?? '') }}'
            },
            @endforeach
        };
        // Auto-populate form when booking is selected
        if (bookingSelect && trailerNumberInput) {
            bookingSelect.addEventListener('change', function() {
                const selectedBookingId = this.value;
                if (selectedBookingId && bookingData[selectedBookingId]) {
                    const data = bookingData[selectedBookingId];
                    // Auto-populate trailer number
                    trailerNumberInput.value = data.trailer_number;
                    // Auto-populate carrier name
                    if (carrierSearchInput && data.carrier_name) {
                        carrierSearchInput.value = data.carrier_name;
                    }
                    // Show and populate trailer details
                    if (trailerDetails) {
                        document.getElementById('detail-booking-ref').textContent = data.booking_reference;
                        document.getElementById('detail-location').textContent = data.current_location;
                        document.getElementById('detail-status').textContent = data.status;
                        document.getElementById('detail-customer').textContent = data.customer_name;
                        document.getElementById('detail-container').textContent = data.trailer_number;
                        document.getElementById('detail-carrier').textContent = data.carrier_name || 'Not specified';
                        trailerDetails.classList.remove('hidden');
                    }
                } else {
                    // Clear fields and hide details
                    trailerNumberInput.value = '';
                    if (carrierSearchInput) {
                        carrierSearchInput.value = '';
                    }
                    if (trailerDetails) {
                        trailerDetails.classList.add('hidden');
                    }
                }
            });
        }
        // Carrier search functionality (simplified version like other views)
        const searchInput = document.getElementById('collection-carrier-search');
        const dropdown = document.getElementById('collection-carrier-dropdown');
        const carrierIdInput = document.getElementById('collection-carrier-id');
        const statusSpan = document.getElementById('collection-carrier-status');
        let isLoading = false;
        if (searchInput && dropdown) {
            function searchCarriers(query) {
                if (query.length < 2) {
                    dropdown.classList.add('hidden');
                    if (statusSpan) statusSpan.textContent = '';
                    return;
                }
                if (isLoading) return;
                isLoading = true;
                if (statusSpan) statusSpan.textContent = '⏳';
                fetch(`{{ route('api.carriers.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        dropdown.innerHTML = '';
                        if (data.carriers && data.carriers.length > 0) {
                            // Show total results if more than displayed
                            if (data.total > data.carriers.length) {
                                const headerItem = document.createElement('div');
                                headerItem.className = 'px-3 py-2 bg-gray-100 border-b border-gray-200 text-xs text-gray-600';
                                headerItem.innerHTML = `Showing ${data.carriers.length} of ${data.total} carriers`;
                                dropdown.appendChild(headerItem);
                            }
                            data.carriers.forEach(carrier => {
                                const item = document.createElement('div');
                                item.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                                item.innerHTML = `
                                    <div class="font-medium">${carrier.name}</div>
                                    <div class="text-xs text-gray-500">${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}</div>
                                `;
                                item.onclick = () => selectCarrier(carrier);
                                dropdown.appendChild(item);
                            });
                            dropdown.classList.remove('hidden');
                            if (statusSpan) statusSpan.textContent = `${data.carriers.length} found`;
                        } else {
                            // Create "Create new" option
                            const createItem = document.createElement('div');
                            createItem.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-blue-600';
                            createItem.innerHTML = `
                                <div class="font-medium">Create "${query}"</div>
                                <div class="text-xs text-blue-500">New carrier</div>
                            `;
                            createItem.onclick = () => selectCarrier({id: '', name: query, isNew: true});
                            dropdown.appendChild(createItem);
                            dropdown.classList.remove('hidden');
                            if (statusSpan) statusSpan.textContent = 'Create new';
                        }
                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Search failed:', error);
                        dropdown.classList.add('hidden');
                        if (statusSpan) statusSpan.textContent = '❌ Error';
                        isLoading = false;
                    });
            }
            function selectCarrier(carrier) {
                searchInput.value = carrier.name;
                if (carrierIdInput) {
                    carrierIdInput.value = carrier.id || '';
                }
                dropdown.classList.add('hidden');
                if (statusSpan) {
                    statusSpan.textContent = carrier.isNew ? '✨ New' : '✓';
                }
            }
            // Search input events
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                searchCarriers(query);
            });
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });
  </script>
</x-warehouse-layout>