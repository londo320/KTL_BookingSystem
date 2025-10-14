
<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Edit Booking #<?php echo e($booking->id); ?></h2>
      <div class="flex space-x-2">
        <?php
          $hasArrived = $booking->arrived_at;
        ?>
        <?php if($booking->cancelled_at): ?>
          <button disabled
                  class="px-4 py-2 bg-black text-white font-semibold rounded-lg border-2 border-black cursor-not-allowed opacity-50">
            🔄 Cannot Rebook - Cancelled
          </button>
          <button disabled
                  class="px-4 py-2 bg-black text-white font-semibold rounded-lg border-2 border-black cursor-not-allowed opacity-50">
            ❌ Already Cancelled
          </button>
        <?php else: ?>
          
          <a href="<?php echo e(route('app.bookings.rebook.show', $booking)); ?>"
             class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-600 border-2 border-blue-600">
            🔄 <?php echo e($hasArrived ? 'Rebook/Reject Instead' : 'Rebook Instead'); ?>

          </a>
          <button onclick="showCancelModal()" 
                  class="px-4 py-2 bg-red-500 text-white font-semibold rounded-lg shadow-lg hover:bg-red-600 border-2 border-red-600">
            ❌ <?php echo e($hasArrived ? 'Cancel/Reject Booking' : 'Cancel Booking'); ?>

          </button>
        <?php endif; ?>
        <a href="<?php echo e(route('app.bookings.show', $booking)); ?>"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          📄 View Details
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <form id="bookingEditForm" action="<?php echo e(route('app.bookings.update', $booking)); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('admin.bookings._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      <div class="mt-6 flex space-x-3">
        <button id="updateBookingBtn" type="submit" class="px-4 py-2 bg-green-600 text-white rounded">
          Update Booking
        </button>
        <a href="<?php echo e(route('app.bookings.index')); ?>"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded">
           Cancel
        </a>
      </div>
    </form>
  </div>
  
  <?php if($booking->arrived_at && !$booking->departed_at): ?>
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
                <?php if($booking->vehicle_registration): ?>
                  <div><strong>Registration:</strong> <?php echo e($booking->vehicle_registration); ?></div>
                <?php endif; ?>
                <?php if($booking->carrier_company): ?>
                  <div><strong>Carrier:</strong> <?php echo e($booking->carrier_company); ?></div>
                <?php endif; ?>
                <?php if($booking->container_number): ?>
                  <div><strong>Container:</strong> <?php echo e($booking->container_number); ?></div>
                <?php endif; ?>
                <?php if($booking->waiting_area_location): ?>
                  <div><strong>Parking Area:</strong> <?php echo e($booking->waiting_area_location); ?></div>
                <?php endif; ?>
                <div><strong>Arrived:</strong> <?php echo e($booking->arrived_at->format('d M Y, H:i')); ?></div>
              </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
              <div class="text-sm text-yellow-700">
                <strong>Warning:</strong> This action cannot be undone and will free up any assigned parking areas or bays.
              </div>
              <form method="POST" action="<?php echo e(request()->route()->getPrefix() === 'depot-admin' 
                  ? route('depot.bookings.unbook', $booking) 
                  : route('app.bookings.unbook', $booking)); ?>" class="inline">
                <?php echo csrf_field(); ?>
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
  <?php endif; ?>
  
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
      fetch('<?php echo e(route("app.bookings.cancel", $booking)); ?>', {
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
          window.location.href = '<?php echo e(route("app.bookings.show", $booking)); ?>';
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

    // Debug form submission
    const form = document.getElementById('bookingEditForm');
    console.log('Form element found:', form);

    // Debug button click
    const submitButton = document.getElementById('updateBookingBtn');
    console.log('Submit button found:', submitButton);

    if (submitButton) {
      submitButton.addEventListener('click', function(e) {
        console.log('Submit button clicked!');
        console.log('Button type:', this.type);

        // Prevent default to check validity first
        e.preventDefault();
        e.stopPropagation();

        // Check form validity
        const isValid = form.checkValidity();
        console.log('Form valid?', isValid);

        const invalidElements = form.querySelectorAll(':invalid');
        console.log('Invalid elements count:', invalidElements.length);

        if (invalidElements.length > 0) {
          invalidElements.forEach(el => {
            console.log('Invalid field:', {
              name: el.name,
              id: el.id,
              type: el.type,
              value: el.value,
              message: el.validationMessage,
              element: el
            });
          });

          // Show validation messages
          form.reportValidity();
        } else {
          // Form is valid, submit it
          console.log('✅ Form is valid, submitting now...');
          form.submit();
        }
      });
    }

    form.addEventListener('submit', function(e) {
      console.log('✅ Form submit event fired!');
      console.log('Form action:', this.action);
      console.log('Form method:', this.method);
      console.log('Form will submit now...');

      // Check form validity
      if (!this.checkValidity()) {
        console.log('Form is invalid!');
        const invalidElements = this.querySelectorAll(':invalid');
        console.log('Invalid elements:', invalidElements);
        invalidElements.forEach(el => {
          console.log('Invalid field:', el.name, el.validationMessage);
        });
      } else {
        console.log('Form is valid, submitting...');
      }
    }, true);
  </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/edit.blade.php ENDPATH**/ ?>