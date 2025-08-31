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
        <h2 class="font-semibold text-xl">⚙️ Priority Settings</h2>
        <p class="text-sm text-gray-600 mt-1">Configure customer priorities and manual adjustments</p>
      </div>
      <div class="flex items-center space-x-2">
        <?php if($currentDepotId): ?>
          <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">
            Depot <?php echo e($currentDepotId); ?>

          </span>
        <?php endif; ?>
        <button onclick="window.close()" class="px-3 py-1 bg-gray-500 text-white text-sm rounded hover:bg-gray-600">
          Close Window
        </button>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-7xl mx-auto px-4">
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="font-medium text-gray-900 mb-2">📊 Active Bookings</h3>
        <div class="text-2xl font-bold text-blue-600"><?php echo e($activeBookings->count()); ?></div>
        <div class="text-xs text-gray-500">Currently on site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="font-medium text-gray-900 mb-2">👥 Active Customers</h3>
        <div class="text-2xl font-bold text-green-600"><?php echo e($customers->count()); ?></div>
        <div class="text-xs text-gray-500">With trailers on site</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="font-medium text-gray-900 mb-2">🎯 Priority Boosts</h3>
        <div class="text-2xl font-bold text-purple-600"><?php echo e($activeBookings->where('manual_priority_boost', '!=', 0)->count()); ?></div>
        <div class="text-xs text-gray-500">Manual adjustments active</div>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- CUSTOMER PRIORITY LEVELS -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
          <h3 class="text-lg font-medium text-blue-800">👥 CUSTOMER PRIORITY LEVELS</h3>
          <p class="text-sm text-blue-600 mt-1">Set base priority for customers (0=normal, 10=highest)</p>
        </div>
        <div class="p-4">
          <div class="space-y-3 max-h-96 overflow-y-auto">
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded p-3">
              <div class="flex items-center justify-between mb-2">
                <div class="font-medium text-gray-900"><?php echo e($customer->name); ?></div>
                <div class="text-sm text-gray-500">
                  <?php echo e($activeBookings->where('customer_id', $customer->id)->count()); ?> active
                </div>
              </div>
              <form onsubmit="updateCustomerPriority(event, <?php echo e($customer->id); ?>)" class="space-y-2">
                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="block text-xs font-medium text-gray-700">Priority Level</label>
                    <select name="priority_level" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                      <?php for($i = 0; $i <= 10; $i++): ?>
                        <option value="<?php echo e($i); ?>" <?php echo e($customer->priority_level == $i ? 'selected' : ''); ?>>
                          <?php echo e($i); ?> <?php echo e($i == 0 ? '(Normal)' : ($i >= 8 ? '(VIP)' : ($i >= 5 ? '(High)' : '(Medium)'))); ?>

                        </option>
                      <?php endfor; ?>
                    </select>
                  </div>
                  <div class="flex items-end">
                    <button type="submit" class="w-full px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                      Update
                    </button>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700">Notes</label>
                  <input type="text" name="priority_notes" value="<?php echo e($customer->priority_notes); ?>"
                         placeholder="Why this priority level?"
                         class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                </div>
              </form>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>
      </div>
      <!-- BOOKING-SPECIFIC ADJUSTMENTS -->
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 bg-purple-50">
          <h3 class="text-lg font-medium text-purple-800">🎯 BOOKING ADJUSTMENTS</h3>
          <p class="text-sm text-purple-600 mt-1">Manual priority boosts and collection scheduling</p>
        </div>
        <div class="p-4">
          <div class="space-y-3 max-h-96 overflow-y-auto">
            <?php $__currentLoopData = $activeBookings->sortBy('booking_reference'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border border-gray-200 rounded p-3">
              <div class="flex items-center justify-between mb-2">
                <div>
                  <div class="font-medium text-gray-900"><?php echo e($booking->booking_reference); ?></div>
                  <div class="text-xs text-gray-500"><?php echo e($booking->customer->name ?? 'Unknown'); ?></div>
                </div>
                <?php if($booking->manual_priority_boost != 0): ?>
                  <div class="text-xs font-bold <?php echo e($booking->manual_priority_boost > 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e($booking->manual_priority_boost > 0 ? '+' : ''); ?><?php echo e($booking->manual_priority_boost); ?>

                  </div>
                <?php endif; ?>
              </div>
              <form onsubmit="updateBookingPriority(event, <?php echo e($booking->id); ?>)" class="space-y-2">
                <div class="grid grid-cols-2 gap-2">
                  <div>
                    <label class="block text-xs font-medium text-gray-700">Priority Boost</label>
                    <select name="manual_priority_boost" class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                      <option value="0" <?php echo e($booking->manual_priority_boost == 0 ? 'selected' : ''); ?>>No Boost</option>
                      <option value="200" <?php echo e($booking->manual_priority_boost == 200 ? 'selected' : ''); ?>>+200 (Emergency)</option>
                      <option value="100" <?php echo e($booking->manual_priority_boost == 100 ? 'selected' : ''); ?>>+100 (Urgent)</option>
                      <option value="50" <?php echo e($booking->manual_priority_boost == 50 ? 'selected' : ''); ?>>+50 (High)</option>
                      <option value="25" <?php echo e($booking->manual_priority_boost == 25 ? 'selected' : ''); ?>>+25 (Medium)</option>
                      <option value="-25" <?php echo e($booking->manual_priority_boost == -25 ? 'selected' : ''); ?>>-25 (Delay)</option>
                      <option value="-50" <?php echo e($booking->manual_priority_boost == -50 ? 'selected' : ''); ?>>-50 (Low)</option>
                    </select>
                  </div>
                  <div class="flex items-end">
                    <button type="submit" class="w-full px-2 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700">
                      Update
                    </button>
                  </div>
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700">Collection Scheduled</label>
                  <input type="datetime-local" name="collection_scheduled_at" 
                         value="<?php echo e($booking->collection_scheduled_at ? $booking->collection_scheduled_at->format('Y-m-d\\TH:i') : ''); ?>"
                         class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                </div>
                <div>
                  <label class="block text-xs font-medium text-gray-700">Notes</label>
                  <input type="text" name="priority_notes" value="<?php echo e($booking->priority_notes); ?>"
                         placeholder="Reason for adjustment"
                         class="w-full px-2 py-1 border border-gray-300 rounded text-xs">
                </div>
              </form>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
          <!-- Reset All Button -->
          <div class="mt-4 pt-4 border-t border-gray-200">
            <button onclick="resetAllPriorities()" 
                    class="w-full px-3 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700">
              🔄 Reset All Manual Boosts
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Priority Explanation -->
    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
      <h4 class="font-medium text-gray-900 mb-2">📚 How Priority Works</h4>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
        <div>
          <h5 class="font-medium text-gray-800 mb-1">Automatic Factors:</h5>
          <ul class="space-y-1 text-xs">
            <li>• Customer Priority Level: 0-100 points (priority_level × 10)</li>
            <li>• Wait Time: 1 point per minute waiting (max 480)</li>
            <li>• Overdue Appointment: 50 points if >30min late</li>
            <li>• Type Efficiency: 25 points for matching types</li>
            <li>• Urgent Collection: 75 points if collection <2hrs</li>
          </ul>
        </div>
        <div>
          <h5 class="font-medium text-gray-800 mb-1">Manual Adjustments:</h5>
          <ul class="space-y-1 text-xs">
            <li>• Emergency: +200 points (critical situations)</li>
            <li>• Urgent: +100 points (high priority)</li>
            <li>• Delay: -25 to -50 points (reduce priority)</li>
            <li>• Collection scheduling triggers urgent collection bonus</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <script>
    async function updateCustomerPriority(event, customerId) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      try {
        const response = await fetch(`/admin/operations/customers/${customerId}/priority`, {
          method: 'PUT',
          headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            priority_level: formData.get('priority_level'),
            priority_notes: formData.get('priority_notes')
          })
        });
        const data = await response.json();
        if (data.success) {
          showMessage(data.message, 'success');
        } else {
          showMessage(data.error || 'Update failed', 'error');
        }
      } catch (error) {
        showMessage('Network error occurred', 'error');
      }
    }
    async function updateBookingPriority(event, bookingId) {
      event.preventDefault();
      const form = event.target;
      const formData = new FormData(form);
      try {
        const response = await fetch(`/admin/operations/bookings/${bookingId}/priority`, {
          method: 'PUT',
          headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            manual_priority_boost: formData.get('manual_priority_boost'),
            collection_scheduled_at: formData.get('collection_scheduled_at') || null,
            priority_notes: formData.get('priority_notes')
          })
        });
        const data = await response.json();
        if (data.success) {
          showMessage(data.message, 'success');
        } else {
          showMessage(data.error || 'Update failed', 'error');
        }
      } catch (error) {
        showMessage('Network error occurred', 'error');
      }
    }
    async function resetAllPriorities() {
      if (!confirm('Are you sure you want to reset ALL manual priority boosts? This cannot be undone.')) {
        return;
      }
      const depotId = new URLSearchParams(window.location.search).get('depot_id');
      try {
        const response = await fetch('/admin/operations/reset-priorities', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            depot_id: depotId
          })
        });
        const data = await response.json();
        if (data.success) {
          showMessage(data.message, 'success');
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showMessage(data.error || 'Reset failed', 'error');
        }
      } catch (error) {
        showMessage('Network error occurred', 'error');
      }
    }
    function showMessage(message, type) {
      // Create toast notification
      const toast = document.createElement('div');
      toast.className = `fixed top-4 right-4 px-4 py-2 rounded text-white text-sm z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
      }`;
      toast.textContent = message;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.remove();
      }, 3000);
    }
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/operations/priority-settings.blade.php ENDPATH**/ ?>