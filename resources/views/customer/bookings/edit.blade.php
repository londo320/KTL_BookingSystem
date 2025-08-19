<x-app-layout>
  @include('layouts.customer-nav')

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
          <a href="{{ route('customer.bookings.rebook.show', $booking) }}"
             class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-600 border-2 border-blue-600">
            🔄 {{ $hasArrived ? 'Rebook/Reject Instead' : 'Rebook Instead' }}
          </a>
          
          <button onclick="showCancelModal()" 
                  class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-lg hover:bg-red-600 border-2 border-red-600">
            ❌ {{ $hasArrived ? 'Cancel/Reject Booking' : 'Cancel Booking' }}
          </button>
        @endif
        
        <a href="{{ route('customer.bookings.show', $booking) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          📄 View Details
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <form action="{{ route('customer.bookings.update', $booking) }}" method="POST">
      @csrf
      @method('PUT')
      @include('customer.bookings._form_readonly')
      <div class="mt-4">
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Update Booking
        </button>
        <a href="{{ route('customer.bookings.index') }}"
           class="ml-2 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
          Cancel
        </a>
      </div>
    </form>
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