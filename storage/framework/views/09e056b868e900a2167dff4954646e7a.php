<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
  <?php echo $__env->make('layouts.customer-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Rebook Booking #<?php echo e($booking->id); ?></h2>
      <a href="<?php echo e(route('customer.bookings.show', $booking)); ?>"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Booking
      </a>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-4xl mx-auto">
    
    
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Current Booking</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium"><?php echo e($booking->slot->depot->name); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Slot</p>
          <p class="font-medium">
            <?php echo e($booking->slot->start_at->format('D, d M Y - H:i')); ?> - <?php echo e($booking->slot->end_at->format('H:i')); ?>

          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Booking Type</p>
          <p class="font-medium"><?php echo e($booking->bookingType->name ?? 'Not specified'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Rebook Count</p>
          <p class="font-medium"><?php echo e($booking->rebook_count); ?> / 3</p>
        </div>
      </div>
    </div>

    
    <?php if($customerBehaviorData['recent_rebooks'] > 2 || $customerBehaviorData['recent_cancellations'] > 3 || $customerBehaviorData['last_minute_actions'] > 1): ?>
      <div class="mb-6 p-4 bg-orange-50 border border-orange-300 rounded-lg">
        <h3 class="text-lg font-semibold text-orange-800 mb-2">⚠️ Customer Behavior Notice</h3>
        <div class="space-y-1 text-sm text-orange-700">
          <?php if($customerBehaviorData['recent_rebooks'] > 2): ?>
            <p>• You have rebooked <?php echo e($customerBehaviorData['recent_rebooks']); ?> times in the last 30 days</p>
          <?php endif; ?>
          <?php if($customerBehaviorData['recent_cancellations'] > 3): ?>
            <p>• You have cancelled <?php echo e($customerBehaviorData['recent_cancellations']); ?> bookings in the last 30 days</p>
          <?php endif; ?>
          <?php if($customerBehaviorData['last_minute_actions'] > 1): ?>
            <p>• You have made <?php echo e($customerBehaviorData['last_minute_actions']); ?> last-minute changes in the last 30 days</p>
          <?php endif; ?>
        </div>
        <p class="mt-2 text-xs text-orange-600">
          Please consider planning ahead to avoid frequent changes. Excessive rebooking may result in booking restrictions.
        </p>
      </div>
    <?php endif; ?>

    
    <div class="bg-white p-6 rounded-lg shadow">
      <h3 class="text-xl font-semibold mb-4 text-gray-800">🔄 Select New Slot</h3>
      
      <?php if($availableSlots->isEmpty()): ?>
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
          <p class="text-red-800">
            ❌ No available slots found for rebooking. Please try again later or contact support.
          </p>
        </div>
      <?php else: ?>
        <form id="rebookForm">
          <?php echo csrf_field(); ?>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select New Slot <span class="text-red-500">*</span>
            </label>
            <select name="new_slot_id" id="newSlotId" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">-- Choose a new slot --</option>
              <?php $__currentLoopData = $availableSlots->groupBy('depot.name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $depotSlots): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <optgroup label="<?php echo e($depotName); ?>">
                  <?php $__currentLoopData = $depotSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                      $isRestricted = $slot->allowed_customers->count() > 0;
                      $daysDiff = now()->diffInDays($slot->start_at, false);
                    ?>
                    <option value="<?php echo e($slot->id); ?>" 
                            data-depot="<?php echo e($slot->depot->name); ?>"
                            data-start="<?php echo e($slot->start_at->format('Y-m-d H:i:s')); ?>"
                            data-is-restricted="<?php echo e($isRestricted ? 'true' : 'false'); ?>"
                            title="<?php echo e($isRestricted ? 'Restricted slot' : 'Public slot'); ?>">
                      <?php echo e($isRestricted ? '🔒' : '🌐'); ?> 
                      <?php echo e($slot->start_at->format('D, d M Y - H:i')); ?> - <?php echo e($slot->end_at->format('H:i')); ?>

                      <?php if($daysDiff <= 1): ?>
                        <span style="color: orange;">(<?php echo e($daysDiff < 1 ? 'TODAY' : 'TOMORROW'); ?>)</span>
                      <?php endif; ?>
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </optgroup>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Reason for Rebooking <span class="text-red-500">*</span>
            </label>
            <textarea name="reason" id="reason" required rows="3" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Please provide a reason for rebooking (e.g., schedule change, operational requirement, etc.)"></textarea>
          </div>

          
          <div id="slotDetails" class="hidden mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
            <h4 class="font-medium text-gray-800 mb-2">New Slot Details:</h4>
            <div id="slotDetailsContent"></div>
          </div>

          <div class="flex space-x-3">
            <button type="submit" 
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              🔄 Rebook Booking
            </button>
            
            <button type="button" onclick="showCancelModal()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
              ❌ Cancel Booking Instead
            </button>
            
            <a href="<?php echo e(route('customer.bookings.show', $booking)); ?>"
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
              Back to Booking
            </a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </div>

  
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
    // Show slot details when selected
    document.getElementById('newSlotId').addEventListener('change', function() {
      const selectedOption = this.options[this.selectedIndex];
      const slotDetails = document.getElementById('slotDetails');
      const slotDetailsContent = document.getElementById('slotDetailsContent');
      
      if (selectedOption.value) {
        const depot = selectedOption.dataset.depot;
        const startTime = new Date(selectedOption.dataset.start);
        const isRestricted = selectedOption.dataset.isRestricted === 'true';
        
        slotDetailsContent.innerHTML = `
          <p><strong>Depot:</strong> ${depot}</p>
          <p><strong>Date & Time:</strong> ${startTime.toLocaleString()}</p>
          <p><strong>Access:</strong> ${isRestricted ? '🔒 Customer Restricted' : '🌐 Public Slot'}</p>
        `;
        slotDetails.classList.remove('hidden');
      } else {
        slotDetails.classList.add('hidden');
      }
    });

    // Handle rebook form submission
    document.getElementById('rebookForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const formData = new FormData();
      formData.append('_token', document.querySelector('input[name="_token"]').value);
      formData.append('new_slot_id', document.getElementById('newSlotId').value);
      formData.append('reason', document.getElementById('reason').value);
      
      fetch('<?php echo e(route("customer.bookings.rebook.store", $booking)); ?>', {
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
      
      fetch('<?php echo e(route("customer.bookings.cancel", $booking)); ?>', {
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
          window.location.href = '<?php echo e(route("customer.bookings.show", $booking)); ?>';
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/customer/bookings/rebook.blade.php ENDPATH**/ ?>