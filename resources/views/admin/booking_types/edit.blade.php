<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">✏️ Edit Booking Type</h2>
  </x-slot>
  <div class="py-6 max-w-4xl mx-auto space-y-6">
    <form method="POST" action="{{ route('app.booking-types.update', $bookingType) }}">
      @csrf
      @method('PUT')

      {{-- Basic Info --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Basic Information</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" class="w-full border rounded p-2"
                   value="{{ old('name', $bookingType->name) }}" required>
            @error('name')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Default Duration (minutes)</label>
            <input type="number" name="duration_minutes" class="w-full border rounded p-2"
                   value="{{ old('duration_minutes', $bookingType->duration_minutes ?? 60) }}" required min="1">
            <p class="text-xs text-gray-500 mt-1">Used when no depot or customer-specific duration is set</p>
            @error('duration_minutes')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>

      {{-- Depot-Specific Durations --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Depot-Specific Durations</h3>
        <p class="text-sm text-gray-600 mb-4">Override the default duration for specific depots</p>
        <div class="space-y-3">
          @foreach($depots as $depot)
            <div class="flex items-center gap-4">
              <label class="w-1/3 text-sm">{{ $depot->name }}</label>
              <input type="number"
                     name="depot_durations[{{ $depot->id }}]"
                     class="border rounded p-2 w-32"
                     value="{{ old('depot_durations.'.$depot->id, $depotDurations[$depot->id] ?? '') }}"
                     placeholder="Default"
                     min="1">
              <span class="text-xs text-gray-500">minutes (leave empty for default)</span>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Customer-Specific Durations --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Customer-Specific Durations</h3>
        <p class="text-sm text-gray-600 mb-4">Override durations for specific customers (optionally per depot)</p>
        <div class="space-y-4">
          @foreach($customers as $customer)
            <div class="border-l-4 border-blue-200 pl-4 py-2">
              <div class="font-medium text-sm mb-2">{{ $customer->name }}</div>

              {{-- All depots --}}
              <div class="flex items-center gap-4 mb-2">
                <label class="w-1/3 text-sm text-gray-600">All Depots</label>
                <input type="number"
                       name="customer_durations[{{ $customer->id }}][all]"
                       class="border rounded p-2 w-32"
                       value="{{ old('customer_durations.'.$customer->id.'.all', $customerDurations[$customer->id.'_all']['duration'] ?? '') }}"
                       placeholder="Default"
                       min="1">
                <span class="text-xs text-gray-500">minutes</span>
              </div>

              {{-- Per depot --}}
              @foreach($depots as $depot)
                <div class="flex items-center gap-4 ml-4">
                  <label class="w-1/3 text-sm text-gray-500">{{ $depot->name }}</label>
                  <input type="number"
                         name="customer_durations[{{ $customer->id }}][{{ $depot->id }}]"
                         class="border rounded p-2 w-32"
                         value="{{ old('customer_durations.'.$customer->id.'.'.$depot->id, $customerDurations[$customer->id.'_'.$depot->id]['duration'] ?? '') }}"
                         placeholder="Default"
                         min="1">
                  <span class="text-xs text-gray-500">minutes</span>
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
      </div>

      <div class="flex justify-end gap-4">
        <a href="{{ route('app.booking-types.index') }}"
           class="text-sm text-gray-600 hover:underline">Cancel</a>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Update Booking Type
        </button>
      </div>
    </form>
  </div>
</x-app-layout>
