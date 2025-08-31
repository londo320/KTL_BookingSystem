
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
      <div>
        <h2 class="font-semibold text-xl">🔄 Rebook Booking: <?php echo e($booking->booking_reference); ?></h2>
        <p class="text-sm text-gray-600 mt-1">Rebook this booking to a different time slot</p>
      </div>
      <a href="<?php echo e(route('app.bookings.show', $booking)); ?>"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        ← Back to Booking
      </a>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto">
    
    
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Current Booking Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium"><?php echo e($booking->customer->name); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Slot</p>
          <p class="font-medium"><?php echo e($booking->slot->start_at->format('D, d M Y - H:i')); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium"><?php echo e($booking->slot->depot->name ?? 'N/A'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Container</p>
          <p class="font-medium"><?php echo e($booking->container_number ?? 'N/A'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Driver</p>
          <p class="font-medium"><?php echo e($booking->driver_name ?? 'N/A'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Rebook Count</p>
          <p class="font-medium"><?php echo e($booking->rebook_count); ?> / <?php echo e($maxRebooksPerBooking); ?></p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        
        <?php if($restrictions['blocked']): ?>
          <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-center">
              <div class="text-red-600 mr-3">🚫</div>
              <div>
                <h3 class="text-lg font-semibold text-red-800">Rebooking Blocked</h3>
                <p class="text-red-700"><?php echo e($restrictions['blocked']); ?></p>
              </div>
            </div>
          </div>
        <?php else: ?>
          <?php if($restrictions['warning']): ?>
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
              <div class="flex items-center">
                <div class="text-orange-600 mr-3">⚠️</div>
                <div>
                  <h3 class="text-lg font-semibold text-orange-800">Warning</h3>
                  <p class="text-orange-700"><?php echo e($restrictions['warning']); ?></p>
                </div>
              </div>
            </div>
          <?php endif; ?>

          
          <div class="bg-white p-6 rounded-lg shadow border">
            <h3 class="text-xl font-semibold mb-4 text-gray-800">🔄 Select New Slot</h3>
            
            <form action="<?php echo e(route('app.bookings.rebook.store', $booking)); ?>" method="POST">
              <?php echo csrf_field(); ?>
              
              <div class="mb-4">
                <label for="new_slot_id" class="block text-sm font-medium text-gray-700 mb-2">
                  New Slot <span class="text-red-500">*</span>
                </label>
                <select name="new_slot_id" id="new_slot_id" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php $__errorArgs = ['new_slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                  <option value="">-- Choose a new slot --</option>
                  <?php $__empty_1 = true; $__currentLoopData = $availableSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($slot->id); ?>" <?php echo e(old('new_slot_id') == $slot->id ? 'selected' : ''); ?>>
                      <?php echo e($slot->start_at->format('D, d M Y - H:i')); ?> - <?php echo e($slot->end_at->format('H:i')); ?>

                      (<?php echo e($slot->bookings->count()); ?>/<?php echo e($slot->capacity); ?> booked)
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option value="">No available slots found</option>
                  <?php endif; ?>
                </select>
                <?php $__errorArgs = ['new_slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="mb-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                  Reason for Rebooking <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" id="reason" required rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          placeholder="Please provide a reason for rebooking..."><?php echo e(old('reason')); ?></textarea>
                <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                  <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
              </div>

              <div class="flex space-x-3">
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                  🔄 Rebook Booking
                </button>
                <a href="<?php echo e(route('app.bookings.show', $booking)); ?>" 
                   class="px-6 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                  Cancel
                </a>
              </div>
            </form>
          </div>
        <?php endif; ?>
      </div>

      <div class="lg:col-span-1">
        
        <div class="bg-white p-6 rounded-lg shadow border">
          <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Customer Behavior (30 days)</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
              <div class="text-2xl font-bold text-orange-600"><?php echo e($customerStats['total_rebooks_30days']); ?></div>
              <div class="text-sm text-gray-600">Total Rebooks</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-red-600"><?php echo e($customerStats['last_minute_rebooks_30days']); ?></div>
              <div class="text-sm text-gray-600">Last Minute</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-blue-600"><?php echo e($customerStats['total_cancellations_30days']); ?></div>
              <div class="text-sm text-gray-600">Cancellations</div>
            </div>
            <div class="text-center">
              <div class="text-2xl font-bold text-green-600"><?php echo e($customerStats['avg_hours_notice']); ?>h</div>
              <div class="text-sm text-gray-600">Avg Notice</div>
            </div>
          </div>
          
          <a href="<?php echo e(route('app.customer-behavior.show', $booking->customer)); ?>" 
             class="block w-full mt-4 px-4 py-2 bg-blue-600 text-white text-center rounded-lg hover:bg-blue-700">
            📈 View Customer Analysis
          </a>
        </div>

        
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

    
    <?php if($booking->history->count() > 0): ?>
    <div class="mt-6 bg-white p-6 rounded-lg shadow border">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Recent History</h3>
      <div class="space-y-4">
        <?php $__currentLoopData = $booking->history->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
          <div class="flex-shrink-0">
            <div class="w-3 h-3 rounded-full <?php echo e($history->action === 'created' ? 'bg-green-500' : ($history->action === 'rebooked' ? 'bg-orange-500' : 'bg-red-500')); ?>"></div>
          </div>
          <div class="flex-1">
            <h4 class="font-medium text-gray-900"><?php echo e(ucfirst($history->action)); ?></h4>
            <p class="text-sm text-gray-600"><?php echo e($history->reason ?? 'No reason provided'); ?></p>
            <p class="text-xs text-gray-500">
              <?php echo e($history->created_at->format('M j, Y g:i A')); ?> by <?php echo e($history->user->name ?? 'System'); ?>

            </p>
          </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <a href="<?php echo e(route('app.bookings.history', $booking)); ?>" 
         class="inline-block mt-4 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
        View Complete History
      </a>
    </div>
    <?php endif; ?>
  </div>

  
  <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-red-800">Cancel Booking</h3>
      <form action="<?php echo e(route('app.bookings.cancel', $booking)); ?>" method="POST">
        <?php echo csrf_field(); ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/rebook.blade.php ENDPATH**/ ?>