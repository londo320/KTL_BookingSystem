<div class="grid grid-cols-2 gap-6">
  
<div class="col-span-2">
  <label class="block text-sm font-medium">Slot 
    <span class="text-xs text-red-600 ml-2">⚠️ Slot changes disabled - Use Rebook button instead</span>
  </label>
  <select name="slot_id" required disabled class="mt-1 block w-full border-gray-300 rounded bg-gray-100 text-gray-500 cursor-not-allowed">
    <?php if($booking->exists && $booking->slot): ?>
      <option value="<?php echo e($booking->slot->id); ?>" selected>
        <?php echo e($booking->slot->depot->name); ?>

        (<?php echo e(\Carbon\Carbon::parse($booking->slot->start_at)->format('d-M H:i')); ?> → <?php echo e(\Carbon\Carbon::parse($booking->slot->end_at)->format('d-M H:i')); ?>)
      </option>
    <?php else: ?>
      <option value="">– Choose slot –</option>
      <?php $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($slot->id); ?>"
          <?php if(old('slot_id', $booking->slot_id) == $slot->id): echo 'selected'; endif; ?>>
          <?php echo e($slot->depot->name); ?>

          (<?php echo e(\Carbon\Carbon::parse($slot->start_at)->format('d-M H:i')); ?> → <?php echo e(\Carbon\Carbon::parse($slot->end_at)->format('d-M H:i')); ?>)
        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
  </select>
  
  
  <?php if($booking->exists && $booking->slot): ?>
    <input type="hidden" name="slot_id" value="<?php echo e($booking->slot->id); ?>">
  <?php endif; ?>
  
  
  <?php if($booking->exists): ?>
    <div class="mt-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
      <p class="text-sm text-orange-800">
        🔄 <strong>Need to change your slot?</strong> Please use the "Rebook" button instead of editing here. 
        This ensures proper tracking and availability management.
        <br>
        <span class="text-xs">You can change other booking details here, but slot changes must be done through rebooking.</span>
      </p>
    </div>
  <?php endif; ?>
  
  <?php $__errorArgs = ['slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

  
  <div>
    <label class="block text-sm font-medium">Booking Type</label>
    <select name="booking_type_id" required class="mt-1 block w-full border-gray-300 rounded">
      <option value="">– Choose type –</option>
      <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($type->id); ?>"
          <?php if(old('booking_type_id', $booking->booking_type_id) == $type->id): echo 'selected'; endif; ?>
        >
          <?php echo e($type->name); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['booking_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>


<div>
  <label class="block text-sm font-medium">Container Size</label>
  <select name="container_size"
          class="mt-1 block w-full border-gray-300 rounded">
    <option value="">– Select Size –</option>
    <option value="20" <?php echo e(old('container_size', $booking->container_size) == 20 ? 'selected' : ''); ?>>20ft</option>
    <option value="40" <?php echo e(old('container_size', $booking->container_size) == 40 ? 'selected' : ''); ?>>40ft</option>
  </select>
  <?php $__errorArgs = ['container_size'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>

  
  <div>
    <label class="block text-sm font-medium">Reference</label>
    <input type="text" name="reference"
           value="<?php echo e(old('reference', $booking->reference)); ?>"
           class="mt-1 block w-full border-gray-300 rounded">
    <?php $__errorArgs = ['reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>


<div class="grid grid-cols-2 gap-6">


  
  <div>
    <label class="block text-sm font-medium">Expected Cases</label>
    <input type="number" name="expected_cases"
           value="<?php echo e(old('expected_cases', $booking->expected_cases)); ?>"
           class="mt-1 block w-full border-gray-300 rounded">
    <?php $__errorArgs = ['expected_cases'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>


<div>
  <label class="block text-sm font-medium">Actual Cases</label>
  <input type="number" name="actual_cases"
         value="<?php echo e(old('actual_cases', $booking->actual_cases)); ?>"
         class="w-full border border-gray-300 rounded px-3 py-2
                <?php echo e(!$booking->exists ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white'); ?>"
         <?php echo e(!$booking->exists ? 'readonly disabled' : ''); ?>>
</div>

  
  <div>
    <label class="block text-sm font-medium">Expected Pallets</label>
    <input type="number" name="expected_pallets"
           value="<?php echo e(old('expected_pallets', $booking->expected_pallets)); ?>"
           class="mt-1 block w-full border-gray-300 rounded">
    <?php $__errorArgs = ['expected_pallets'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div>
    <label class="block text-sm font-medium">Actual Pallets</label>
    <input type="number" name="actual_pallets"
           value="<?php echo e(old('actual_pallets', $booking->actual_pallets)); ?>"
           class="w-full border border-gray-300 rounded px-3 py-2
                <?php echo e(!$booking->exists ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'bg-white'); ?>"
         <?php echo e(!$booking->exists ? 'readonly disabled' : ''); ?>>
  </div>
</div>


  
  <div class="col-span-2 mt-6">
    <?php if (isset($component)) { $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $booking,'readonly' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking),'readonly' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $attributes = $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $component = $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>
  </div>

  
  <div class="col-span-2">
    <label class="block text-sm font-medium">Notes</label>
    <textarea name="notes" rows="3"
              class="mt-1 block w-full border-gray-300 rounded"><?php echo e(old('notes', $booking->notes)); ?></textarea>
    <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="col-span-2 mt-6 border-t pt-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">🚛 Transportation Details</h3>
    <div class="grid grid-cols-2 gap-4">
      
      
      <div>
        <label class="block text-sm font-medium text-gray-700">Vehicle Registration</label>
        <input type="text" name="vehicle_registration"
               value="<?php echo e(old('vehicle_registration', $booking->vehicle_registration)); ?>"
               placeholder="e.g., AB12 CDE"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700">Container/Trailer Number</label>
        <input type="text" name="container_number"
               value="<?php echo e(old('container_number', $booking->container_number)); ?>"
               placeholder="e.g., CONT123456"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['container_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>


      
      <div>
        <label class="block text-sm font-medium text-gray-700">Carrier Company</label>
        <input type="text" name="carrier_company"
               value="<?php echo e(old('carrier_company', $booking->carrier_company)); ?>"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['carrier_company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700">Gate Number</label>
        <input type="text" name="gate_number"
               value="<?php echo e(old('gate_number', $booking->gate_number)); ?>"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['gate_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700">Bay Number</label>
        <input type="text" name="bay_number"
               value="<?php echo e(old('bay_number', $booking->bay_number)); ?>"
               class="mt-1 block w-full border-gray-300 rounded-lg">
        <?php $__errorArgs = ['bay_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div class="col-span-2 border-t pt-4 mt-4">
        <label class="block text-sm font-medium text-blue-700">📞 Expected Arrival Time (if different from slot)</label>
        <input type="datetime-local" name="estimated_arrival"
               value="<?php echo e(old('estimated_arrival', $booking->estimated_arrival ? $booking->estimated_arrival->format('Y-m-d\TH:i') : '')); ?>"
               class="mt-1 block w-full border-blue-300 rounded-lg bg-blue-50">
        <p class="text-xs text-blue-600 mt-1">💡 Update this if your expected arrival changes</p>
        <?php $__errorArgs = ['estimated_arrival'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-700">Special Instructions</label>
        <textarea name="special_instructions" rows="2"
                  class="mt-1 block w-full border-gray-300 rounded-lg"><?php echo e(old('special_instructions', $booking->special_instructions)); ?></textarea>
        <?php $__errorArgs = ['special_instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

    </div>

    <?php if($booking->exists && $booking->arrived_at): ?>
      <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
        <p class="text-sm text-green-800">
          ✅ <strong>Vehicle Arrived:</strong> <?php echo e($booking->arrived_at->format('d-M-Y H:i:s')); ?>

          <?php if($booking->departed_at): ?>
            <br>🕒 <strong>Departed:</strong> <?php echo e($booking->departed_at->format('d-M-Y H:i:s')); ?>

          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>
  </div>

</div><?php /**PATH /Users/londo/Herd/test/resources/views/customer/bookings/_form_readonly.blade.php ENDPATH**/ ?>