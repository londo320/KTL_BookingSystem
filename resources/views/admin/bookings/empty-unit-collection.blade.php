<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">🚛 Empty Unit Collection</h2>
      <a href="{{ route('admin.bookings.index') }}"
         class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to Bookings
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-6xl mx-auto">
    
    <!-- Collection Form -->
    <div class="bg-white p-6 rounded shadow mb-6">
      <h3 class="text-lg font-medium text-gray-900 mb-4">Record Unit Collection</h3>
      <p class="text-sm text-gray-600 mb-6">
        Use this form when a vehicle arrives to collect a trailer from either collection zones or tipping bays (no booking reference required).
      </p>
      
      <form action="{{ route('admin.empty-unit-collection.process') }}" method="POST">
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
                  <input type="text" id="carrier-search" name="carrier_name"
                         value="{{ old('carrier_company') }}"
                         placeholder="Search or type carrier name..."
                         autocomplete="off"
                         class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <input type="hidden" id="carrier-id" name="carrier_id" value="">
                  
                  <!-- Dropdown for search results -->
                  <div id="carrier-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1 hidden max-h-60 overflow-auto">
                    <div id="carrier-results"></div>
                  </div>
                </div>
                @error('carrier_company')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>
          
          <!-- Collection Details -->
          <div class="col-span-2 border-t pt-4">
            <h4 class="font-medium text-gray-800 mb-3">📦 Collection Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Trailer Number/ID <span class="text-red-500">*</span>
                </label>
                <input type="text" name="collected_trailer_number" required
                       value="{{ old('collected_trailer_number') }}"
                       placeholder="e.g., TR-001, CONT789123"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                @error('collected_trailer_number')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Collection Location <span class="text-red-500">*</span>
                </label>
                <select name="collection_location" required
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <option value="">– Select Location –</option>
                  
                  @if($collectionZones->count() > 0)
                    <optgroup label="📦 Collection Zones">
                      @foreach($collectionZones as $zone)
                        <option value="ZONE_{{ $zone->id }}" {{ old('collection_location') == 'ZONE_'.$zone->id ? 'selected' : '' }}>
                          {{ $zone->name }}@if($zone->code) ({{ $zone->code }})@endif
                          - {{ $zone->getAvailableCapacity() }}/{{ $zone->capacity }} spaces
                        </option>
                      @endforeach
                    </optgroup>
                  @endif
                  
                  @if($tippingBays->count() > 0)
                    <optgroup label="🏗️ Tipping Bays">
                      @foreach($tippingBays as $bay)
                        <option value="BAY_{{ $bay->id }}" {{ old('collection_location') == 'BAY_'.$bay->id ? 'selected' : '' }}>
                          {{ $bay->name }}@if($bay->code) ({{ $bay->code }})@endif
                          @if($bay->is_occupied) - Currently Occupied @else - Available @endif
                        </option>
                      @endforeach
                    </optgroup>
                  @endif
                </select>
                @error('collection_location')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
              
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Link to Booking (Optional)</label>
                <select name="collected_from_booking_id"
                        class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                  <option value="">– No specific booking –</option>
                  @foreach($availableTrailers as $booking)
                    <option value="{{ $booking->id }}" {{ old('collected_from_booking_id') == $booking->id ? 'selected' : '' }}>
                      {{ $booking->booking_reference }} - {{ $booking->container_number ?? 'No container number' }} 
                      ({{ $booking->dropped_trailer_location ?? 'Unknown location' }})
                    </option>
                  @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">If collecting a specific trailer from a booking, select it here</p>
                @error('collected_from_booking_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
              </div>
            </div>
          </div>
          
        </div>
        
        <!-- Form Actions -->
        <div class="mt-6 pt-4 border-t flex justify-end space-x-3">
          <a href="{{ route('admin.bookings.index') }}"
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container/Trailer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled Collection</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @foreach($availableTrailers as $booking)
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ $booking->booking_reference }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->container_number ?? 'Not specified' }}
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
                @if($booking->trailer_collection_scheduled)
                  {{ $booking->trailer_collection_scheduled->format('d-M-Y H:i') }}
                @else
                  <span class="text-gray-400">Not scheduled</span>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {{ $booking->customer->name ?? 'Unknown' }}
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
  </div>

  <script>
    // Auto-populate form when booking is selected
    document.addEventListener('DOMContentLoaded', function() {
        const bookingSelect = document.querySelector('select[name="collected_from_booking_id"]');
        const trailerNumberInput = document.querySelector('input[name="collected_trailer_number"]');
        const carrierSearchInput = document.querySelector('#carrier-search');
        
        if (bookingSelect && trailerNumberInput) {
            // Booking data for auto-population
            const bookingData = {
                @foreach($availableTrailers as $booking)
                {{ $booking->id }}: {
                    trailer_number: '{{ $booking->container_number ?? "TRAILER-" . $booking->id }}',
                    carrier_name: '{{ addslashes($booking->carrier_company ?? '') }}',
                    current_location: '{{ addslashes($booking->current_location ?? '') }}'
                },
                @endforeach
            };
            
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
                } else {
                    // Clear fields
                    trailerNumberInput.value = '';
                    if (carrierSearchInput) {
                        carrierSearchInput.value = '';
                    }
                }
            });
        }
        
        // Carrier search functionality
        const searchInput = document.getElementById('carrier-search');
        const dropdown = document.getElementById('carrier-dropdown');
        const resultsDiv = document.getElementById('carrier-results');
        const carrierIdInput = document.getElementById('carrier-id');
        let selectedCarrierId = '';
        let isLoading = false;

        function searchCarriers(query) {
            if (query.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }
            
            if (isLoading) return;
            isLoading = true;
            
            fetch(`{{ route('api.carriers.search') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    
                    if (data.carriers && data.carriers.length > 0) {
                        data.carriers.forEach(carrier => {
                            const item = document.createElement('div');
                            item.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-b-0';
                            item.innerHTML = `
                                <div class="font-medium">${carrier.name}</div>
                                <div class="text-xs text-gray-500">Used in ${carrier.bookings_count || 0} bookings</div>
                            `;
                            item.onclick = () => selectCarrier(carrier);
                            resultsDiv.appendChild(item);
                        });
                        
                        // Add option to create new carrier
                        const createItem = document.createElement('div');
                        createItem.className = 'px-4 py-2 hover:bg-blue-100 cursor-pointer border-t border-blue-200 bg-blue-50';
                        createItem.innerHTML = `<div class="text-blue-600 font-medium">+ Create "${query}" as new carrier</div>`;
                        createItem.onclick = () => createNewCarrier(query);
                        resultsDiv.appendChild(createItem);
                        
                        dropdown.classList.remove('hidden');
                    } else {
                        // No results, show create option
                        const createItem = document.createElement('div');
                        createItem.className = 'px-4 py-2 hover:bg-blue-100 cursor-pointer bg-blue-50';
                        createItem.innerHTML = `<div class="text-blue-600 font-medium">+ Create "${query}" as new carrier</div>`;
                        createItem.onclick = () => createNewCarrier(query);
                        resultsDiv.appendChild(createItem);
                        dropdown.classList.remove('hidden');
                    }
                    
                    isLoading = false;
                })
                .catch(error => {
                    console.error('Search failed:', error);
                    dropdown.classList.add('hidden');
                    isLoading = false;
                });
        }

        function selectCarrier(carrier) {
            searchInput.value = carrier.name;
            carrierIdInput.value = carrier.id;
            selectedCarrierId = carrier.id;
            dropdown.classList.add('hidden');
        }

        function createNewCarrier(name) {
            searchInput.value = name;
            carrierIdInput.value = '';
            selectedCarrierId = '';
            dropdown.classList.add('hidden');
        }

        // Search input events
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query !== searchInput.dataset.lastQuery) {
                searchInput.dataset.lastQuery = query;
                searchCarriers(query);
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    });
  </script>
</x-app-layout>