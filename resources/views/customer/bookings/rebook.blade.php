<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Rebook Booking #{{ $booking->id }}</h2>
      <a href="{{ route('customer.bookings.show', $booking) }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Booking
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto">
    
    {{-- Current Booking Info --}}
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Current Booking</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium">{{ $booking->slot->depot->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Slot</p>
          <p class="font-medium">
            {{ $booking->slot->start_at->format('D, d M Y - H:i') }} - {{ $booking->slot->end_at->format('H:i') }}
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Booking Type</p>
          <p class="font-medium">{{ $booking->bookingType->name ?? 'Not specified' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Rebook Count</p>
          <p class="font-medium">{{ $booking->rebook_count }} / {{ $maxRebooksPerBooking }}</p>
        </div>
      </div>
    </div>

    {{-- Customer Behavior Warnings --}}
    @if($customerBehaviorData['recent_rebooks'] > 2 || $customerBehaviorData['recent_cancellations'] > 3 || $customerBehaviorData['last_minute_actions'] > 1)
      <div class="mb-6 p-4 bg-orange-50 border border-orange-300 rounded-lg">
        <h3 class="text-lg font-semibold text-orange-800 mb-2">⚠️ Customer Behavior Notice</h3>
        <div class="space-y-1 text-sm text-orange-700">
          @if($customerBehaviorData['recent_rebooks'] > 2)
            <p>• You have rebooked {{ $customerBehaviorData['recent_rebooks'] }} times in the last 30 days</p>
          @endif
          @if($customerBehaviorData['recent_cancellations'] > 3)
            <p>• You have cancelled {{ $customerBehaviorData['recent_cancellations'] }} bookings in the last 30 days</p>
          @endif
          @if($customerBehaviorData['last_minute_actions'] > 1)
            <p>• You have made {{ $customerBehaviorData['last_minute_actions'] }} last-minute changes in the last 30 days</p>
          @endif
        </div>
        <p class="mt-2 text-xs text-orange-600">
          Please consider planning ahead to avoid frequent changes. Excessive rebooking may result in booking restrictions.
        </p>
      </div>
    @endif

    {{-- Rebooking Form --}}
    <div class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-xl font-semibold mb-4 text-gray-800">🔄 Select New Slot</h3>
      <p class="text-xs text-gray-500 mb-4">🌐 = Public, 🔒 = Customer Restricted &mdash; new slots must be for the same depot & booking type as the original booking.</p>

      <form id="rebookForm">
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
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select New Slot <span class="text-red-500">*</span>
            </label>
            <select name="new_slot_id" id="newSlotId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">← Click a date first</option>
            </select>

            <div class="mt-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Reason for Rebooking <span class="text-red-500">*</span>
              </label>
              <textarea name="reason" id="reason" required rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Please provide a reason for rebooking (e.g., schedule change, operational requirement, etc.)"></textarea>
            </div>
          </div>
        </div>

        <div class="flex space-x-3">
          <button type="submit"
                  class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            🔄 Rebook Booking
          </button>

          @if($booking->arrived_at)
            <span class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed"
                  title="This vehicle is already on site. Please contact the warehouse directly to cancel.">
              🔒 Cancel Unavailable — On Site
            </span>
          @else
            <button type="button" onclick="showCancelModal()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
              ❌ Cancel Booking Instead
            </button>
          @endif

          <a href="{{ route('customer.bookings.show', $booking) }}"
             class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
            Back to Booking
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Cancel Booking Modal --}}
  <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-red-800">Cancel Booking</h3>
      <form id="cancelForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation *</label>
          <textarea id="cancellationReason" required rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Please provide a reason for cancellation..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeCancelModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Cancel Booking
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Rebooking is locked to the original booking's depot & booking type —
    // only the date/slot can change, same date-sidebar UX as new bookings.
    const rebookDepotId = {{ $booking->slot->depot_id }};
    const rebookBookingTypeId = {{ $booking->booking_type_id ?? 'null' }};
    const rebookCurrentSlotId = {{ $booking->slot_id }};

    const rebookAvailabilityPreview = document.getElementById('rebook-availability-preview');
    const rebookSlotSelect = document.getElementById('newSlotId');
    let rebookSelectedDate = null;

    function rebookClearSlots(message) {
      rebookSlotSelect.innerHTML = `<option value="">${message || '– Choose your time slot –'}</option>`;
    }

    function rebookLoadAvailability() {
      rebookAvailabilityPreview.innerHTML = '<p class="text-gray-500 text-sm">🔄 Loading...</p>';

      fetch(`/customer/availability?depot_id=${rebookDepotId}&booking_type_id=${rebookBookingTypeId}`)
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
    }

    function rebookLoadSlots(date) {
      rebookSlotSelect.innerHTML = '<option value="">🔄 Loading slots...</option>';
      rebookSlotSelect.disabled = true;

      fetch(`/customer/slots?depot_id=${rebookDepotId}&date=${date}&booking_type_id=${rebookBookingTypeId}`)
        .then(response => response.json())
        .then(data => {
          rebookClearSlots();
          rebookSlotSelect.disabled = false;

          const slots = (data.slots || []).filter(slot => slot.id !== rebookCurrentSlotId);

          if (slots.length > 0) {
            slots.forEach(slot => {
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
    }

    window.rebookSelectDate = function(date) {
      rebookSelectedDate = date;
      rebookLoadAvailability();
      rebookLoadSlots(date);
    };

    rebookClearSlots('← Click a date above');
    rebookLoadAvailability();

    // Handle rebook form submission
    document.getElementById('rebookForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('_token', document.querySelector('input[name="_token"]').value);
      formData.append('new_slot_id', document.getElementById('newSlotId').value);
      formData.append('reason', document.getElementById('reason').value);
      
      fetch('{{ route("customer.bookings.rebook.store", $booking) }}', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Booking rebooked successfully!');
          window.location.href = data.redirect;
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error rebooking booking');
      });
    });

    // Cancel modal functions
    function showCancelModal() {
      document.getElementById('cancelModal').classList.remove('hidden');
      document.getElementById('cancelModal').classList.add('flex');
    }

    function closeCancelModal() {
      document.getElementById('cancelModal').classList.add('hidden');
      document.getElementById('cancelModal').classList.remove('flex');
    }

    // Handle cancel form submission
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const reason = document.getElementById('cancellationReason').value;
      
      fetch('{{ route("customer.bookings.cancel", $booking) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          cancellation_reason: reason
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeCancelModal();
          alert('Booking cancelled successfully!');
          window.location.href = '{{ route("customer.bookings.show", $booking) }}';
        } else {
          alert('Error cancelling booking: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error cancelling booking');
      });
    });

    // Close modal when clicking outside
    document.getElementById('cancelModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeCancelModal();
      }
    });
  </script>
</x-app-layout>