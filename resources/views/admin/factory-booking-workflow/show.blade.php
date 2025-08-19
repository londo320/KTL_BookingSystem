<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      {{-- Header with Factory Badge --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">🚛</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Factory Delivery Workflow</h1>
              <p class="text-sm text-gray-600">Tipping Operations Management</p>
            </div>
          </div>
        </div>
        
        {{-- Reference Badge --}}
        <div class="text-right">
          <div class="text-sm text-gray-500">Factory Reference</div>
          <div class="text-2xl font-bold text-orange-600">#{{ $factoryBooking->reference }}</div>
        </div>
      </div>
      
      {{-- Navigation --}}
      <div class="flex flex-wrap gap-3">
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Navigation</span>
          <a href="{{ route('admin.factory-bookings.show', $factoryBooking) }}"
             class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
            ← Factory Booking Details
          </a>
          <a href="{{ route('admin.tipping-workflow.dashboard') }}"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📊 Tipping Dashboard
          </a>
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto px-4">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Current Status --}}
    @php
      $movement = $factoryBooking->movements->last();
      $currentStatus = $movement ? $movement->current_status : 'arrived';
      $currentLocation = $movement?->tippingLocation;
      $currentBay = $movement?->tippingBay;
    @endphp

    <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-orange-800">Current Status: {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}</h3>
          @if($currentLocation)
            <p class="text-orange-700">📍 Location: {{ $currentLocation->name }}</p>
          @endif
          @if($currentBay)
            <p class="text-orange-700">🚛 Bay: {{ $currentBay->name }}</p>
          @endif
          @if($movement && $movement->operation_notes)
            <p class="text-orange-700 text-sm mt-2">📝 Notes: {{ $movement->operation_notes }}</p>
          @endif
        </div>
        <div class="text-right">
          <div class="text-sm text-gray-600">{{ $factoryBooking->customer->name }}</div>
          <div class="text-sm text-gray-600">{{ $factoryBooking->vehicle_registration }}</div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Workflow Actions --}}
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🔄 Workflow Actions</h3>
        
        {{-- Vehicle Movement --}}
        <div class="mb-6">
          <h4 class="font-medium text-gray-800 mb-3">🚛 Vehicle Movement</h4>
          
          {{-- Drop at Location --}}
          <form method="POST" action="{{ route('admin.factory-booking-workflow.drop-trailer', $factoryBooking) }}" class="mb-3">
            @csrf
            <div class="flex flex-wrap items-end gap-2">
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Drop at Location</label>
                <select name="tipping_location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                  <option value="">Select location...</option>
                  @foreach($availableLocations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }} 
                      ({{ $location->getCurrentOccupancy() }}/{{ $location->capacity }})
                    </option>
                  @endforeach
                </select>
              </div>
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional notes">
              </div>
              <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                Park Vehicle
              </button>
            </div>
          </form>

          {{-- Move to Bay --}}
          <form method="POST" action="{{ route('admin.factory-booking-workflow.move-to-bay', $factoryBooking) }}" class="mb-3">
            @csrf
            <div class="flex flex-wrap items-end gap-2">
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Move to Bay</label>
                <select name="tipping_bay_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                  <option value="">Select bay...</option>
                  @foreach($availableBays as $bay)
                    <option value="{{ $bay->id }}">{{ $bay->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional notes">
              </div>
              <button type="submit" class="px-3 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                Move to Bay
              </button>
            </div>
          </form>
        </div>

        {{-- Tipping Operations --}}
        <div class="mb-6">
          <h4 class="font-medium text-gray-800 mb-3">⚡ Tipping Operations</h4>
          
          @if(!$movement || !$movement->unloading_completed_at)
            {{-- Start Tipping --}}
            <form method="POST" action="{{ route('admin.factory-booking-workflow.start-tipping', $factoryBooking) }}" class="mb-3">
              @csrf
              <div class="flex flex-wrap items-end gap-2">
                <div class="flex-1">
                  <label class="block text-sm font-medium text-gray-700">Start Tipping</label>
                  <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional notes">
                </div>
                <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                  Start Tipping
                </button>
              </div>
            </form>

            {{-- Complete Tipping --}}
            <div class="p-4 bg-gray-50 rounded-lg">
              <h5 class="font-medium text-gray-800 mb-3">Complete Tipping</h5>
              
              <form method="POST" action="{{ route('admin.factory-booking-workflow.complete-tipping', $factoryBooking) }}">
                @csrf
                
                {{-- PO Lines Actual Quantities --}}
                @if($factoryBooking->poNumbers->count() > 0)
                  <div class="mb-4">
                    <h6 class="font-medium text-gray-700 mb-2">📦 Record Actual Quantities</h6>
                    
                    @foreach($factoryBooking->poNumbers as $po)
                      @if($po->lines->count() > 0)
                        <div class="mb-4 p-3 border border-gray-200 rounded">
                          <h7 class="font-medium text-gray-700">PO: {{ $po->po_number }}</h7>
                          
                          @foreach($po->lines as $line)
                            <div class="mt-3 p-3 bg-white rounded border">
                              <div class="flex items-center justify-between mb-2">
                                <span class="font-medium">{{ $line->expectedPalletType->name ?? 'Unknown Type' }}</span>
                                <span class="text-sm text-gray-600">Expected: {{ $line->expected_cases }} cases</span>
                              </div>
                              
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                {{-- Actual Cases --}}
                                <div>
                                  <label class="block text-sm font-medium text-gray-700">Actual Cases</label>
                                  <input type="number" 
                                         name="po_lines[{{ $line->id }}][actual_cases]" 
                                         value="{{ $line->actual_cases ?: $line->expected_cases }}"
                                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" 
                                         min="1" required>
                                </div>
                                
                                {{-- Actual Pallets --}}
                                <div>
                                  <label class="block text-sm font-medium text-gray-700">Actual Pallets</label>
                                  <div class="space-y-2">
                                    @foreach($palletTypes as $palletType)
                                      <div class="flex items-center space-x-2">
                                        <span class="text-sm w-20">{{ $palletType->name }}:</span>
                                        <input type="number" 
                                               name="po_lines[{{ $line->id }}][actual_pallets][{{ $loop->index }}][quantity]" 
                                               value="{{ $line->actualPallets->where('pallet_type_id', $palletType->id)->first()?->quantity ?? ($palletType->id == $line->expected_pallet_type_id ? $line->expected_pallets : 0) }}"
                                               class="flex-1 border-gray-300 rounded-md shadow-sm text-sm" 
                                               min="0">
                                        <input type="hidden" 
                                               name="po_lines[{{ $line->id }}][actual_pallets][{{ $loop->index }}][pallet_type_id]" 
                                               value="{{ $palletType->id }}">
                                      </div>
                                    @endforeach
                                  </div>
                                </div>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @endif
                    @endforeach
                  </div>
                @endif
                
                {{-- Completion Notes --}}
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes</label>
                  <textarea name="notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Any notes about the tipping completion..."></textarea>
                </div>
                
                {{-- Issues --}}
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Issues (if any)</label>
                  <div class="space-y-2">
                    <input type="text" name="issues[]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Describe any issues...">
                    <input type="text" name="issues[]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Additional issue...">
                  </div>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded hover:bg-green-700">
                  Complete Tipping
                </button>
              </form>
            </div>
          @else
            <div class="p-4 bg-green-100 rounded-lg">
              <p class="text-green-800">✅ Tipping completed at {{ $movement->unloading_completed_at->format('d M Y, H:i') }}</p>
            </div>
          @endif
        </div>

        {{-- Departure --}}
        <div>
          <h4 class="font-medium text-gray-800 mb-3">🏁 Departure</h4>
          
          <form method="POST" action="{{ route('admin.factory-booking-workflow.trailer-depart', $factoryBooking) }}">
            @csrf
            <div class="flex flex-wrap items-end gap-2">
              <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Mark Departure</label>
                <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional departure notes">
              </div>
              <button type="submit" class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                Mark Departed
              </button>
            </div>
          </form>
        </div>
      </div>

      {{-- Information Panel --}}
      <div class="space-y-6">
        {{-- Factory Booking Details --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h4 class="font-medium text-gray-800 mb-3">📋 Factory Booking Details</h4>
          
          <div class="space-y-2 text-sm">
            <div><strong>Reference:</strong> {{ $factoryBooking->reference }}</div>
            <div><strong>Customer:</strong> {{ $factoryBooking->customer->name }}</div>
            <div><strong>Depot:</strong> {{ $factoryBooking->depot->name }}</div>
            <div><strong>Vehicle:</strong> {{ $factoryBooking->vehicle_registration }}</div>
            @if($factoryBooking->trailer_registration)
              <div><strong>Trailer:</strong> {{ $factoryBooking->trailer_registration }}</div>
            @endif
            @if($factoryBooking->driver_name)
              <div><strong>Driver:</strong> {{ $factoryBooking->driver_name }}</div>
            @endif
            <div><strong>Arrived:</strong> {{ $factoryBooking->arrived_at->format('d M Y, H:i') }}</div>
            <div><strong>Time on Site:</strong> {{ $factoryBooking->getTimeOnSite() }}</div>
          </div>
        </div>

        {{-- PO Information --}}
        @if($factoryBooking->poNumbers->count() > 0)
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h4 class="font-medium text-gray-800 mb-3">📦 PO Numbers</h4>
            
            <div class="space-y-3">
              @foreach($factoryBooking->poNumbers as $po)
                <div class="p-3 bg-gray-50 rounded-md">
                  <div class="font-medium">{{ $po->po_number }}</div>
                  @if($po->description)
                    <div class="text-sm text-gray-600">{{ $po->description }}</div>
                  @endif
                  @if($po->lines->count() > 0)
                    <div class="mt-2 text-xs text-gray-500">
                      {{ $po->lines->count() }} line(s)
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        @endif

        {{-- Movement History --}}
        @if($factoryBooking->movements->count() > 0)
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h4 class="font-medium text-gray-800 mb-3">📊 Movement History</h4>
            
            <div class="space-y-2 text-sm">
              @foreach($factoryBooking->movements as $mov)
                <div class="flex justify-between">
                  <span>{{ ucfirst(str_replace('_', ' ', $mov->current_status)) }}</span>
                  <span class="text-gray-600">{{ $mov->updated_at->format('M j, H:i') }}</span>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>