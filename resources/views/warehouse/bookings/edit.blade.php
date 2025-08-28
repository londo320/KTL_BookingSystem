{{-- resources/views/admin/bookings/edit.blade.php --}}
<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Edit Booking #{{ $booking->id }}</h2>
      <div class="flex space-x-2">
        @php
          $hasArrived = $booking->arrived_at;
        @endphp
        @if($booking->cancelled_at)
          <button disabled
                  class="px-4 py-2 bg-black text-white font-semibold rounded-lg border-2 border-black cursor-not-allowed opacity-50">
            🔄 Cannot Rebook - Cancelled
          </button>
          <button disabled
                  class="px-4 py-2 bg-black text-white font-semibold rounded-lg border-2 border-black cursor-not-allowed opacity-50">
            ❌ Already Cancelled
          </button>
        @else
          {{-- Show rebook/cancel buttons for active bookings --}}
          <a href="{{ route('app.bookings.rebook.show', $booking) }}"
             class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-600 border-2 border-blue-600">
            🔄 {{ $hasArrived ? 'Rebook/Reject Instead' : 'Rebook Instead' }}
          </a>
          <button onclick="showCancelModal()" 
                  class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-lg hover:bg-red-600 border-2 border-red-600">
            ❌ {{ $hasArrived ? 'Cancel/Reject Booking' : 'Cancel Booking' }}
          </button>
        @endif
        <a href="{{ route('app.bookings.show', $booking) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          📄 View Details
        </a>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <form action="{{ route('app.bookings.update', $booking) }}" method="POST">
      @csrf
      @method('PUT')
      @include('admin.bookings._form')
      <div class="mt-6 flex space-x-3">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
          Update Booking
        </button>
        <a href="{{ route('app.bookings.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded">
           Cancel
        </a>
      </div>
    </form>
  </div>
  {{-- Unbook Vehicle Section (for arrived bookings only) --}}
  @if($booking->arrived_at && !$booking->departed_at)
    <div class="mt-6 max-w-3xl mx-auto">
      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <span class="text-2xl">⚠️</span>
          </div>
          <div class="ml-3 flex-1">
            <h3 class="text-lg font-medium text-yellow-800 mb-2">Vehicle Arrival Issues</h3>
            <p class="text-sm text-yellow-700 mb-4">
              If this vehicle was assigned to the wrong booking reference, you can unbook it here. 
              This will clear all arrival details and reset the booking to scheduled status.
            </p>
            <div class="bg-white rounded border border-yellow-300 p-4">
              <h4 class="font-medium text-gray-800 mb-2">Current Vehicle Details:</h4>
              <div class="text-sm text-gray-600 space-y-1">
                @if($booking->vehicle_registration)
                  <div><strong>Registration:</strong> {{ $booking->vehicle_registration }}</div>
                @endif
                @if($booking->carrier_company)
                  <div><strong>Carrier:</strong> {{ $booking->carrier_company }}</div>
                @endif
                @if($booking->container_number)
                  <div><strong>Container:</strong> {{ $booking->container_number }}</div>
                @endif
                @if($booking->waiting_area_location)
                  <div><strong>Parking Area:</strong> {{ $booking->waiting_area_location }}</div>
                @endif
                <div><strong>Arrived:</strong> {{ $booking->arrived_at->format('d M Y, H:i') }}</div>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
              <div class="text-sm text-yellow-700">
                <strong>Warning:</strong> This action cannot be undone and will free up any assigned parking areas or bays.
              </div>
              <form method="POST" action="{{ 
                request()->route()->getPrefix() === 'depot-admin' 
                  ? route('depot.bookings.unbook', $booking) 
                  : route('app.bookings.unbook', $booking) 
              }}" class="inline">
                @csrf
                <button type="submit" 
                        onclick="return confirm('⚠️ CONFIRM UNBOOK VEHICLE ⚠️\n\nThis will:\n• Clear all arrival details\n• Reset booking to scheduled status\n• Free up assigned parking areas/bays\n• Remove vehicle registration and carrier info\n\nAre you absolutely sure you want to unbook this vehicle?')"
                        class="px-6 py-2 bg-orange-600 text-white font-medium rounded hover:bg-orange-700 border-2 border-orange-700">
                  🔄 Unbook Vehicle
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
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
    function showCancelModal() {
      document.getElementById('cancelModal').classList.remove('hidden');
      document.getElementById('cancelModal').classList.add('flex');
    }
    function closeCancelModal() {
      document.getElementById('cancelModal').classList.add('hidden');
      document.getElementById('cancelModal').classList.remove('flex');
    }
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const reason = document.getElementById('cancellationReason').value;
      // Send request to cancel endpoint
      fetch('{{ route("app.bookings.cancel", $booking) }}', {
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
          window.location.href = '{{ route("app.bookings.show", $booking) }}';
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
</x-warehouse-layout>
