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

      {{-- Time of Day Restrictions --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">⏰ Time of Day Restrictions</h3>
        <p class="text-sm text-gray-600 mb-4">Restrict when this booking type can be booked (leave empty for 24/7 availability)</p>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Earliest Booking Time</label>
            <input type="time" name="booking_start_time" class="w-full border rounded p-2"
                   value="{{ old('booking_start_time', $bookingType->booking_start_time ? \Carbon\Carbon::parse($bookingType->booking_start_time)->format('H:i') : '') }}">
            <p class="text-xs text-gray-500 mt-1">e.g., 08:00 (slots before this time won't show for this booking type)</p>
            @error('booking_start_time')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Latest Booking Time</label>
            <input type="time" name="booking_end_time" class="w-full border rounded p-2"
                   value="{{ old('booking_end_time', $bookingType->booking_end_time ? \Carbon\Carbon::parse($bookingType->booking_end_time)->format('H:i') : '') }}">
            <p class="text-xs text-gray-500 mt-1">e.g., 17:00 (slots after this time won't show for this booking type)</p>
            @error('booking_end_time')
              <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>
        @if($bookingType->booking_start_time || $bookingType->booking_end_time)
          <div class="mt-3 p-3 bg-blue-50 rounded border border-blue-200">
            <p class="text-sm text-blue-800">
              <strong>Current Availability:</strong> {{ $bookingType->time_availability }}
            </p>
          </div>
        @endif
      </div>

      {{-- Depot-Specific Durations and Time Restrictions --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Depot-Specific Settings</h3>
        <p class="text-sm text-gray-600 mb-4">Override the default duration and time restrictions for specific depots</p>
        <div class="space-y-4">
          @foreach($depots as $depot)
            <div class="border-l-4 border-blue-200 pl-4 py-2">
              <div class="font-medium text-sm mb-2">{{ $depot->name }}</div>
              <div class="grid grid-cols-3 gap-3">
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Duration (minutes)</label>
                  <input type="number"
                         name="depot_durations[{{ $depot->id }}]"
                         class="border rounded p-2 w-full"
                         value="{{ old('depot_durations.'.$depot->id, $depotDurations[$depot->id] ?? '') }}"
                         placeholder="Default"
                         min="1">
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                  <input type="time"
                         name="depot_start_times[{{ $depot->id }}]"
                         class="border rounded p-2 w-full"
                         value="{{ old('depot_start_times.'.$depot->id, isset($depotStartTimes[$depot->id]) && $depotStartTimes[$depot->id] ? \Carbon\Carbon::parse($depotStartTimes[$depot->id])->format('H:i') : '') }}">
                </div>
                <div>
                  <label class="block text-xs text-gray-600 mb-1">End Time</label>
                  <input type="time"
                         name="depot_end_times[{{ $depot->id }}]"
                         class="border rounded p-2 w-full"
                         value="{{ old('depot_end_times.'.$depot->id, isset($depotEndTimes[$depot->id]) && $depotEndTimes[$depot->id] ? \Carbon\Carbon::parse($depotEndTimes[$depot->id])->format('H:i') : '') }}">
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      {{-- Customer-Specific Durations and Time Restrictions --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Customer-Specific Settings</h3>
        <p class="text-sm text-gray-600 mb-4">Override durations and time restrictions for specific customers (optionally per depot)</p>
        <div class="space-y-4">
          @foreach($customers as $customer)
            <div class="border-l-4 border-green-200 pl-4 py-2">
              <div class="font-medium text-sm mb-3">{{ $customer->name }}</div>

              {{-- All depots --}}
              <div class="bg-gray-50 p-3 rounded mb-3">
                <div class="text-xs font-medium text-gray-700 mb-2">All Depots</div>
                <div class="grid grid-cols-3 gap-3">
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">Duration (min)</label>
                    <input type="number"
                           name="customer_durations[{{ $customer->id }}][all]"
                           class="border rounded p-2 w-full"
                           value="{{ old('customer_durations.'.$customer->id.'.all', $customerDurations[$customer->id.'_all']['duration'] ?? '') }}"
                           placeholder="Default"
                           min="1">
                  </div>
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">Start Time</label>
                    <input type="time"
                           name="customer_start_times[{{ $customer->id }}][all]"
                           class="border rounded p-2 w-full"
                           value="{{ old('customer_start_times.'.$customer->id.'.all', isset($customerDurations[$customer->id.'_all']['start_time']) && $customerDurations[$customer->id.'_all']['start_time'] ? \Carbon\Carbon::parse($customerDurations[$customer->id.'_all']['start_time'])->format('H:i') : '') }}">
                  </div>
                  <div>
                    <label class="block text-xs text-gray-600 mb-1">End Time</label>
                    <input type="time"
                           name="customer_end_times[{{ $customer->id }}][all]"
                           class="border rounded p-2 w-full"
                           value="{{ old('customer_end_times.'.$customer->id.'.all', isset($customerDurations[$customer->id.'_all']['end_time']) && $customerDurations[$customer->id.'_all']['end_time'] ? \Carbon\Carbon::parse($customerDurations[$customer->id.'_all']['end_time'])->format('H:i') : '') }}">
                  </div>
                </div>
              </div>

              {{-- Per depot --}}
              @foreach($depots as $depot)
                <div class="ml-4 mb-2">
                  <div class="text-xs text-gray-600 mb-1">{{ $depot->name }}</div>
                  <div class="grid grid-cols-3 gap-3">
                    <div>
                      <input type="number"
                             name="customer_durations[{{ $customer->id }}][{{ $depot->id }}]"
                             class="border rounded p-2 w-full text-sm"
                             value="{{ old('customer_durations.'.$customer->id.'.'.$depot->id, $customerDurations[$customer->id.'_'.$depot->id]['duration'] ?? '') }}"
                             placeholder="Default"
                             min="1">
                    </div>
                    <div>
                      <input type="time"
                             name="customer_start_times[{{ $customer->id }}][{{ $depot->id }}]"
                             class="border rounded p-2 w-full text-sm"
                             value="{{ old('customer_start_times.'.$customer->id.'.'.$depot->id, isset($customerDurations[$customer->id.'_'.$depot->id]['start_time']) && $customerDurations[$customer->id.'_'.$depot->id]['start_time'] ? \Carbon\Carbon::parse($customerDurations[$customer->id.'_'.$depot->id]['start_time'])->format('H:i') : '') }}">
                    </div>
                    <div>
                      <input type="time"
                             name="customer_end_times[{{ $customer->id }}][{{ $depot->id }}]"
                             class="border rounded p-2 w-full text-sm"
                             value="{{ old('customer_end_times.'.$customer->id.'.'.$depot->id, isset($customerDurations[$customer->id.'_'.$depot->id]['end_time']) && $customerDurations[$customer->id.'_'.$depot->id]['end_time'] ? \Carbon\Carbon::parse($customerDurations[$customer->id.'_'.$depot->id]['end_time'])->format('H:i') : '') }}">
                    </div>
                  </div>
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
