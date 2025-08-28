<x-site-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            🚛 Gate Arrival Processing - SEPARATE FORM VERSION - {{ $booking->booking_reference }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            
            <!-- Booking Summary -->
            <div class="px-6 py-4 border-b bg-green-50">
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

            <form method="POST" action="{{ route('site.bookings.arrival', $booking) }}" class="p-6">
                @csrf
                
                <h3 class="text-lg font-medium text-gray-900 mb-6">🚪 Gate Processing - Vehicle Arrival</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Required Vehicle Registration -->
                    <div class="md:col-span-2">
                        <label class="block text-lg font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="{{ old('vehicle_registration', $booking->vehicle_registration) }}"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-lg p-3">
                        @error('vehicle_registration')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1"><strong>REQUIRED:</strong> Must be entered to process arrival</p>
                    </div>

                    <!-- Container/Trailer Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Container/Trailer Number
                        </label>
                        <input type="text" name="container_number"
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
                        <input type="text" name="carrier_company" required
                               value="{{ old('carrier_company', $booking->carrier_company) }}"
                               placeholder="e.g., ABC Transport Ltd"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('carrier_company')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
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
                        <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle to parking area</p>
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

                <!-- Submit Button -->
                <div class="mt-8 bg-green-50 p-4 rounded-lg">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('site.bookings.index') }}" 
                           class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-8 py-3 bg-green-600 text-white text-lg font-medium rounded-lg hover:bg-green-700">
                            ✅ Process Arrival
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-site-admin-layout>