<x-app-layout>
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
      {{-- Gate Information --}}
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🚪</span>
          Gate Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Depot Selection --}}
          <div>
            <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">
              Depot <span class="text-red-500">*</span>
            </label>
            <select name="depot_id" id="depot_id" required 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Depot</option>
              @foreach($depots as $depot)
                <option value="{{ $depot->id }}" {{ old('depot_id') == $depot->id ? 'selected' : '' }}>
                  {{ $depot->name }}
                </option>
              @endforeach
            </select>
            @if($depots->count() === 1)
              <p class="mt-1 text-xs text-gray-500">Auto-selected based on your access</p>
            @endif
          </div>
          {{-- Priority --}}
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
              Priority (0-100)
            </label>
            <div class="relative">
              <input type="number" name="priority" id="priority" min="0" max="100" 
                     value="{{ old('priority', 50) }}"
                     class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <span class="text-gray-400 text-sm">Default: 50</span>
              </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Higher numbers = higher priority (80+ urgent, 50 normal, 20- low)
            </p>
          </div>
        </div>
      </div>
      {{-- Customer & Carrier Information --}}
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🏢</span>
          Customer & Carrier Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Customer --}}
          <div>
            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
              Customer <span class="text-red-500">*</span>
            </label>
            <select name="customer_id" id="customer_id" required 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Customer</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                  {{ $customer->name }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- Carrier (Optional) --}}
          <div>
            <label for="carrier_id" class="block text-sm font-medium text-gray-700 mb-2">
              Carrier (Optional)
            </label>
            <select name="carrier_id" id="carrier_id" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Carrier (if known)</option>
              @foreach($carriers as $carrier)
                <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                  {{ $carrier->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      {{-- Vehicle Information --}}
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🚛</span>
          Vehicle Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Vehicle Registration --}}
          <div>
            <label for="vehicle_registration" class="block text-sm font-medium text-gray-700 mb-2">
              Vehicle Registration <span class="text-red-500">*</span>
            </label>
            <input type="text" name="vehicle_registration" id="vehicle_registration" required 
                   value="{{ old('vehicle_registration') }}"
                   placeholder="e.g., AB12 XYZ"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
          </div>
          {{-- Trailer Registration --}}
          <div>
            <label for="trailer_registration" class="block text-sm font-medium text-gray-700 mb-2">
              Trailer Registration (Optional)
            </label>
            <input type="text" name="trailer_registration" id="trailer_registration" 
                   value="{{ old('trailer_registration') }}"
                   placeholder="e.g., TR12 345"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
          </div>
          {{-- Trailer Type --}}
          <div>
            <label for="trailer_type_id" class="block text-sm font-medium text-gray-700 mb-2">
              Trailer Type (Optional)
            </label>
            <select name="trailer_type_id" id="trailer_type_id" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Trailer Type (if known)</option>
              @foreach($trailerTypes as $trailerType)
                <option value="{{ $trailerType->id }}" {{ old('trailer_type_id') == $trailerType->id ? 'selected' : '' }}>
                  {{ $trailerType->name }}
                </option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      {{-- Driver Information --}}
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">👤</span>
          Driver Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          {{-- Driver Name --}}
          <div>
            <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">
              Driver Name (Optional)
            </label>
            <input type="text" name="driver_name" id="driver_name" 
                   value="{{ old('driver_name') }}"
                   placeholder="e.g., John Smith"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
          {{-- Driver Phone --}}
          <div>
            <label for="driver_phone" class="block text-sm font-medium text-gray-700 mb-2">
              Driver Phone (Optional)
            </label>
            <input type="text" name="driver_phone" id="driver_phone" 
                   value="{{ old('driver_phone') }}"
                   placeholder="e.g., 07123 456789"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>
      </div>
      {{-- Additional Information --}}
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">📝</span>
          Additional Information
        </h3>
        <div class="space-y-4">
          {{-- Delivery Notes --}}
          <div>
            <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Delivery Notes (Optional)
            </label>
            <textarea name="delivery_notes" id="delivery_notes" rows="3" 
                      placeholder="Any relevant information about the delivery..."
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">{{ old('delivery_notes') }}</textarea>
          </div>
          {{-- Gate Notes --}}
          <div>
            <label for="gate_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Gate Staff Notes (Optional)
            </label>
            <textarea name="gate_notes" id="gate_notes" rows="3" 
                      placeholder="Internal notes for gate staff and operations..."
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">{{ old('gate_notes') }}</textarea>
          </div>
        </div>
      </div>
      {{-- Important Information Box --}}
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-blue-800 font-medium mb-2">📋 What happens next?</h4>
        <ul class="text-blue-700 text-sm space-y-1">
          <li>• A unique reference (FAC-YYYY-XXX) will be automatically generated</li>
          <li>• PO numbers can be added later once details are confirmed</li>
          <li>• The delivery will immediately appear in tipping workflow queues</li>
          <li>• All standard tipping workflow procedures apply</li>
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
  {{-- Auto-uppercase script for vehicle registrations --}}
  <script>
    document.getElementById('vehicle_registration').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });
    document.getElementById('trailer_registration').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });
    // Auto-select depot if only one available
    document.addEventListener('DOMContentLoaded', function() {
      const depotSelect = document.getElementById('depot_id');
      if (depotSelect.options.length === 2) { // Only "Select Depot" + one depot
        depotSelect.selectedIndex = 1;
      }
    });
  </script>
</x-app-layout>