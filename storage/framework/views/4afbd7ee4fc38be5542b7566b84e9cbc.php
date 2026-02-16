<!-- Streamlined Arrival Modal -->
<div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
  <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
    <div class="mt-3">
      <!-- Modal Header -->
      <div class="flex justify-between items-center pb-4 border-b">
        <h3 class="text-lg font-semibold text-gray-900">🚛 Quick Vehicle Arrival</h3>
        <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Booking Summary -->
      <div id="bookingSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
        <!-- Will be populated by JavaScript -->
      </div>

      <!-- Simplified Arrival Form -->
      <form id="arrivalForm" method="POST" class="mt-6">
        <?php echo csrf_field(); ?>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <!-- Required Vehicle Registration -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Vehicle Registration <span class="text-red-500">*</span>
            </label>
            <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                   placeholder="e.g., AB12 CDE"
                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 text-lg font-mono">
          </div>

          <!-- Container Number -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
            <input type="text" id="containerNumber" name="container_number"
                   placeholder="e.g., CONT123456"
                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 font-mono">
          </div>

          <!-- Carrier Company -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Carrier Company <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="carrierCompany" 
                   name="carrier_name"
                   placeholder="Company name..."
                   required
                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            <input type="hidden" id="carrierId" name="carrier_id">
          </div>

          <!-- Tipping Type Selection -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-3">🚛 Tipping Type <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                <input type="radio" name="tipping_type" value="live_tip" required class="sr-only">
                <div class="w-full flex items-center">
                  <span class="text-2xl mr-3">🚛📦</span>
                  <div>
                    <div class="font-medium text-gray-900">Live Tip</div>
                    <div class="text-xs text-gray-600">Vehicle stays connected</div>
                  </div>
                </div>
              </label>
              <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 transition-colors">
                <input type="radio" name="tipping_type" value="drop" required class="sr-only">
                <div class="w-full flex items-center">
                  <span class="text-2xl mr-3">📦</span>
                  <div>
                    <div class="font-medium text-gray-900">Drop</div>
                    <div class="text-xs text-gray-600">Vehicle leaves trailer</div>
                  </div>
                </div>
              </label>
            </div>
          </div>

          <!-- Quick Assignment -->
          <div class="md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h4 class="font-medium text-blue-800 mb-3">🎯 Quick Assignment</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Auto Parking Area -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Drop Location</label>
                <select id="tippingLocation" name="tipping_location_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                  <option value="">– Auto-assign best location –</option>
                  <?php if(isset($tippingLocations)): ?>
                    <?php $__currentLoopData = $tippingLocations->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($location->id); ?>">
                        <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity() ?? 'Available'); ?>)
                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php endif; ?>
                </select>
              </div>

              <!-- Direct Bay (if available) -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Direct to Bay</label>
                <select id="tippingBay" name="tipping_bay_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                  <option value="">– Skip to parking area first –</option>
                  <?php if(isset($tippingBays)): ?>
                    <?php $__currentLoopData = $tippingBays->where('is_active', true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option value="<?php echo e($bay->id); ?>" <?php if($bay->is_occupied ?? false): echo 'disabled'; endif; ?>>
                        <?php echo e($bay->name); ?> 
                        <?php if($bay->is_occupied ?? false): ?>
                          - Occupied
                        <?php else: ?>
                          - Available
                        <?php endif; ?>
                      </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>
          </div>

        </div>

        <!-- Arrival Time Display -->
        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-medium text-green-800">📅 Arrival Time</h4>
              <p class="text-green-700 font-semibold" id="arrivalTime">Recording now...</p>
            </div>
            <div class="text-2xl">🚛</div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-6 flex justify-end space-x-4">
          <button type="button" onclick="closeArrivalModal()" 
                  class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit" 
                  class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
            🚛 Process Arrival
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
let currentBookingId = null;

function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, carrierCompany, expectedCases, expectedPallets, specialInstructions) {
  currentBookingId = bookingId;
  
  // Update booking summary
  document.getElementById('bookingSummary').innerHTML = `
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
      <div>
        <strong>Booking:</strong><br>
        <span class="font-mono text-blue-600">${bookingRef}</span>
      </div>
      <div>
        <strong>Customer:</strong><br>
        ${customer}
      </div>
      <div>
        <strong>Scheduled:</strong><br>
        ${scheduledTime}
      </div>
      <div>
        <strong>Expected:</strong><br>
        ${expectedCases} cases, ${expectedPallets} pallets
      </div>
    </div>
  `;

  // Update form action - determine correct prefix
  const routePrefix = window.location.pathname.includes('/depot-admin/') ? '/depot-admin' : '/admin';
  document.getElementById('arrivalForm').action = `${routePrefix}/bookings/${bookingId}/arrival`;

  // Pre-populate fields
  document.getElementById('vehicleRegistration').value = vehicleReg || '';
  document.getElementById('containerNumber').value = containerNum || '';
  document.getElementById('carrierCompany').value = carrierCompany || '';

  // Auto-select first available parking area if only one
  const parkingAreas = document.querySelectorAll('#tippingLocation option:not([value=""])');
  if (parkingAreas.length === 1) {
    document.getElementById('tippingLocation').value = parkingAreas[0].value;
  }

  // Update arrival time display
  updateArrivalTime();

  // Show modal
  document.getElementById('arrivalModal').classList.remove('hidden');
  
  // Focus on vehicle registration if empty, otherwise carrier
  setTimeout(() => {
    const vehicleRegInput = document.getElementById('vehicleRegistration');
    const carrierInput = document.getElementById('carrierCompany');

    if (!vehicleReg && vehicleRegInput) {
      vehicleRegInput.focus();
    } else if (carrierInput) {
      carrierInput.focus();
    }
  }, 100);
}

