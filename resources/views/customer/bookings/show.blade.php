<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      {{-- Corporate Header with Logo --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          {{-- Company Logo/Brand --}}
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">WM</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Warehouse Manager</h1>
              <p class="text-sm text-gray-600">Customer Portal</p>
            </div>
          </div>
        </div>
        
        {{-- Booking Status Badge --}}
        <div class="text-right">
          <div class="text-sm text-gray-500">Booking Reference</div>
          <div class="text-2xl font-bold text-gray-900">#{{ $booking->id }}</div>
        </div>
      </div>
      
      {{-- Action Buttons - Organized by Category --}}
      <div class="flex flex-wrap gap-3">
        @php
          $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
          $hasArrived = $booking->arrived_at;
        @endphp
        
        {{-- Documents Group --}}
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Documents</span>
          <a href="{{ route('customer.bookings.download-pdf', $booking) }}"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📄 PDF
          </a>
          <button onclick="emailBookingPDF({{ $booking->id }})"
                  class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
            📧 Email
          </button>
        </div>
        
        {{-- Booking Management Group --}}
        <div class="flex items-center space-x-2 bg-blue-50 p-2 rounded-lg border border-blue-200">
          <span class="text-xs font-medium text-blue-700 uppercase">Management</span>
          
          @if($booking->cancelled_at)
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-400 text-white text-sm font-medium rounded-md cursor-not-allowed">
              ❌ Cancelled
            </span>
          @else
            @if(!$hasArrived && !$isLocked && auth()->user()->can('update', $booking))
              <a href="{{ route('customer.bookings.edit', $booking) }}"
                 class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                ✏️ Edit
              </a>
            @endif
            
            <a href="{{ route('customer.bookings.rebook.show', $booking) }}"
               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
              🔄 {{ $hasArrived ? 'Rebook/Reject' : 'Rebook' }}
            </a>
            
            @unless($booking->departed_at)
              <button onclick="showCancelModal()"
                      class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                ❌ {{ $hasArrived ? 'Cancel/Reject' : 'Cancel' }}
              </button>
            @else
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-400 text-white text-sm font-medium rounded-md cursor-not-allowed"
                    title="Booking has already departed">
                🚛 Departed
              </span>
            @endunless
          @endif
        </div>
        
        {{-- Information Group --}}
        @php
          $hasHistory = true; // Show for testing
          try {
            if (\Schema::hasTable('booking_history')) {
              $hasHistory = \App\Models\BookingHistory::where(function ($query) use ($booking) {
                $query->where('booking_id', $booking->id)
                      ->orWhere('original_booking_id', $booking->id);
              })->exists();
            }
          } catch (\Exception $e) {
            $hasHistory = true;
          }
        @endphp
        
        <div class="flex items-center space-x-2 bg-yellow-50 p-2 rounded-lg border border-yellow-200">
          <span class="text-xs font-medium text-yellow-700 uppercase">Information</span>
          @if($hasHistory)
            <a href="{{ route('customer.bookings.history', $booking) }}"
               class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
              📋 History
            </a>
          @endif
        </div>
        
        {{-- Navigation --}}
        <div class="flex items-center ml-auto">
          <a href="{{ route('customer.bookings.index') }}"
             class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
            ← Back to Bookings
          </a>
        </div>
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto">
    
    {{-- Success/Info Messages --}}
    @if(session('success'))
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <p class="text-green-800">{{ session('success') }}</p>
      </div>
    @endif
    
    @if(session('info'))
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <p class="text-blue-800">{{ session('info') }}</p>
      </div>
    @endif
    
    {{-- Status Banner --}}
    @if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked')))
      <div class="mb-6 p-4 bg-black text-white rounded-lg">
        <div class="flex items-center">
          <span class="text-white text-2xl mr-3">❌</span>
          <div>
            <h3 class="text-lg font-semibold text-white">Booking Cancelled</h3>
            <p class="text-white">
              Cancelled: {{ $booking->cancelled_at->format('d M Y, H:i') }}
              @if($booking->cancellation_reason)
                <br>Reason: {{ $booking->cancellation_reason }}
              @endif
            </p>
          </div>
        </div>
      </div>
    @elseif($hasArrived)
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-green-600 text-2xl mr-3">✅</span>
          <div>
            <h3 class="text-lg font-semibold text-green-800">Vehicle Arrived</h3>
            <p class="text-green-700">
              Arrived: {{ $booking->arrived_at->format('d M Y, H:i') }}
              @if($booking->departed_at)
                | Departed: {{ $booking->departed_at->format('d M Y, H:i') }}
              @else
                | Currently on-site
              @endif
            </p>
          </div>
        </div>
      </div>
    @elseif($isLocked)
      <div class="mb-6 p-4 bg-orange-100 border border-orange-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-orange-600 text-2xl mr-3">🔒</span>
          <div>
            <h3 class="text-lg font-semibold text-orange-800">Booking Locked</h3>
            <p class="text-orange-700">
              This booking's slot/PO details are locked and can no longer be edited. Cut-off time: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
              You can still update your expected arrival time below.
            </p>
          </div>
        </div>
      </div>
    @else
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-blue-600 text-2xl mr-3">📅</span>
          <div>
            <h3 class="text-lg font-semibold text-blue-800">Booking Confirmed</h3>
            <p class="text-blue-700">Your booking is confirmed and can be edited until the cut-off time.</p>
          </div>
        </div>
      </div>
    @endif

    {{-- Update ETA — always available (regardless of edit-lock status) until
         the vehicle has arrived or the booking is cancelled --}}
    @if(!$hasArrived && !$booking->isCancelled())
      <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
        <h3 class="text-lg font-semibold text-purple-900 mb-2">📞 Expected Arrival Time</h3>
        @error('estimated_arrival')
          <p class="text-red-600 text-sm mb-2">{{ $message }}</p>
        @enderror
        <form method="POST" action="{{ route('customer.bookings.eta.update', $booking) }}" class="flex flex-wrap items-end gap-3">
          @csrf
          <div>
            <label class="block text-xs font-medium text-purple-800 mb-1">If different from the slot time</label>
            <input type="datetime-local" name="estimated_arrival"
                   value="{{ old('estimated_arrival', $booking->estimated_arrival ? $booking->estimated_arrival->format('Y-m-d\TH:i') : '') }}"
                   class="border-purple-300 rounded-lg bg-white">
          </div>
          <button type="submit"
                  class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition-colors">
            Update ETA
          </button>
        </form>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      {{-- Slot & Location Details --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📍 Slot & Location</h3>
        
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Depot</label>
            <p class="text-lg">{{ $booking->slot->depot->name }}</p>
            @if($booking->slot->depot->location)
              <p class="text-sm text-gray-500">{{ $booking->slot->depot->location }}</p>
            @endif
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Date & Time</label>
            <p class="text-lg">
              {{ $booking->slot->start_at->format('l, d F Y') }}
            </p>
            <p class="text-lg font-semibold text-blue-600">
              {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}
            </p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Booking Type</label>
            <p class="text-lg">{{ $booking->bookingType->name ?? 'Not specified' }}</p>
          </div>
          
          @if($booking->reference)
            <div>
              <label class="text-sm font-medium text-gray-600">Reference</label>
              <p class="text-lg font-mono">{{ $booking->reference }}</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Load Details --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📦 Load Details</h3>
        
        <div class="space-y-4">
          {{-- PO Numbers and Load Details --}}
          @if($booking->poNumbers->count() > 0)
            @foreach($booking->poNumbers as $po)
              <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                  <h4 class="font-medium text-gray-800">PO: {{ $po->po_number }}</h4>
                  @if($po->hasVariance())
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Has Variance</span>
                  @endif
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="font-medium text-gray-600">Expected:</span>
                    <div class="mt-1 text-gray-800">
                      {!! str_replace('<br>', '<br>', $po->expected_summary_text) !!}
                    </div>
                  </div>
                  
                  @if($po->total_actual_units > 0 || $po->total_actual_pallets > 0)
                    <div>
                      <span class="font-medium text-gray-600">Actual:</span>
                      <div class="mt-1 text-green-600">
                        {!! str_replace('<br>', '<br>', $po->actual_summary_text) !!}
                      </div>
                    </div>
                  @endif
                </div>
                
                @if($po->hasVariance())
                  <div class="mt-3 p-2 bg-yellow-50 rounded border border-yellow-200">
                    <div class="text-xs font-medium text-yellow-800 mb-1">Variances:</div>
                    @if($po->total_unit_variance != 0)
                      <div class="text-xs text-yellow-700">
                        Units: {{ $po->total_unit_variance > 0 ? '+' : '' }}{{ $po->total_unit_variance }}
                      </div>
                    @endif
                    @if($po->total_pallet_variance != 0)
                      <div class="text-xs text-yellow-700">
                        Pallets: {{ $po->total_pallet_variance > 0 ? '+' : '' }}{{ $po->total_pallet_variance }}
                      </div>
                    @endif
                    @if($po->hasTypeVariances())
                      <div class="text-xs text-yellow-700">
                        Type variances: {{ implode(', ', $po->type_variances) }}
                      </div>
                    @endif
                  </div>
                @endif
              </div>
            @endforeach
          @else
            <div class="text-gray-500 text-center py-4">
              No PO numbers specified for this booking
            </div>
          @endif
          
          @if($booking->container_size)
            <div>
              <label class="text-sm font-medium text-gray-600">Container Size</label>
              <p class="text-lg">{{ number_format($booking->container_size) }} kg</p>
            </div>
          @endif
          
          @if($booking->load_type)
            <div>
              <label class="text-sm font-medium text-gray-600">Load Type</label>
              <p class="text-lg">{{ $booking->load_type }}</p>
            </div>
          @endif
          
          @if($booking->hazmat)
            <div>
              <label class="text-sm font-medium text-gray-600">Special Requirements</label>
              <p class="text-lg text-red-600 font-semibold">⚠️ Hazardous Materials (HAZMAT)</p>
            </div>
          @endif
          
          @if($booking->temperature_requirements)
            <div>
              <label class="text-sm font-medium text-gray-600">Temperature Requirements</label>
              <p class="text-lg">{{ $booking->temperature_requirements }}</p>
            </div>
          @endif
        </div>
      </div>

      {{-- Transportation Details --}}
      @if($booking->vehicle_registration || $booking->driver_name || $booking->carrier_company)
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Transportation</h3>
          
          <div class="space-y-3">
            @if($booking->vehicle_registration)
              <div>
                <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                <p class="text-lg font-mono">{{ $booking->vehicle_registration }}</p>
              </div>
            @endif
            
            @if($booking->container_number)
              <div>
                <label class="text-sm font-medium text-gray-600">Container Number</label>
                <p class="text-lg font-mono">{{ $booking->container_number }}</p>
              </div>
            @endif
            
            
            @if($booking->carrier_company)
              <div>
                <label class="text-sm font-medium text-gray-600">Carrier Company</label>
                <p class="text-lg">{{ $booking->carrier_company }}</p>
              </div>
            @endif
            
            @if($booking->estimated_arrival)
              <div>
                <label class="text-sm font-medium text-gray-600">Estimated Arrival</label>
                <p class="text-lg">{{ $booking->estimated_arrival->format('d M Y, H:i') }}</p>
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- Additional Information --}}
      @if($booking->special_instructions || $booking->notes)
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">📝 Additional Information</h3>
          
          <div class="space-y-3">
            @if($booking->special_instructions)
              <div>
                <label class="text-sm font-medium text-gray-600">Special Instructions</label>
                <p class="text-base leading-relaxed">{{ $booking->special_instructions }}</p>
              </div>
            @endif
            
            @if($booking->notes)
              <div>
                <label class="text-sm font-medium text-gray-600">Notes</label>
                <p class="text-base leading-relaxed">{{ $booking->notes }}</p>
              </div>
            @endif
          </div>
        </div>
      @endif

      {{-- Arrival Information (if arrived) --}}
      @if($hasArrived)
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
          <h3 class="text-xl font-semibold mb-4 text-green-800">✅ Arrival Information</h3>
          
          <div class="space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Arrived At</label>
              <p class="text-lg">{{ $booking->arrived_at->format('l, d F Y - H:i') }}</p>
            </div>
            
            @if($booking->departed_at)
              <div>
                <label class="text-sm font-medium text-gray-600">Departed At</label>
                <p class="text-lg">{{ $booking->departed_at->format('l, d F Y - H:i') }}</p>
              </div>
              
              <div>
                <label class="text-sm font-medium text-gray-600">Time On-Site</label>
                <p class="text-lg">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</p>
              </div>
            @else
              <div class="p-3 bg-blue-100 rounded border border-blue-300">
                <p class="text-blue-800 font-medium">🚛 Currently on-site</p>
              </div>
            @endif
          </div>
        </div>
      @endif

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

  {{-- Email PDF Modal --}}
  <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4">Email Booking PDF</h3>
      <form id="emailForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <div class="flex space-x-2">
            <input type="email" id="emailAddress" required
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Enter email address">
            <button type="button" onclick="useMyEmail()"
                    class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 whitespace-nowrap">
              Use My Email
            </button>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
          <textarea id="emailMessage" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Add a personal message..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeEmailModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Send PDF
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function emailBookingPDF(bookingId) {
      document.getElementById('emailModal').classList.remove('hidden');
      document.getElementById('emailModal').classList.add('flex');
    }

    function closeEmailModal() {
      document.getElementById('emailModal').classList.add('hidden');
      document.getElementById('emailModal').classList.remove('flex');
    }

    function useMyEmail() {
      document.getElementById('emailAddress').value = '{{ auth()->user()->email }}';
    }

    function showCancelModal() {
      document.getElementById('cancelModal').classList.remove('hidden');
      document.getElementById('cancelModal').classList.add('flex');
    }

    function closeCancelModal() {
      document.getElementById('cancelModal').classList.add('hidden');
      document.getElementById('cancelModal').classList.remove('flex');
    }

    document.getElementById('emailForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const email = document.getElementById('emailAddress').value;
      const message = document.getElementById('emailMessage').value;
      
      // Send request to email endpoint
      fetch('{{ route("customer.bookings.email-pdf", $booking) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          email: email,
          message: message
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeEmailModal();
          alert('PDF sent successfully!');
        } else {
          alert('Error sending PDF: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error sending PDF');
      });
    });

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
          location.reload(); // Refresh to show cancelled status
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
    document.getElementById('emailModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEmailModal();
      }
    });

    document.getElementById('cancelModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeCancelModal();
      }
    });
  </script>
</x-app-layout>