
<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">🔄 Rebook Booking: {{ $booking->booking_reference }}</h2>
        <p class="text-sm text-gray-600 mt-1">Rebook this booking to a different time slot</p>
      </div>
      <a href="{{ route('app.bookings.show', $booking) }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        ← Back to Booking
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    
    {{-- Current Booking Info --}}
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Current Booking Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium">{{ $booking->customer->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Slot</p>
          <p class="font-medium">{{ $booking->slot->start_at->format('D, d M Y - H:i') }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium">{{ $booking->slot->depot->name ?? 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Container</p>
          <p class="font-medium">{{ $booking->container_number ?? 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Driver</p>
          <p class="font-medium">{{ $booking->driver_name ?? 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Rebook Count</p>
          <p class="font-medium">{{ $booking->rebook_count }} / {{ $maxRebooksPerBooking }}</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        {{-- Restrictions/Warnings --}}
        @if($restrictions['blocked'])
          <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
              <div class="text-red-600 mr-3">🚫</div>
              <div>
                <h3 class="text-lg font-semibold text-red-800">Rebooking Blocked</h3>
                <p class="text-red-700">{{ $restrictions['blocked'] }}</p>
              </div>
            </div>
          </div>
        @else
          @if($restrictions['warning'])
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
              <div class="flex items-center">
                <div class="text-orange-600 mr-3">⚠️</div>
                <div>
                  <h3 class="text-lg font-semibold text-orange-800">Warning</h3>
                  <p class="text-orange-700">{{ $restrictions['warning'] }}</p>
                </div>
              </div>
            </div>
          @endif

          {{-- Rebook Form --}}
          <div class="bg-white p-6 rounded-lg shadow border">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">🔄 Select New Slot</h3>
            <p class="text-xs text-gray-500 mb-4">Same depot & booking type as the original booking — pick any available date.</p>

            <form action="{{ route('app.bookings.rebook.store', $booking) }}" method="POST" id="rebookForm">
              @csrf

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                {{-- Date Sidebar --}}
                <div class="md:col-span-1">
                  <label class="block text-sm font-medium text-gray-700 mb-2">📅 Available Dates</label>
                  <div id="rebook-availability-preview" class="border border-gray-200 rounded-lg p-3 max-h-96 overflow-y-auto">
                    <p class="text-gray-500 text-sm">🔄 Loading available dates...</p>
                  </div>
                </div>

                {{-- Slot Select --}}
                <div class="md:col-span-2">
                  <label for="new_slot_id" class="block text-sm font-medium text-gray-700 mb-2">
                    New Slot <span class="text-red-500">*</span>
                  </label>
                  <select name="new_slot_id" id="new_slot_id" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('new_slot_id') border-red-300 @enderror">
                    <option value="">← Click a date first</option>
                  </select>
                  @error('new_slot_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                  @enderror

                  <div class="mt-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                      Reason for Rebooking <span class="text-red-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" required rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('reason') border-red-300 @enderror"
                              placeholder="Please provide a reason for rebooking...">{{ old('reason') }}</textarea>
                    @error('reason')
                      <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="flex space-x-3">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  🔄 Rebook Booking
                </button>
                <a href="{{ route('app.bookings.show', $booking) }}"
                   class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                  Cancel
                </a>
              </div>
            </form>
          </div>
        @endif
      </div>

      <div class="lg:col-span-1">
        {{-- Customer Behavior Stats --}}
        <div class="bg-white p-6 rounded-lg shadow border">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Customer Behavior (30 days)</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600">{{ $customerStats['total_rebooks_30days'] }}</div>
              <div class="text-sm text-gray-600">Total Rebooks</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600">{{ $customerStats['last_minute_rebooks_30days'] }}</div>
              <div class="text-sm text-gray-600">Last Minute</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600">{{ $customerStats['total_cancellations_30days'] }}</div>
              <div class="text-sm text-gray-600">Cancellations</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600">{{ $customerStats['avg_hours_notice'] }}h</div>
              <div class="text-sm text-gray-600">Avg Notice</div>
            </div>
          </div>
          
          <a href="{{ route('app.customer-behavior.show', $booking->customer) }}" 
             class="block w-full mt-4 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700">
            📈 View Customer Analysis
          </a>
        </div>

        {{-- Cancel Booking --}}
        <div class="mt-6 bg-white p-6 rounded-lg shadow border">
          <h3 class="text-lg font-semibold text-red-800 mb-3">🚫 Cancel Booking</h3>
          <p class="text-gray-600 text-sm mb-4">If you need to cancel this booking instead of rebooking it.</p>
          
          <button type="button" onclick="showCancelModal()"
                  class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            🚫 Cancel Booking
          </button>
        </div>
      </div>
    </div>

    {{-- Booking History --}}
    @if($booking->history->count() > 0)
    <div class="mt-6 bg-white p-6 rounded-lg shadow border">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Recent History</h3>
      <div class="space-y-4">
        @foreach($booking->history->take(5) as $history)
        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
          <div class="flex-shrink-0">
            <div class="w-3 h-3 rounded-full {{ $history->action === 'created' ? 'bg-green-500' : ($history->action === 'rebooked' ? 'bg-orange-500' : 'bg-red-500') }}"></div>
          </div>
          <div class="flex-1">
            <h4 class="font-medium text-gray-900">{{ ucfirst($history->action) }}</h4>
            <p class="text-sm text-gray-600">{{ $history->reason ?? 'No reason provided' }}</p>
            <p class="text-xs text-gray-500">
              {{ $history->created_at->format('M j, Y g:i A') }} by {{ $history->user->name ?? 'System' }}
            </p>
          </div>
        </div>
        @endforeach
      </div>
      <a href="{{ route('app.bookings.history', $booking) }}" 
         class="inline-block mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
        View Complete History
      </a>
    </div>
    @endif
  </div>

  {{-- Cancel Booking Modal --}}
  <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-red-800">Cancel Booking</h3>
      <form action="{{ route('app.bookings.cancel', $booking) }}" method="POST">
        @csrf
        <div class="mb-4">
          <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
            Reason for Cancellation <span class="text-red-500">*</span>
          </label>
          <textarea name="cancellation_reason" id="cancellation_reason" required rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Please provide a reason for cancellation..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeCancelModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
            Close
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Cancel Booking
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Date-sidebar driven slot picker for rebooking (only present when
    // rebooking isn't blocked by restrictions)
    const rebookSlotSelect = document.getElementById('new_slot_id');
    const rebookAvailabilityPreview = document.getElementById('rebook-availability-preview');

    if (rebookSlotSelect && rebookAvailabilityPreview) {
      let rebookSelectedDate = null;

      const rebookClearSlots = (message) => {
        rebookSlotSelect.innerHTML = `<option value="">${message || '– Choose your time slot –'}</option>`;
      };

      const rebookLoadAvailability = () => {
        rebookAvailabilityPreview.innerHTML = '<p class="text-gray-500 text-sm">🔄 Loading...</p>';

        fetch('{{ route("app.bookings.rebook.availability", $booking) }}')
          .then(response => response.json())
          .then(data => {
            if (data.dates && data.dates.length > 0) {
              let html = '<div class="space-y-2">';
              data.dates.forEach(dateInfo => {
                const date = new Date(dateInfo.date);
                const isSelected = dateInfo.date === rebookSelectedDate;
                const buttonClass = isSelected
                  ? 'w-full text-left p-2 rounded bg-blue-100 border border-blue-300 text-blue-800 text-sm'
                  : 'w-full text-left p-2 rounded bg-gray-50 hover:bg-gray-100 border text-sm transition-colors';

                html += `
                  <button type="button" onclick="rebookSelectDate('${dateInfo.date}')" class="${buttonClass}">
                    <div class="font-medium">${date.toLocaleDateString('en-GB', { weekday: 'short', month: 'short', day: 'numeric' })}</div>
                    <div class="text-xs text-gray-600">${dateInfo.available_slots} slot${dateInfo.available_slots !== 1 ? 's' : ''} available</div>
                  </button>
                `;
              });
              html += '</div>';
              rebookAvailabilityPreview.innerHTML = html;
            } else {
              rebookAvailabilityPreview.innerHTML = '<p class="text-gray-500 text-sm">📭 No available slots found</p>';
            }
          })
          .catch(error => {
            console.error('Error loading availability:', error);
            rebookAvailabilityPreview.innerHTML = '<p class="text-red-500 text-sm">❌ Error loading availability</p>';
          });
      };

      const rebookLoadSlots = (date) => {
        rebookSlotSelect.innerHTML = '<option value="">🔄 Loading slots...</option>';
        rebookSlotSelect.disabled = true;

        fetch(`{{ route('app.bookings.rebook.slots', $booking) }}?date=${date}`)
          .then(response => response.json())
          .then(data => {
            rebookClearSlots();
            rebookSlotSelect.disabled = false;

            if (data.slots && data.slots.length > 0) {
              data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot.id;
                option.textContent = `${slot.time_range} ${slot.is_restricted ? '🔒' : '🌐'} ${slot.customers_info}`;
                rebookSlotSelect.appendChild(option);
              });
            } else {
              const option = document.createElement('option');
              option.value = '';
              option.textContent = '📭 No other slots available for this date';
              option.disabled = true;
              rebookSlotSelect.appendChild(option);
            }
          })
          .catch(error => {
            console.error('Error loading slots:', error);
            rebookClearSlots('❌ Error loading slots');
            rebookSlotSelect.disabled = false;
          });
      };

      window.rebookSelectDate = function(date) {
        rebookSelectedDate = date;
        rebookLoadAvailability();
        rebookLoadSlots(date);
      };

      rebookClearSlots('← Click a date above');
      rebookLoadAvailability();
    }

    // Cancel modal functions
    function showCancelModal() {
      document.getElementById('cancelModal').classList.remove('hidden');
      document.getElementById('cancelModal').classList.add('flex');
    }

    function closeCancelModal() {
      document.getElementById('cancelModal').classList.add('hidden');
      document.getElementById('cancelModal').classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeCancelModal();
      }
    });
  </script>
</x-warehouse-layout>