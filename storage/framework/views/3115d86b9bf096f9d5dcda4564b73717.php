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
  <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Edit Factory Booking</h2>
        <p class="text-sm text-gray-600 mt-1"><?php echo e($factoryBooking->reference); ?> - Update delivery details</p>
      </div>
      <div class="flex gap-2">
        <a href="<?php echo e(route('admin.factory-bookings.show', $factoryBooking)); ?>"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          ← Back to Details
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-4xl mx-auto">
    <?php if($errors->any()): ?>
      <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <h3 class="text-red-800 font-medium mb-2">Please correct the following errors:</h3>
        <ul class="text-red-700 text-sm space-y-1">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>• <?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('admin.factory-bookings.update', $factoryBooking)); ?>" class="space-y-6">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">📋</span>
          Basic Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
            <div class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-600 font-mono">
              <?php echo e($factoryBooking->reference); ?>

            </div>
            <p class="mt-1 text-xs text-gray-500">Reference cannot be changed</p>
          </div>

          
          <div>
            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
              Priority (0-100)
            </label>
            <input type="number" name="priority" id="priority" min="0" max="100" 
                   value="<?php echo e(old('priority', $factoryBooking->priority)); ?>"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            <p class="mt-1 text-xs text-gray-500">
              Higher numbers = higher priority (80+ urgent, 50 normal, 20- low)
            </p>
          </div>
        </div>
      </div>

      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🏢</span>
          Customer & Carrier Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
              Customer <span class="text-red-500">*</span>
            </label>
            <select name="customer_id" id="customer_id" required 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Customer</option>
              <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($customer->id); ?>" 
                        <?php echo e((old('customer_id', $factoryBooking->customer_id) == $customer->id) ? 'selected' : ''); ?>>
                  <?php echo e($customer->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          
          <div>
            <label for="carrier_id" class="block text-sm font-medium text-gray-700 mb-2">
              Carrier (Optional)
            </label>
            <select name="carrier_id" id="carrier_id" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Carrier (if known)</option>
              <?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($carrier->id); ?>" 
                        <?php echo e((old('carrier_id', $factoryBooking->carrier_id) == $carrier->id) ? 'selected' : ''); ?>>
                  <?php echo e($carrier->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        </div>
      </div>

      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">🚛</span>
          Vehicle Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label for="vehicle_registration" class="block text-sm font-medium text-gray-700 mb-2">
              Vehicle Registration <span class="text-red-500">*</span>
            </label>
            <input type="text" name="vehicle_registration" id="vehicle_registration" required 
                   value="<?php echo e(old('vehicle_registration', $factoryBooking->vehicle_registration)); ?>"
                   placeholder="e.g., AB12 XYZ"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
          </div>

          
          <div>
            <label for="trailer_registration" class="block text-sm font-medium text-gray-700 mb-2">
              Trailer Registration (Optional)
            </label>
            <input type="text" name="trailer_registration" id="trailer_registration" 
                   value="<?php echo e(old('trailer_registration', $factoryBooking->trailer_registration)); ?>"
                   placeholder="e.g., TR12 345"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
          </div>

          
          <div>
            <label for="trailer_type_id" class="block text-sm font-medium text-gray-700 mb-2">
              Trailer Type (Optional)
            </label>
            <select name="trailer_type_id" id="trailer_type_id" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
              <option value="">Select Trailer Type (if known)</option>
              <?php $__currentLoopData = $trailerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailerType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($trailerType->id); ?>" 
                        <?php echo e((old('trailer_type_id', $factoryBooking->trailer_type_id) == $trailerType->id) ? 'selected' : ''); ?>>
                  <?php echo e($trailerType->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        </div>
      </div>

      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">👤</span>
          Driver Information
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          
          <div>
            <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">
              Driver Name (Optional)
            </label>
            <input type="text" name="driver_name" id="driver_name" 
                   value="<?php echo e(old('driver_name', $factoryBooking->driver_name)); ?>"
                   placeholder="e.g., John Smith"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>

          
          <div>
            <label for="driver_phone" class="block text-sm font-medium text-gray-700 mb-2">
              Driver Phone (Optional)
            </label>
            <input type="text" name="driver_phone" id="driver_phone" 
                   value="<?php echo e(old('driver_phone', $factoryBooking->driver_phone)); ?>"
                   placeholder="e.g., 07123 456789"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>
      </div>

      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
          <span class="mr-2">📝</span>
          Additional Information
        </h3>
        
        <div class="space-y-4">
          
          <div>
            <label for="delivery_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Delivery Notes (Optional)
            </label>
            <textarea name="delivery_notes" id="delivery_notes" rows="3" 
                      placeholder="Any relevant information about the delivery..."
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('delivery_notes', $factoryBooking->delivery_notes)); ?></textarea>
          </div>

          
          <div>
            <label for="gate_notes" class="block text-sm font-medium text-gray-700 mb-2">
              Gate Staff Notes (Optional)
            </label>
            <textarea name="gate_notes" id="gate_notes" rows="3" 
                      placeholder="Internal notes for gate staff and operations..."
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('gate_notes', $factoryBooking->gate_notes)); ?></textarea>
          </div>
        </div>
      </div>

      
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-blue-800 font-medium mb-2">📊 Current Status</h4>
        <div class="text-blue-700 text-sm space-y-1">
          <div><strong>Status:</strong> <?php echo e(ucfirst($factoryBooking->status)); ?></div>
          <div><strong>Arrived:</strong> <?php echo e($factoryBooking->arrived_at->format('d M Y, H:i')); ?></div>
          <?php if($factoryBooking->processing_started_at): ?>
            <div><strong>Processing Started:</strong> <?php echo e($factoryBooking->processing_started_at->format('d M Y, H:i')); ?></div>
          <?php endif; ?>
          <div><strong>Time on Site:</strong> <?php echo e($factoryBooking->getTimeOnSite()); ?></div>
        </div>
      </div>

      
      <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <a href="<?php echo e(route('admin.factory-bookings.show', $factoryBooking)); ?>" 
           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
          Cancel
        </a>
        
        <button type="submit" 
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
          💾 Update Factory Booking
        </button>
      </div>
    </form>
  </div>

  
  <script>
    document.getElementById('vehicle_registration').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });
    
    document.getElementById('trailer_registration').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-bookings/edit.blade.php ENDPATH**/ ?>