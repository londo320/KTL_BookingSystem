<!-- Streamlined Departure Modal -->
<div id="departureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
  <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
    <div class="mt-3">
      <!-- Modal Header -->
      <div class="flex justify-between items-center pb-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">🏁 Quick Vehicle Departure</h3>
        <button onclick="closeDepartureModal()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div id="departureSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
        <!-- Will be populated by JavaScript -->
      </div>

      <!-- Simplified Departure Form -->
      <form id="departureForm" method="POST" class="mt-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        
        <div class="grid grid-cols-1 gap-4">
          
          <!-- Quick Departure Scenarios -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-3">
              What happened? <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-3">
              <!-- Vehicle Left With Trailer -->
              <div>
                <input type="radio" name="departure_scenario" id="leftWithTrailer" value="completed_with_trailer" required
                       class="hidden peer">
                <label for="leftWithTrailer" 
                       class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                  <div class="text-3xl mb-2">🚛✅</div>
                  <div class="text-sm font-medium text-center">Job Complete<br>Left WITH trailer</div>
                </label>
              </div>
              
              <!-- Vehicle Left Without Trailer -->
              <div>
                <input type="radio" name="departure_scenario" id="leftWithoutTrailer" value="completed_dropped_trailer" required
                       class="hidden peer">
                <label for="leftWithoutTrailer" 
                       class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50">
                  <div class="text-3xl mb-2">🚛📦</div>
                  <div class="text-sm font-medium text-center">Unit Left<br>Trailer DROPPED</div>
                </label>
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Notes (optional)</label>
            <input type="text" name="departure_notes" id="departureNotes"
                   placeholder="e.g., Driver requested early departure, collection scheduled tomorrow..."
                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
          </div>

        </div>

        <!-- Departure Time Display -->
        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-green-800">📅 Departure Time</h4>
              <p class="text-green-700 font-semibold" id="departureTime">Recording now...</p>
            </div>
            <div class="text-2xl">🏁</div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-6 flex justify-end space-x-4">
          <button type="button" onclick="closeDepartureModal()" 
                  class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit" 
                  class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
            🏁 Record Departure
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentDepartureBookingId = null;

function openDepartureModal(bookingId, bookingRef, customer, vehicleReg, currentLocation, currentLocationName) {
  currentDepartureBookingId = bookingId;
  
  // Update departure summary
  document.getElementById('departureSummary').innerHTML = `
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
      <div>
        <strong>Booking:</strong><br>
        <span class="font-mono text-blue-600">${bookingRef}</span>
      </div>
      <div>
        <strong>Customer:</strong><br>
        ${customer}
      </div>
      <div>
        <strong>Vehicle:</strong><br>
        ${vehicleReg}
      </div>
    </div>
  `;

  // Update form action - determine correct prefix and route type
  let routePrefix = '/admin'; // default
  let routeSuffix = '/departure';
  
  if (window.location.pathname.includes('/app/')) {
    routePrefix = '/app';
  } else if (window.location.pathname.includes('/depot-admin/')) {
    routePrefix = '/depot-admin';
  } else if (window.location.pathname.includes('/admin/')) {
    routePrefix = '/admin';
  }
  
  // Check if this is a factory booking workflow
  if (window.location.pathname.includes('factory-booking-workflow')) {
    document.getElementById('departureForm').action = `${routePrefix}/factory-booking-workflow/${bookingId}/trailer-depart`;
  } else {
    document.getElementById('departureForm').action = `${routePrefix}/bookings/${bookingId}/departure`;
  }

  // Update departure time display
  updateDepartureTime();

  // Show modal
  document.getElementById('departureModal').classList.remove('hidden');
}

function closeDepartureModal() {
  document.getElementById('departureModal').classList.add('hidden');
  currentDepartureBookingId = null;
  document.getElementById('departureForm').reset();
}

function updateDepartureTime() {
  const now = new Date();
  const timeString = now.toLocaleString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
  document.getElementById('departureTime').textContent = `Recording: ${timeString}`;
}

// Update time every second
setInterval(() => {
  if (!document.getElementById('departureModal').classList.contains('hidden')) {
    updateDepartureTime();
  }
}, 1000);

// Close modal when clicking outside
document.getElementById('departureModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeDepartureModal();
  }
});

// Form submission with better error handling
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('departureForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      console.log('Form submit triggered');
      console.log('Form action:', this.action);
      
      // Quick validation
      const scenario = document.querySelector('input[name="departure_scenario"]:checked');
      
      if (!scenario) {
        alert('Please select what happened with the vehicle');
        return;
      }
      
      console.log('Selected scenario:', scenario.value);
      
      // Disable submit button to prevent double submission
      const submitBtn = this.querySelector('button[type="submit"]');
      const originalText = submitBtn.innerHTML;
      submitBtn.disabled = true;
      submitBtn.innerHTML = '🔄 Processing...';
      
      // Submit form
      const formData = new FormData(this);
      console.log('Form data entries:');
      for (let [key, value] of formData.entries()) {
        console.log(key, value);
      }
      
      fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        if (response.ok) {
          closeDepartureModal();
          alert('✅ Vehicle departure recorded successfully');
          window.location.reload();
        } else {
          return response.text().then(text => {
            console.error('Response error text:', text);
            alert('❌ Failed to process departure. Status: ' + response.status);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          });
        }
      })
      .catch(error => {
        console.error('Departure processing failed:', error);
        alert('❌ Failed to process departure: ' + error.message);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      });
    });
  }
});
</script><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/partials/departure-modal.blade.php ENDPATH**/ ?>