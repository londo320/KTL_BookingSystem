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
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">🚛</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Factory Delivery Workflow</h1>
              <p class="text-sm text-gray-600">Tipping Operations Management</p>
            </div>
          </div>
        </div>
        
        
        <div class="text-right">
          <div class="text-sm text-gray-500">Factory Reference</div>
          <div class="text-2xl font-bold text-orange-600">#<?php echo e($factoryBooking->reference); ?></div>
        </div>
      </div>
      
      
      <div class="flex flex-wrap gap-3">
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Navigation</span>
          <a href="<?php echo e(route('admin.factory-bookings.show', $factoryBooking)); ?>"
             class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
            ← Factory Booking Details
          </a>
          <a href="<?php echo e(route('admin.tipping-workflow.dashboard')); ?>"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📊 Tipping Dashboard
          </a>
        </div>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto px-4">
    <?php if(session('success')): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <ul class="list-disc pl-5">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    
    <?php
      $movement = $factoryBooking->movements->last();
      $currentStatus = $movement ? $movement->current_status : 'arrived';
      $currentLocation = $movement?->tippingLocation;
      $currentBay = $movement?->tippingBay;
    ?>

    <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-semibold text-orange-800">Current Status: <?php echo e(ucfirst(str_replace('_', ' ', $currentStatus))); ?></h3>
          <?php if($currentLocation): ?>
            <p class="text-orange-700">📍 Location: <?php echo e($currentLocation->name); ?></p>
          <?php endif; ?>
          <?php if($currentBay): ?>
            <p class="text-orange-700">🚛 Bay: <?php echo e($currentBay->name); ?></p>
          <?php endif; ?>
          <?php if($movement && $movement->operation_notes): ?>
            <p class="text-orange-700 text-sm mt-2">📝 Notes: <?php echo e($movement->operation_notes); ?></p>
          <?php endif; ?>
        </div>
        <div class="text-right">
          <div class="text-sm text-gray-600"><?php echo e($factoryBooking->customer->name); ?></div>
          <div class="text-sm text-gray-600"><?php echo e($factoryBooking->vehicle_registration); ?></div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div class="bg-white rounded-lg shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">🔄 Workflow Actions</h3>
        
        
        <div class="mb-6">
          <h4 class="font-medium text-gray-800 mb-3">🚛 Vehicle Movement</h4>
          
          
          <form method="POST" action="<?php echo e(route('admin.factory-booking-workflow.drop-trailer', $factoryBooking)); ?>" class="mb-3">
            <?php echo csrf_field(); ?>
            <div class="flex flex-wrap items-end gap-2">
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Drop at Location</label>
                <select name="tipping_location_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                  <option value="">Select location...</option>
                  <?php $__currentLoopData = $availableLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?> 
                      (<?php echo e($location->getCurrentOccupancy()); ?>/<?php echo e($location->capacity); ?>)
                    </option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional notes">
              </div>
              <button type="submit" class="px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                Park Vehicle
              </button>
            </div>
          </form>

          
          <form method="POST" action="<?php echo e(route('admin.factory-booking-workflow.move-to-bay', $factoryBooking)); ?>" class="mb-3">
            <?php echo csrf_field(); ?>
            <div class="flex flex-wrap items-end gap-2">
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Move to Bay</label>
                <select name="tipping_bay_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                  <option value="">Select bay...</option>
                  <?php $__currentLoopData = $availableBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($bay->id); ?>"><?php echo e($bay->name); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              </div>
              <div class="flex-1 min-w-0">
                <label class="block text-sm font-medium text-gray-700">Notes</label>
                <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional notes">
              </div>
              <button type="submit" class="px-3 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700">
                Move to Bay
              </button>
            </div>
          </form>
        </div>

        
        <div class="mb-6">
          <h4 class="font-medium text-gray-800 mb-3">⚡ Tipping Operations</h4>
          
          <?php if(!$movement || !$movement->unloading_completed_at): ?>
            
            <form method="POST" action="<?php echo e(route('admin.factory-booking-workflow.start-tipping', $factoryBooking)); ?>" class="mb-3">
              <?php echo csrf_field(); ?>
              <div class="flex flex-wrap items-end gap-2">
                <div class="flex-1">
                  <label class="block text-sm font-medium text-gray-700">Start Tipping</label>
                  <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional notes">
                </div>
                <button type="submit" class="px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                  Start Tipping
                </button>
              </div>
            </form>

            
            <div class="p-4 bg-gray-50 rounded-lg">
              <h5 class="font-medium text-gray-800 mb-3">Complete Tipping</h5>
              
              <form method="POST" action="<?php echo e(route('admin.factory-booking-workflow.complete-tipping', $factoryBooking)); ?>">
                <?php echo csrf_field(); ?>
                
                
                <?php if($factoryBooking->poNumbers->count() > 0): ?>
                  <div class="mb-4">
                    <h6 class="font-medium text-gray-700 mb-2">📦 Record Actual Quantities</h6>
                    
                    <?php $__currentLoopData = $factoryBooking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <?php if($po->lines->count() > 0): ?>
                        <div class="mb-4 p-3 border border-gray-200 rounded">
                          <h7 class="font-medium text-gray-700">PO: <?php echo e($po->po_number); ?></h7>
                          
                          <?php $__currentLoopData = $po->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="mt-3 p-3 bg-white rounded border">
                              <div class="flex items-center justify-between mb-2">
                                <span class="font-medium"><?php echo e($line->expectedPalletType->name ?? 'Unknown Type'); ?></span>
                                <span class="text-sm text-gray-600">Expected: <?php echo e($line->expected_cases); ?> cases</span>
                              </div>
                              
                              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                
                                <div>
                                  <label class="block text-sm font-medium text-gray-700">Actual Cases</label>
                                  <input type="number" 
                                         name="po_lines[<?php echo e($line->id); ?>][actual_cases]" 
                                         value="<?php echo e($line->actual_cases ?: $line->expected_cases); ?>"
                                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" 
                                         min="1" required>
                                </div>
                                
                                
                                <div>
                                  <label class="block text-sm font-medium text-gray-700">Actual Pallets</label>
                                  <div class="space-y-2">
                                    <?php $__currentLoopData = $palletTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <div class="flex items-center space-x-2">
                                        <span class="text-sm w-20"><?php echo e($palletType->name); ?>:</span>
                                        <input type="number" 
                                               name="po_lines[<?php echo e($line->id); ?>][actual_pallets][<?php echo e($loop->index); ?>][quantity]" 
                                               value="<?php echo e($line->actualPallets->where('pallet_type_id', $palletType->id)->first()?->quantity ?? ($palletType->id == $line->expected_pallet_type_id ? $line->expected_pallets : 0)); ?>"
                                               class="flex-1 border-gray-300 rounded-md shadow-sm text-sm" 
                                               min="0">
                                        <input type="hidden" 
                                               name="po_lines[<?php echo e($line->id); ?>][actual_pallets][<?php echo e($loop->index); ?>][pallet_type_id]" 
                                               value="<?php echo e($palletType->id); ?>">
                                      </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php endif; ?>
                
                
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Completion Notes</label>
                  <textarea name="notes" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Any notes about the tipping completion..."></textarea>
                </div>
                
                
                <div class="mb-4">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Issues (if any)</label>
                  <div class="space-y-2">
                    <input type="text" name="issues[]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Describe any issues...">
                    <input type="text" name="issues[]" class="block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Additional issue...">
                  </div>
                </div>
                
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white font-medium rounded hover:bg-green-700">
                  Complete Tipping
                </button>
              </form>
            </div>
          <?php else: ?>
            <div class="p-4 bg-green-100 rounded-lg">
              <p class="text-green-800">✅ Tipping completed at <?php echo e($movement->unloading_completed_at->format('d M Y, H:i')); ?></p>
            </div>
          <?php endif; ?>
        </div>

        
        <div>
          <h4 class="font-medium text-gray-800 mb-3">🏁 Departure</h4>
          
          <form method="POST" action="<?php echo e(route('admin.factory-booking-workflow.trailer-depart', $factoryBooking)); ?>">
            <?php echo csrf_field(); ?>
            <div class="flex flex-wrap items-end gap-2">
              <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700">Mark Departure</label>
                <input type="text" name="notes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Optional departure notes">
              </div>
              <button type="submit" class="px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                Mark Departed
              </button>
            </div>
          </form>
        </div>
      </div>

      
      <div class="space-y-6">
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h4 class="font-medium text-gray-800 mb-3">📋 Factory Booking Details</h4>
          
          <div class="space-y-2 text-sm">
            <div><strong>Reference:</strong> <?php echo e($factoryBooking->reference); ?></div>
            <div><strong>Customer:</strong> <?php echo e($factoryBooking->customer->name); ?></div>
            <div><strong>Depot:</strong> <?php echo e($factoryBooking->depot->name); ?></div>
            <div><strong>Vehicle:</strong> <?php echo e($factoryBooking->vehicle_registration); ?></div>
            <?php if($factoryBooking->trailer_registration): ?>
              <div><strong>Trailer:</strong> <?php echo e($factoryBooking->trailer_registration); ?></div>
            <?php endif; ?>
            <?php if($factoryBooking->driver_name): ?>
              <div><strong>Driver:</strong> <?php echo e($factoryBooking->driver_name); ?></div>
            <?php endif; ?>
            <div><strong>Arrived:</strong> <?php echo e($factoryBooking->arrived_at->format('d M Y, H:i')); ?></div>
            <div><strong>Time on Site:</strong> <?php echo e($factoryBooking->getTimeOnSite()); ?></div>
          </div>
        </div>

        
        <?php if($factoryBooking->poNumbers->count() > 0): ?>
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h4 class="font-medium text-gray-800 mb-3">📦 PO Numbers</h4>
            
            <div class="space-y-3">
              <?php $__currentLoopData = $factoryBooking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-3 bg-gray-50 rounded-md">
                  <div class="font-medium"><?php echo e($po->po_number); ?></div>
                  <?php if($po->description): ?>
                    <div class="text-sm text-gray-600"><?php echo e($po->description); ?></div>
                  <?php endif; ?>
                  <?php if($po->lines->count() > 0): ?>
                    <div class="mt-2 text-xs text-gray-500">
                      <?php echo e($po->lines->count()); ?> line(s)
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        <?php endif; ?>

        
        <?php if($factoryBooking->movements->count() > 0): ?>
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h4 class="font-medium text-gray-800 mb-3">📊 Movement History</h4>
            
            <div class="space-y-2 text-sm">
              <?php $__currentLoopData = $factoryBooking->movements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between">
                  <span><?php echo e(ucfirst(str_replace('_', ' ', $mov->current_status))); ?></span>
                  <span class="text-gray-600"><?php echo e($mov->updated_at->format('M j, H:i')); ?></span>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-booking-workflow/show.blade.php ENDPATH**/ ?>