function closeArrivalModal() {
  document.getElementById('arrivalModal').classList.add('hidden');
  currentBookingId = null;
  document.getElementById('arrivalForm').reset();
  
  // Reset tipping type visual selection
  const tippingTypeLabels = document.querySelectorAll('label:has(input[name="tipping_type"])');
  tippingTypeLabels.forEach(label => {
    label.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
    label.classList.add('border-gray-200');
  });
}

function updateArrivalTime() {
  const now = new Date();
  const timeString = now.toLocaleString('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
  document.getElementById('arrivalTime').textContent = `Recording: ${timeString}`;
}

// Update time every second
setInterval(() => {
  if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
    updateArrivalTime();
  }
}, 1000);

// Close modal when clicking outside
document.getElementById('arrivalModal').addEventListener('click', function(e) {
  if (e.target === this) {
    closeArrivalModal();
  }
});

// Form submission
document.getElementById('arrivalForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Quick validation
  const vehicleReg = document.getElementById('vehicleRegistration').value.trim();
  const carrier = document.getElementById('carrierCompany').value.trim();
  const tippingType = document.querySelector('input[name="tipping_type"]:checked');
  
  if (!vehicleReg) {
    alert('Vehicle registration is required');
    const vehicleRegInput = document.getElementById('vehicleRegistration');
    if (vehicleRegInput) vehicleRegInput.focus();
    return;
  }

  if (!carrier) {
    alert('Carrier company is required');
    const carrierInput = document.getElementById('carrierCompany');
    if (carrierInput) carrierInput.focus();
    return;
  }
  
  if (!tippingType) {
    alert('Please select a tipping type (Live Tip or Drop)');
    return;
  }
  
  // Submit form
  const formData = new FormData(this);
  
  fetch(this.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(response => {
    if (response.ok) {
      closeArrivalModal();
      showNotification('✅ Vehicle arrived successfully', 'success');
      // Refresh the page to show updated status
      setTimeout(() => window.location.reload(), 1000);
    } else {
      throw new Error('Failed to process arrival');
    }
  })
  .catch(error => {
    console.error('Arrival processing failed:', error);
    showNotification('❌ Failed to process arrival', 'error');
  });
});

// Handle tipping type selection visual feedback
document.addEventListener('DOMContentLoaded', function() {
  const tippingTypeInputs = document.querySelectorAll('input[name="tipping_type"]');
  const tippingTypeLabels = document.querySelectorAll('label:has(input[name="tipping_type"])');
  
  tippingTypeInputs.forEach((input, index) => {
    input.addEventListener('change', function() {
      // Reset all labels
      tippingTypeLabels.forEach(label => {
        label.classList.remove('border-blue-500', 'bg-blue-50', 'border-green-500', 'bg-green-50');
        label.classList.add('border-gray-200');
      });
      
      // Highlight selected label
      const selectedLabel = tippingTypeLabels[index];
      selectedLabel.classList.remove('border-gray-200');
      
      if (this.value === 'live_tip') {
        selectedLabel.classList.add('border-blue-500', 'bg-blue-50');
      } else {
        selectedLabel.classList.add('border-green-500', 'bg-green-50');
      }
    });
  });
});
</script><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/partials/arrival-modal.blade.php ENDPATH**/ ?>