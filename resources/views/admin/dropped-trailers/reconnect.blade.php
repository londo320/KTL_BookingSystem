<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">🔗 Reconnect Trailer to Vehicle</h2>
                <p class="text-sm text-gray-600 mt-1">Booking #{{ $booking->id }} - {{ $booking->customer->name ?? 'No Customer' }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('app.dropped-trailers.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Dropped Trailers
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-4xl mx-auto">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-bold">Reconnection Failed</h4>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Trailer Information --}}
        <div class="mb-6 p-6 bg-orange-50 border border-orange-200 rounded-lg">
            <h3 class="text-lg font-semibold text-orange-800 mb-3">🚛 Trailer Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Current Location</p>
                    <p class="font-medium">
                        @if($booking->tippingBay)
                            {{ $booking->tippingBay->name }} (Bay)
                        @elseif($booking->tippingLocation)
                            {{ $booking->tippingLocation->name }} (Parking Area)
                        @else
                            Location not set
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Container/Trailer</p>
                    <p class="font-medium font-mono">{{ $booking->container_number ?? 'Not specified' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tipping Status</p>
                    <div>{!! $booking->tipping_status_badge !!}</div>
                </div>
            </div>
            {{-- Original Vehicle Info --}}
            @if($booking->vehicle_registration)
                <div class="mt-4 pt-4 border-t border-orange-200">
                    <p class="text-sm text-gray-600 mb-2">Original Delivery Vehicle</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong>Registration:</strong> {{ $booking->vehicle_registration }}
                        </div>
                    </div>
                </div>
            @endif
            {{-- Timeline --}}
            <div class="mt-4 pt-4 border-t border-orange-200">
                <p class="text-sm text-gray-600 mb-2">Timeline</p>
                <div class="space-y-1 text-sm">
                    @if($booking->trailer_dropped_at)
                        <div>• Dropped: {{ $booking->trailer_dropped_at->format('M j, Y H:i') }}</div>
                    @endif
                    @if($booking->tipping_completed_at)
                        <div>• Tipping completed: {{ $booking->tipping_completed_at->format('M j, Y H:i') }}</div>
                    @endif
                    <div class="text-gray-500">• Duration on site: {{ $booking->trailer_dropped_at ? $booking->trailer_dropped_at->diffForHumans(null, true) : 'Unknown' }}</div>
                </div>
            </div>
        </div>
        {{-- Reconnection Form --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚚 New Vehicle Details</h3>
                <p class="text-gray-600 mt-1">Enter details for the vehicle collecting the trailer</p>
            </div>
            <form method="POST" action="{{ route('app.dropped-trailers.reconnect', $booking) }}" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Vehicle Registration (Required) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Vehicle Registration <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_registration" required
                               value="{{ old('vehicle_registration') }}"
                               placeholder="e.g., AB12 CDE"
                               class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        @error('vehicle_registration')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Registration of the vehicle collecting the trailer</p>
                    </div>
                    {{-- Departure Notes --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
                        <textarea name="departure_notes" rows="3"
                                  class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                                  placeholder="Optional notes about the departure...">{{ old('departure_notes') }}</textarea>
                        @error('departure_notes')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                {{-- Warning Message --}}
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="text-yellow-600 text-xl">⚠️</div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Departure Confirmation</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This action will:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Connect the trailer to the new vehicle</li>
                                    <li>Mark the trailer as departed from site</li>
                                    <li>Update the booking with departure time</li>
                                    <li>Free up the tipping bay/location for other use</li>
                                    <li>Complete the booking workflow</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Action Buttons --}}
                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('app.dropped-trailers.index') }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        🔗 Reconnect & Depart
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>