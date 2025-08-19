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
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">WM</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Warehouse Manager</h1>
              <p class="text-sm text-gray-600">Professional Booking System</p>
            </div>
          </div>
        </div>
        
        
        <div class="text-right">
          <div class="text-sm text-gray-500">Booking Reference</div>
          <div class="text-2xl font-bold text-gray-900">#<?php echo e($booking->id); ?></div>
        </div>
      </div>
      
      
      <div class="flex flex-wrap gap-3">
        <?php
          $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
          $hasArrived = $booking->arrived_at;
          
          // Action restriction logic
          $user = auth()->user();
          $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();
          if (empty($allowedDepotIds) && $user->hasRole('admin|site-admin')) {
              $allowedDepotIds = \App\Models\Depot::pluck('id')->toArray();
          }
          $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
          $canTakeAction = $booking->slot->depot_id == $defaultDepotId;
        ?>
        
        
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Documents</span>
          <a href="<?php echo e(route('admin.bookings.download-pdf', $booking)); ?>"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📄 PDF
          </a>
          <button onclick="emailBookingPDF(<?php echo e($booking->id); ?>)"
                  class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
            📧 Email
          </button>
        </div>
        
        
        <?php if($hasArrived && !$booking->cancelled_at): ?>
          <div class="flex items-center space-x-2 bg-orange-50 p-2 rounded-lg border border-orange-200">
            <span class="text-xs font-medium text-orange-700 uppercase">Operations</span>
            <?php if($canTakeAction): ?>
              <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>"
                 class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                🚛 Workflow
              </a>
            <?php else: ?>
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                    title="Actions only available for your default depot">
                🚛 Workflow
              </span>
            <?php endif; ?>
            
            <?php if($booking->tipping_bay_id && in_array($booking->tipping_status, ['at_bay', 'unloading'])): ?>
              <?php if($canTakeAction): ?>
                <a href="<?php echo e(route('admin.bookings.transfer-bay.form', $booking)); ?>"
                   class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
                  🔄 Transfer
                </a>
              <?php else: ?>
                <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                      title="Actions only available for your default depot">
                  🔄 Transfer
                </span>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        
        <div class="flex items-center space-x-2 bg-blue-50 p-2 rounded-lg border border-blue-200">
          <span class="text-xs font-medium text-blue-700 uppercase">Management</span>
          
          <?php if($booking->cancelled_at): ?>
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-400 text-white text-sm font-medium rounded-md cursor-not-allowed">
              ❌ Cancelled
            </span>
          <?php else: ?>
            <?php if(!$hasArrived && !$booking->isCancelled()): ?>
              <?php if($canTakeAction): ?>
                <a href="<?php echo e(route('admin.bookings.edit', $booking)); ?>"
                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                  ✏️ Edit
                </a>
              <?php else: ?>
                <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                      title="Actions only available for your default depot">
                  ✏️ Edit
                </span>
              <?php endif; ?>
            <?php endif; ?>
            
            <?php if($canTakeAction): ?>
              <a href="<?php echo e(route('admin.bookings.rebook.show', $booking)); ?>"
                 class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                🔄 <?php echo e($hasArrived ? 'Rebook/Reject' : 'Rebook'); ?>

              </a>
              
              <button onclick="showCancelModal()" 
                      class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                ❌ <?php echo e($hasArrived ? 'Cancel/Reject' : 'Cancel'); ?>

              </button>
            <?php else: ?>
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                    title="Actions only available for your default depot">
                🔄 <?php echo e($hasArrived ? 'Rebook/Reject' : 'Rebook'); ?>

              </span>
              
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                    title="Actions only available for your default depot">
                ❌ <?php echo e($hasArrived ? 'Cancel/Reject' : 'Cancel'); ?>

              </span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
        
        
        <?php
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
        ?>
        
        <div class="flex items-center space-x-2 bg-yellow-50 p-2 rounded-lg border border-yellow-200">
          <span class="text-xs font-medium text-yellow-700 uppercase">Information</span>
          <?php if($hasHistory): ?>
            <a href="<?php echo e(route('admin.bookings.history', $booking)); ?>"
               class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
              📋 History
            </a>
          <?php endif; ?>
        </div>
        
        
        <div class="flex items-center ml-auto">
          <a href="<?php echo e(route('admin.bookings.index')); ?>"
             class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
            ← Back to Bookings
          </a>
        </div>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-4xl mx-auto">
    
    
    <?php if(session('success')): ?>
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <p class="text-green-800"><?php echo e(session('success')); ?></p>
      </div>
    <?php endif; ?>
    
    <?php if(session('info')): ?>
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <p class="text-blue-800"><?php echo e(session('info')); ?></p>
      </div>
    <?php endif; ?>
    
    
    <?php if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'))): ?>
      <div class="mb-6 p-4 bg-black text-white rounded-lg">
        <div class="flex items-center">
          <span class="text-white text-2xl mr-3">❌</span>
          <div>
            <h3 class="text-lg font-semibold text-white">Booking Cancelled</h3>
            <p class="text-white">
              Cancelled: <?php echo e($booking->cancelled_at->format('d M Y, H:i')); ?>

              <?php if($booking->cancellation_reason): ?>
                <br>Reason: <?php echo e($booking->cancellation_reason); ?>

              <?php endif; ?>
            </p>
          </div>
        </div>
      </div>
    <?php elseif($hasArrived): ?>
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-green-600 text-2xl mr-3">✅</span>
            <div>
              <h3 class="text-lg font-semibold text-green-800">Vehicle Arrived</h3>
              <p class="text-green-700">
                Arrived: <?php echo e($booking->arrived_at->format('d M Y, H:i')); ?>

                <?php if($booking->departed_at): ?>
                  | Departed: <?php echo e($booking->departed_at->format('d M Y, H:i')); ?>

                <?php else: ?>
                  | Currently on-site
                <?php endif; ?>
              </p>
            </div>
          </div>
          
          
          <div class="text-right">
            <p class="text-sm text-gray-600 mb-1">Tipping Status:</p>
            <div class="mb-2"><?php echo $booking->tipping_status_badge; ?></div>
            
            
            <?php $movement = $booking->movements->first(); ?>
            <?php if($movement && ($movement->tippingLocation || $movement->tippingBay)): ?>
              <div class="text-xs text-gray-600 mb-2">
                <?php if($movement->tippingLocation): ?>
                  <div>📍 <?php echo e($movement->tippingLocation->name); ?></div>
                  <div class="text-gray-400">(<?php echo e($movement->tippingLocation->depot->name); ?>)</div>
                <?php endif; ?>
                <?php if($movement->tippingBay): ?>
                  <div>🚛 <?php echo e($movement->tippingBay->name); ?></div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
            
            
            <?php if($booking->tipping_status && $booking->tipping_status !== 'departed'): ?>
              <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                 class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                🚛 Manage Workflow
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php elseif($isLocked): ?>
      <div class="mb-6 p-4 bg-orange-100 border border-orange-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-orange-600 text-2xl mr-3">🔒</span>
          <div>
            <h3 class="text-lg font-semibold text-orange-800">Booking Locked</h3>
            <p class="text-orange-700">
              Cut-off time: <?php echo e($booking->slot->locked_at->format('d M Y, H:i')); ?>

            </p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-blue-600 text-2xl mr-3">📅</span>
          <div>
            <h3 class="text-lg font-semibold text-blue-800">Booking Active</h3>
            <p class="text-blue-700">This booking is active and can be edited.</p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📋 Booking Information</h3>
        
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Booking ID</label>
            <p class="text-lg font-mono">#<?php echo e($booking->id); ?></p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Customer</label>
            <p class="text-lg"><?php echo e($booking->customer->name ?? 'Not assigned'); ?></p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Created By</label>
            <p class="text-lg"><?php echo e($booking->user->name ?? 'Unknown'); ?></p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Created At</label>
            <p class="text-lg"><?php echo e($booking->created_at->format('d M Y, H:i')); ?></p>
          </div>
          
          <?php if($booking->reference): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Reference</label>
              <p class="text-lg font-mono"><?php echo e($booking->reference); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📍 Slot & Location</h3>
        
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Depot</label>
            <p class="text-lg"><?php echo e($booking->slot->depot->name); ?></p>
            <?php if($booking->slot->depot->location): ?>
              <p class="text-sm text-gray-500"><?php echo e($booking->slot->depot->location); ?></p>
            <?php endif; ?>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Date & Time</label>
            <p class="text-lg">
              <?php echo e($booking->slot->start_at->format('l, d F Y')); ?>

            </p>
            <p class="text-lg font-semibold text-blue-600">
              <?php echo e($booking->slot->start_at->format('H:i')); ?> - <?php echo e($booking->slot->end_at->format('H:i')); ?>

            </p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Booking Type</label>
            <p class="text-lg"><?php echo e($booking->bookingType->name ?? 'Not specified'); ?></p>
          </div>
          
          <div>
            <label class="text-sm font-medium text-gray-600">Slot Capacity</label>
            <p class="text-lg"><?php echo e($booking->slot->capacity ?? 'Unlimited'); ?></p>
          </div>
        </div>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow col-span-2">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📦 PO Numbers & Load Details</h3>
        
        <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
          <div class="space-y-4">
            <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $poNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-start mb-3">
                  <h4 class="font-medium text-lg text-gray-800">PO: <?php echo e($poNumber->po_number); ?></h4>
                  <div class="flex space-x-2">
                    <?php if($poNumber->hasVariance()): ?>
                      <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                        Has Variance
                      </span>
                    <?php endif; ?>
                    <?php if($poNumber->hasTypeVariances()): ?>
                      <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                        Type Variance
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                
                <div class="mb-4 p-3 bg-white rounded border">
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600">Total Expected:</span>
                      <span class="font-semibold"><?php echo e(number_format($poNumber->total_expected_units)); ?> units, <?php echo e(number_format($poNumber->total_expected_pallets)); ?> pallets</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Total Actual:</span>
                      <span class="font-semibold"><?php echo e(number_format($poNumber->total_actual_units)); ?> units, <?php echo e(number_format($poNumber->total_actual_pallets)); ?> pallets</span>
                    </div>
                  </div>
                  <div class="mt-2">
                    <span class="text-gray-600">Summary:</span>
                    <span class="text-sm"><?php echo e($poNumber->expected_summary_text); ?></span>
                  </div>
                </div>

                
                <?php if($poNumber->lines->count() > 0): ?>
                  <div class="space-y-3">
                    <h5 class="font-medium text-gray-700">Lines (<?php echo e($poNumber->lines->count()); ?>)</h5>
                    <?php $__currentLoopData = $poNumber->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="border border-gray-300 rounded p-3 bg-white">
                        <div class="flex justify-between items-start mb-2">
                          <span class="font-medium text-sm">Line <?php echo e($line->line_number); ?></span>
                          <?php if($line->hasVariance()): ?>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                              Variance
                            </span>
                          <?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                          
                          <div>
                            <div class="font-medium text-gray-600 mb-1">Units/Cases</div>
                            <div class="flex items-center space-x-2">
                              <span class="text-gray-500">Expected:</span>
                              <span class="font-semibold"><?php echo e(number_format($line->expected_cases)); ?></span>
                              <?php if($line->actual_cases !== null): ?>
                                <span class="text-gray-400">→</span>
                                <span class="text-gray-500">Actual:</span>
                                <span class="font-semibold <?php echo e($line->unit_variance == 0 ? 'text-green-600' : ($line->unit_variance > 0 ? 'text-blue-600' : 'text-red-600')); ?>">
                                  <?php echo e(number_format($line->actual_cases)); ?>

                                </span>
                                <?php if($line->unit_variance != 0): ?>
                                  <span class="text-xs <?php echo e($line->unit_variance > 0 ? 'text-blue-600' : 'text-red-600'); ?>">
                                    (<?php echo e($line->unit_variance > 0 ? '+' : ''); ?><?php echo e(number_format($line->unit_variance)); ?>)
                                  </span>
                                <?php endif; ?>
                              <?php elseif($hasArrived): ?>
                                <span class="text-gray-400">→ Not recorded</span>
                              <?php endif; ?>
                            </div>
                          </div>

                          
                          <div>
                            <div class="font-medium text-gray-600 mb-1">Pallets</div>
                            <div class="flex items-center space-x-2">
                              <span class="text-gray-500">Expected:</span>
                              <span class="font-semibold"><?php echo e(number_format($line->expected_pallets)); ?></span>
                              <?php if($line->expectedPalletType): ?>
                                <span class="text-xs text-gray-600">(<?php echo e($line->expectedPalletType->name); ?>)</span>
                              <?php endif; ?>
                              <?php if($line->total_actual_pallets > 0): ?>
                                <span class="text-gray-400">→</span>
                                <span class="text-gray-500">Actual:</span>
                                <span class="font-semibold <?php echo e($line->pallet_variance == 0 ? 'text-green-600' : ($line->pallet_variance > 0 ? 'text-blue-600' : 'text-red-600')); ?>">
                                  <?php echo e(number_format($line->total_actual_pallets)); ?>

                                </span>
                                <?php if($line->actualPallets->count() > 0): ?>
                                  <span class="text-xs text-gray-600">
                                    <?php if($line->hasMultiplePalletTypes()): ?>
                                      (<?php echo e($line->actualPallets->map(fn($p) => $p->quantity . ' ' . $p->palletType->name)->join(', ')); ?>)
                                    <?php else: ?>
                                      (<?php echo e($line->actualPallets->first()->palletType->name); ?>)
                                    <?php endif; ?>
                                  </span>
                                <?php elseif($line->actualPalletType): ?>
                                  <span class="text-xs text-gray-600">(<?php echo e($line->actualPalletType->name); ?>)</span>
                                <?php endif; ?>
                                <?php if($line->pallet_variance != 0): ?>
                                  <span class="text-xs <?php echo e($line->pallet_variance > 0 ? 'text-blue-600' : 'text-red-600'); ?>">
                                    (<?php echo e($line->pallet_variance > 0 ? '+' : ''); ?><?php echo e(number_format($line->pallet_variance)); ?>)
                                  </span>
                                <?php endif; ?>
                              <?php elseif($hasArrived): ?>
                                <span class="text-gray-400">→ Not recorded</span>
                              <?php endif; ?>
                            </div>
                            
                            
                            <?php if($line->pallet_type_variance): ?>
                              <div class="mt-1 text-xs text-red-600">
                                <span class="font-medium">Type Change:</span> <?php echo e($line->pallet_type_variance); ?>

                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            
            <?php if($booking->poNumbers->count() > 1): ?>
              <div class="border-t pt-4 mt-4">
                <h5 class="font-medium text-gray-800 mb-3">Summary Totals</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="text-sm font-medium text-gray-600">Total Cases</label>
                    <div class="flex items-center space-x-4 mt-1">
                      <?php if($booking->total_expected_cases > 0): ?>
                        <div>
                          <span class="text-sm text-gray-500">Expected:</span>
                          <span class="text-xl font-bold"><?php echo e(number_format($booking->total_expected_cases)); ?></span>
                        </div>
                      <?php endif; ?>
                      <?php if($booking->total_actual_cases > 0): ?>
                        <?php if($booking->total_expected_cases > 0): ?>
                          <div class="text-gray-400">→</div>
                        <?php endif; ?>
                        <div>
                          <span class="text-sm text-gray-500">Actual:</span>
                          <span class="text-xl font-bold <?php echo e($booking->total_case_variance == 0 ? 'text-green-600' : ($booking->total_case_variance > 0 ? 'text-blue-600' : 'text-red-600')); ?>">
                            <?php echo e(number_format($booking->total_actual_cases)); ?>

                          </span>
                          <?php if($booking->total_expected_cases > 0 && $booking->total_case_variance != 0): ?>
                            <span class="text-lg <?php echo e($booking->total_case_variance > 0 ? 'text-blue-600' : 'text-red-600'); ?>">
                              (<?php echo e($booking->total_case_variance > 0 ? '+' : ''); ?><?php echo e(number_format($booking->total_case_variance)); ?>)
                            </span>
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                  
                  <div>
                    <label class="text-sm font-medium text-gray-600">Total Pallets</label>
                    <div class="flex items-center space-x-4 mt-1">
                      <?php if($booking->total_expected_pallets > 0): ?>
                        <div>
                          <span class="text-sm text-gray-500">Expected:</span>
                          <span class="text-xl font-bold"><?php echo e(number_format($booking->total_expected_pallets)); ?></span>
                        </div>
                      <?php endif; ?>
                      <?php if($booking->total_actual_pallets > 0): ?>
                        <?php if($booking->total_expected_pallets > 0): ?>
                          <div class="text-gray-400">→</div>
                        <?php endif; ?>
                        <div>
                          <span class="text-sm text-gray-500">Actual:</span>
                          <span class="text-xl font-bold <?php echo e($booking->total_pallet_variance == 0 ? 'text-green-600' : ($booking->total_pallet_variance > 0 ? 'text-blue-600' : 'text-red-600')); ?>">
                            <?php echo e(number_format($booking->total_actual_pallets)); ?>

                          </span>
                          <?php if($booking->total_expected_pallets > 0 && $booking->total_pallet_variance != 0): ?>
                            <span class="text-lg <?php echo e($booking->total_pallet_variance > 0 ? 'text-blue-600' : 'text-red-600'); ?>">
                              (<?php echo e($booking->total_pallet_variance > 0 ? '+' : ''); ?><?php echo e(number_format($booking->total_pallet_variance)); ?>)
                            </span>
                          <?php endif; ?>
                        </div>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="text-center py-8 text-gray-500">
            <p>No PO numbers recorded for this booking</p>
          </div>
        <?php endif; ?>
        
        
        <div class="border-t pt-4 mt-6 space-y-3">
          <?php if($booking->container_size): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Container Size</label>
              <p class="text-lg"><?php echo e($booking->container_size); ?>ft</p>
            </div>
          <?php endif; ?>
          
          <?php if($booking->load_type): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Load Type</label>
              <p class="text-lg"><?php echo e($booking->load_type); ?></p>
            </div>
          <?php endif; ?>
          
          <?php if($booking->hazmat): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Special Requirements</label>
              <p class="text-lg text-red-600 font-semibold">⚠️ Hazardous Materials (HAZMAT)</p>
            </div>
          <?php endif; ?>
          
          <?php if($booking->temperature_requirements): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Temperature Requirements</label>
              <p class="text-lg"><?php echo e($booking->temperature_requirements); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      
      <?php if($booking->vehicle_registration || $booking->container_number || $booking->carrier_company || $booking->trailerType): ?>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Transportation & Vehicle Details</h3>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="space-y-3">
              <h4 class="font-medium text-gray-800">Vehicle Information</h4>
              
              <?php if($booking->vehicle_registration): ?>
                <div>
                  <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                  <p class="text-lg font-mono bg-gray-100 px-2 py-1 rounded"><?php echo e($booking->vehicle_registration); ?></p>
                </div>
              <?php endif; ?>
              
              <?php if($booking->carrier_company): ?>
                <div>
                  <label class="text-sm font-medium text-gray-600">Carrier Company</label>
                  <p class="text-lg"><?php echo e($booking->carrier_company); ?></p>
                </div>
              <?php endif; ?>
              
              <?php if($booking->carrier_contact): ?>
                <div>
                  <label class="text-sm font-medium text-gray-600">Carrier Contact</label>
                  <p class="text-lg"><?php echo e($booking->carrier_contact); ?></p>
                </div>
              <?php endif; ?>
            </div>
            
            
            <div class="space-y-3">
              <h4 class="font-medium text-gray-800">Container/Trailer Details</h4>
              
              <?php if($booking->container_number): ?>
                <div>
                  <label class="text-sm font-medium text-gray-600">Container/Trailer Number</label>
                  <p class="text-lg font-mono bg-gray-100 px-2 py-1 rounded"><?php echo e($booking->container_number); ?></p>
                </div>
              <?php endif; ?>
              
              <?php if($booking->trailerType): ?>
                <div>
                  <label class="text-sm font-medium text-gray-600">Trailer Type</label>
                  <p class="text-lg"><?php echo e($booking->trailerType->name); ?></p>
                  <?php if($booking->trailerType->description): ?>
                    <p class="text-sm text-gray-500"><?php echo e($booking->trailerType->description); ?></p>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              
              <?php if($booking->container_size): ?>
                <div>
                  <label class="text-sm font-medium text-gray-600">Container Size</label>
                  <p class="text-lg"><?php echo e($booking->container_size); ?>ft</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          
          <div class="border-t mt-6 pt-4 space-y-3">
            <?php if($booking->gate_number): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Gate Number</label>
                <p class="text-lg"><?php echo e($booking->gate_number); ?></p>
              </div>
            <?php endif; ?>
            
            <?php if($booking->manifest_number): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Manifest Number</label>
                <p class="text-lg font-mono"><?php echo e($booking->manifest_number); ?></p>
              </div>
            <?php endif; ?>
            
            <?php if($booking->estimated_arrival): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Estimated Arrival</label>
                <p class="text-lg"><?php echo e($booking->estimated_arrival->format('d M Y, H:i')); ?></p>
              </div>
            <?php endif; ?>
            
            <?php if($booking->waiting_area_location): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">🅿️ Waiting Area</label>
                <p class="text-lg"><?php echo e($booking->waiting_area_location); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      
      <?php if($booking->special_instructions || $booking->notes): ?>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">📝 Additional Information</h3>
          
          <div class="space-y-3">
            <?php if($booking->special_instructions): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Special Instructions</label>
                <p class="text-base leading-relaxed"><?php echo e($booking->special_instructions); ?></p>
              </div>
            <?php endif; ?>
            
            <?php if($booking->notes): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Notes</label>
                <p class="text-base leading-relaxed"><?php echo e($booking->notes); ?></p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>

      
      <?php if($hasArrived): ?>
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
          <h3 class="text-xl font-semibold mb-4 text-green-800">✅ Arrival Information</h3>
          
          <div class="space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Arrived At</label>
              <p class="text-lg"><?php echo e($booking->arrived_at->format('l, d F Y - H:i')); ?></p>
              <p class="text-sm text-gray-600">Slot: <?php echo e($booking->slot->start_at->format('H:i')); ?></p>
            </div>
            
            <?php if($booking->departed_at): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Departed At</label>
                <p class="text-lg"><?php echo e($booking->departed_at->format('l, d F Y - H:i')); ?></p>
              </div>
              
              <div>
                <label class="text-sm font-medium text-gray-600">Time On-Site</label>
                <div class="flex items-center space-x-2">
                  <p class="text-lg"><?php echo e($booking->arrived_at->diffForHumans($booking->departed_at, true)); ?></p>
                  <?php
                    $slotStart = $booking->slot->start_at;
                    $arrivalTime = $booking->arrived_at;
                    $isLate = $arrivalTime->gt($slotStart);
                    $isEarly = $arrivalTime->lt($slotStart);
                    $timingText = '';
                    
                    if ($isLate || $isEarly) {
                      $totalMinutes = abs($arrivalTime->diffInMinutes($slotStart));
                      
                      if ($totalMinutes >= 1440) {
                        $days = floor($totalMinutes / 1440);
                        $remainingMinutes = $totalMinutes % 1440;
                        $hours = floor($remainingMinutes / 60);
                        $minutes = $remainingMinutes % 60;
                        
                        $timingText .= $days . 'd ';
                        if ($hours > 0) $timingText .= $hours . 'h ';
                        if ($minutes > 0) $timingText .= $minutes . 'm';
                      } elseif ($totalMinutes >= 60) {
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        
                        $timingText .= $hours . 'h ';
                        if ($minutes > 0) $timingText .= $minutes . 'm';
                      } else {
                        $timingText .= $totalMinutes . 'm';
                      }
                      
                      $timingText = trim($timingText);
                    }
                  ?>
                  <?php if($isLate): ?>
                    <span class="text-sm text-red-600 bg-red-50 px-2 py-1 rounded">
                      🚨 <?php echo e($timingText); ?> late
                    </span>
                  <?php elseif($isEarly): ?>
                    <span class="text-sm text-green-600 bg-green-50 px-2 py-1 rounded">
                      ✅ <?php echo e($timingText); ?> early
                    </span>
                  <?php else: ?>
                    <span class="text-sm text-green-600 bg-green-50 px-2 py-1 rounded">
                      ✅ On time
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            <?php else: ?>
              <div class="p-3 bg-blue-100 rounded border border-blue-300">
                <p class="text-blue-800 font-medium">🚛 Currently on-site</p>
              </div>
            <?php endif; ?>
            
            
            <?php if($booking->departed_at && ($booking->departure_vehicle_registration || $booking->departure_driver_name)): ?>
              <div class="mt-4 pt-4 border-t border-green-200">
                <h4 class="font-medium text-green-800 mb-3">🚚 Collection Vehicle</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <?php if($booking->departure_vehicle_registration): ?>
                    <div>
                      <label class="text-xs font-medium text-gray-600">Collection Vehicle</label>
                      <p class="font-mono"><?php echo e($booking->departure_vehicle_registration); ?></p>
                    </div>
                  <?php endif; ?>
                  
                </div>
                
                <?php if($booking->departure_notes): ?>
                  <div class="mt-2">
                    <label class="text-xs font-medium text-gray-600">Departure Notes</label>
                    <p class="text-sm text-gray-700"><?php echo e($booking->departure_notes); ?></p>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>

          </div>
        </div>
      <?php endif; ?>

      
      <?php if($hasArrived): ?>
        <div class="bg-gradient-to-r from-orange-50 to-blue-50 p-6 rounded-lg border border-orange-200 col-span-2">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Tipping Status & Progress</h3>
          
          
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">Current Status</div>
              <div class="text-lg"><?php echo $booking->tipping_status_badge; ?></div>
            </div>
            
            
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">
                <?php if($booking->departed_at || $booking->tipping_status === 'departed'): ?>
                  Last Location
                <?php else: ?>
                  Current Location
                <?php endif; ?>
              </div>
              <div class="text-sm font-semibold">
                <?php if($booking->tippingBay): ?>
                  🏭 <?php echo e($booking->tippingBay->name); ?>

                <?php elseif($booking->tippingLocation): ?>
                  📍 <?php echo e($booking->tippingLocation->name); ?>

                <?php else: ?>
                  <span class="text-gray-400">Not assigned</span>
                <?php endif; ?>
              </div>
            </div>
            
            
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">Time on Site</div>
              <div class="text-sm font-semibold <?php echo e($booking->arrived_at->diffInHours() > 4 ? 'text-orange-600' : 'text-gray-800'); ?>">
                <?php echo e($booking->arrived_at->diffForHumans(null, true)); ?>

              </div>
              <?php
                // Use ArrivalTimeSetting to determine proper timing status
                $statusDetails = \App\Models\ArrivalTimeSetting::getArrivalStatusDetails(
                  $booking->slot->start_at,
                  $booking->arrived_at,
                  $booking->customer_id,
                  $booking->slot->depot_id
                );
                
                $totalMinutes = $statusDetails['difference_minutes'];
                $status = $statusDetails['status'];
                
                // Format the timing text
                $timingText = '';
                if ($totalMinutes >= 1440) {
                  $days = floor($totalMinutes / 1440);
                  $remainingMinutes = $totalMinutes % 1440;
                  $hours = floor($remainingMinutes / 60);
                  $minutes = $remainingMinutes % 60;
                  
                  $timingText .= $days . 'd ';
                  if ($hours > 0) $timingText .= $hours . 'h ';
                  if ($minutes > 0) $timingText .= $minutes . 'm';
                } elseif ($totalMinutes >= 60) {
                  $hours = floor($totalMinutes / 60);
                  $minutes = $totalMinutes % 60;
                  
                  $timingText .= $hours . 'h ';
                  if ($minutes > 0) $timingText .= $minutes . 'm';
                } else {
                  $timingText .= $totalMinutes . 'm';
                }
                
                $timingText = trim($timingText);
              ?>
              
              <?php if($status === \App\Models\ArrivalTimeSetting::STATUS_LATE): ?>
                <div class="text-xs text-red-600 mt-1">
                  🚨 <?php echo e($timingText); ?> late
                </div>
              <?php elseif($status === \App\Models\ArrivalTimeSetting::STATUS_EARLY): ?>
                <div class="text-xs text-yellow-600 font-bold mt-1">
                  ✅ <?php echo e($timingText); ?> early
                </div>
              <?php else: ?>
                <div class="text-xs text-green-600 mt-1">
                  ✅ On time
                </div>
              <?php endif; ?>
            </div>
            
            
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">Tipping Performance</div>
              <div class="text-sm font-semibold">
                <?php
                  // Calculate tipping performance with sophisticated rules
                  $slotStart = $booking->slot->start_at;
                  $slotEnd = $booking->slot->end_at;
                  $arrivalTime = $booking->arrived_at;
                  $actualTipStart = $booking->tipping_started_at;
                  $actualTipEnd = $booking->tipping_completed_at;
                  
                  // Check if trailer was dropped on site (always ontime but show duration)
                  $movement = $booking->movements()->first();
                  $isDroppedTrailer = $movement && in_array($movement->current_status, ['trailer_dropped', 'empty']) && $actualTipEnd;
                  
                  if ($isDroppedTrailer) {
                    $performanceStatus = 'ontime_tip';
                    $performanceText = '📍 Dropped Trailer - Always Ontime';
                    $performanceClass = 'text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs';
                    // Show actual tipping duration for dropped trailers
                    if ($booking->actual_tipping_duration) {
                      $performanceText .= ' (' . $booking->actual_tipping_duration . ' mins)';
                    }
                  } elseif ($actualTipEnd && $arrivalTime) {
                    // Calculate extended deadline based on arrival delay
                    // Handle case where arrival is after slot start time (considering dates)
                    if ($arrivalTime->gt($slotStart)) {
                      $arrivalDelay = $slotStart->diffInMinutes($arrivalTime);
                    } else {
                      $arrivalDelay = 0; // Early or on-time
                    }
                    $adjustedDeadline = $slotEnd->copy()->addMinutes($arrivalDelay); // Extend deadline by delay
                    
                    // Compare actual tipping completion to adjusted deadline
                    $onTime = $actualTipEnd->lte($adjustedDeadline);
                    $performanceStatus = $onTime ? 'ontime' : 'late';
                    
                    if ($arrivalDelay > 0) {
                      // Late arrival - show extended time calculation
                      $delayHours = floor($arrivalDelay / 60);
                      $delayMins = $arrivalDelay % 60;
                      $delayText = $delayHours > 0 ? "{$delayHours}h {$delayMins}m" : "{$delayMins}m";
                      
                      $performanceText = $onTime 
                        ? "✅ Ontime (Extended +{$delayText})" 
                        : "🚨 Late (Even with +{$delayText} extension)";
                      $performanceClass = $onTime ? 'text-green-600' : 'text-red-600';
                    } else {
                      // Early/on-time arrival - distinguish between early and exactly on time
                      if ($arrivalTime->lt($slotStart)) {
                        // Early arrival
                        $performanceText = $onTime ? '🟡 Early (Ontime)' : '🚨 Late';
                        $performanceClass = $onTime ? 'text-orange-600' : 'text-red-600';
                      } else {
                        // Exactly on time
                        $performanceText = $onTime ? '✅ Ontime' : '🚨 Late';
                        $performanceClass = $onTime ? 'text-green-600' : 'text-red-600';
                      }
                    }
                    
                    // Override class if not set above
                    if (!isset($performanceClass)) {
                      $performanceClass = $onTime ? 'text-green-600' : 'text-red-600';
                    }
                  } elseif ($actualTipStart) {
                    $performanceText = $booking->actual_tipping_duration . ' mins (ongoing)';
                    $performanceClass = 'text-orange-600';
                  } else {
                    $performanceText = 'Not started';
                    $performanceClass = 'text-gray-400';
                  }
                ?>
                
                <span class="<?php echo e($performanceClass ?? 'text-gray-400'); ?>">
                  <?php echo e($performanceText ?? 'Not started'); ?>

                </span>
                
<?php if($booking->actual_tipping_duration): ?>
                  <div class="text-xs text-gray-500 mt-1">
                    <?php if($isDroppedTrailer): ?>
                      Tipping Duration: <?php echo e($booking->actual_tipping_duration); ?> minutes
                    <?php else: ?>
                      Duration: <?php echo e($booking->actual_tipping_duration); ?> minutes
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                
                <?php if(!$isDroppedTrailer && $arrivalTime && $actualTipEnd): ?>
                  <div class="text-xs text-gray-400 mt-1">
                    <?php
                      $originalDeadline = $slotEnd;
                      $arrivalDelayMins = $arrivalTime->gt($slotStart) ? $arrivalTime->diffInMinutes($slotStart) : 0;
                      $adjustedDeadline = $slotEnd->copy()->addMinutes($arrivalDelayMins);
                    ?>
                    
                    Slot: <?php echo e($slotStart->format('H:i')); ?>-<?php echo e($originalDeadline->format('H:i')); ?>

                    <?php if($arrivalDelayMins > 0): ?>
                      | Arrived: <?php echo e($arrivalTime->format('H:i')); ?> (+<?php echo e(floor($arrivalDelayMins/60) ? floor($arrivalDelayMins/60).'h ' : ''); ?><?php echo e($arrivalDelayMins%60); ?>m)
                      | Extended to: <?php echo e($adjustedDeadline->format('H:i')); ?>

                    <?php endif; ?>
                    | Completed: <?php echo e($actualTipEnd->format('H:i')); ?>

                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          
          <div class="bg-white p-4 rounded-lg border shadow-sm">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center">
              <span class="mr-2">📋</span>
              Progress Timeline
            </h4>
            
            <div class="space-y-3">
              
              <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full text-sm font-semibold mr-4">
                  ✓
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Vehicle Arrived</div>
                  <div class="text-xs text-gray-500"><?php echo e($booking->arrived_at->format('M j, H:i')); ?></div>
                </div>
              </div>

              
              <div class="flex items-center <?php echo e($booking->trailer_dropped_at ? '' : 'opacity-50'); ?>">
                <div class="flex items-center justify-center w-8 h-8 <?php echo e($booking->trailer_dropped_at ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400'); ?> rounded-full text-sm font-semibold mr-4">
                  <?php echo e($booking->trailer_dropped_at ? '✓' : '2'); ?>

                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Trailer Dropped</div>
                  <div class="text-xs text-gray-500">
                    <?php if($booking->trailer_dropped_at): ?>
                      <?php echo e($booking->trailer_dropped_at->format('M j, H:i')); ?>

                      <?php if($booking->tippingLocation): ?>
                        at <?php echo e($booking->tippingLocation->name); ?>

                      <?php endif; ?>
                    <?php else: ?>
                      Pending
                    <?php endif; ?>
                  </div>
                </div>
                <?php if($booking->tipping_status === 'arrived' && !$booking->trailer_dropped_at): ?>
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                <?php endif; ?>
              </div>

              
              <div class="flex items-center <?php echo e($booking->moved_to_bay_at ? '' : 'opacity-50'); ?>">
                <div class="flex items-center justify-center w-8 h-8 <?php echo e($booking->moved_to_bay_at ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-400'); ?> rounded-full text-sm font-semibold mr-4">
                  <?php echo e($booking->moved_to_bay_at ? '✓' : '3'); ?>

                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Moved to Tipping Bay</div>
                  <div class="text-xs text-gray-500">
                    <?php if($booking->moved_to_bay_at): ?>
                      <?php echo e($booking->moved_to_bay_at->format('M j, H:i')); ?>

                      <?php if($booking->tippingBay): ?>
                        - <?php echo e($booking->tippingBay->name); ?>

                      <?php endif; ?>
                    <?php else: ?>
                      Pending
                    <?php endif; ?>
                  </div>
                </div>
                <?php if($booking->tipping_status === 'trailer_dropped'): ?>
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                <?php endif; ?>
              </div>

              
              <div class="flex items-center <?php echo e($booking->tipping_started_at ? '' : 'opacity-50'); ?>">
                <div class="flex items-center justify-center w-8 h-8 <?php echo e($booking->tipping_started_at ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-400'); ?> rounded-full text-sm font-semibold mr-4">
                  <?php echo e($booking->tipping_started_at ? '✓' : '4'); ?>

                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Tipping Started</div>
                  <div class="text-xs text-gray-500">
                    <?php if($booking->tipping_started_at): ?>
                      <?php echo e($booking->tipping_started_at->format('M j, H:i')); ?>

                    <?php else: ?>
                      Pending
                    <?php endif; ?>
                  </div>
                </div>
                <?php if($booking->tipping_status === 'at_bay'): ?>
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                <?php endif; ?>
              </div>

              
              <div class="flex items-center <?php echo e($booking->tipping_completed_at ? '' : 'opacity-50'); ?>">
                <div class="flex items-center justify-center w-8 h-8 <?php echo e($booking->tipping_completed_at ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-400'); ?> rounded-full text-sm font-semibold mr-4">
                  <?php echo e($booking->tipping_completed_at ? '✓' : '5'); ?>

                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Tipping Completed</div>
                  <div class="text-xs text-gray-500">
                    <?php if($booking->tipping_completed_at): ?>
                      <?php echo e($booking->tipping_completed_at->format('M j, H:i')); ?>

                      <?php if($booking->actual_tipping_duration): ?>
                        (<?php echo e($booking->actual_tipping_duration); ?> minutes)
                      <?php endif; ?>
                    <?php else: ?>
                      Pending
                    <?php endif; ?>
                  </div>
                </div>
                <?php if($booking->tipping_status === 'unloading'): ?>
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                <?php endif; ?>
              </div>

              
              <div class="flex items-center <?php echo e(in_array($booking->tipping_status, ['empty', 'departed']) ? '' : 'opacity-50'); ?>">
                <div class="flex items-center justify-center w-8 h-8 <?php echo e(in_array($booking->tipping_status, ['empty', 'departed']) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400'); ?> rounded-full text-sm font-semibold mr-4">
                  <?php echo e(in_array($booking->tipping_status, ['empty', 'departed']) ? '✓' : '6'); ?>

                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">
                    <?php if($booking->tipping_status === 'departed'): ?>
                      Departed from Site
                    <?php else: ?>
                      Ready for Collection
                    <?php endif; ?>
                  </div>
                  <div class="text-xs text-gray-500">
                    <?php if($booking->tipping_status === 'departed'): ?>
                      <?php if($booking->trailer_departed_at): ?>
                        <?php echo e($booking->trailer_departed_at->format('M j, H:i')); ?>

                        <?php if($booking->trailer_left_on_site): ?>
                          (trailer left on site)
                        <?php else: ?>
                          (vehicle & trailer departed)
                        <?php endif; ?>
                      <?php else: ?>
                        Departed
                      <?php endif; ?>
                    <?php elseif($booking->tipping_status === 'empty'): ?>
                      Empty trailer awaiting collection
                    <?php else: ?>
                      Pending completion
                    <?php endif; ?>
                  </div>
                </div>
                <?php if(in_array($booking->tipping_status, ['empty', 'departed'])): ?>
                  <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Complete</div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          
          <?php if($booking->tipping_notes || $booking->tipping_status !== 'not_started'): ?>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              <?php if($booking->tipping_notes): ?>
                <div class="bg-white p-4 rounded-lg border shadow-sm">
                  <h5 class="font-medium text-gray-800 mb-2 flex items-center">
                    <span class="mr-2">📝</span>
                    Tipping Notes
                  </h5>
                  <div class="text-sm text-gray-700 whitespace-pre-line"><?php echo e($booking->tipping_notes); ?></div>
                </div>
              <?php endif; ?>
              
              <?php if($booking->tipping_status !== 'departed'): ?>
                <div class="bg-white p-4 rounded-lg border shadow-sm">
                  <h5 class="font-medium text-gray-800 mb-2 flex items-center">
                    <span class="mr-2">🚛</span>
                    Tipping Operations
                  </h5>
                  <div class="text-sm text-gray-600 mb-3">
                    All tipping operations are managed through the centralized workflow interface.
                  </div>
                  <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                     class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                    🚛 Manage Tipping Workflow
                  </a>
                </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
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

  
  <div id="departureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-purple-800">🏁 Record Departure</h3>
      <form id="departureForm" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>
        
        
        <?php if($booking->tipping_type): ?>
          <div class="mb-4 p-3 bg-blue-50 rounded-lg">
            <div class="text-sm font-medium text-blue-800">
              Tipping Type: 
              <?php if($booking->tipping_type === 'live_tip'): ?>
                <span class="inline-flex items-center">🚛📦 Live Tip</span>
              <?php else: ?>
                <span class="inline-flex items-center">📦 Drop</span>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Departure *</label>
          <div class="space-y-2">
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="completed_with_trailer" class="mr-2" checked>
              <span class="text-sm">🚛 Same vehicle & trailer departed together</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="completed_dropped_trailer" class="mr-2" id="droppedTrailerOption">
              <span class="text-sm">📍 Trailer dropped - vehicle departed solo</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="trailer_swap" class="mr-2" id="trailerSwapOption">
              <span class="text-sm">🔄 Vehicle collected different trailer</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="emergency_departure" class="mr-2">
              <span class="text-sm text-red-600">🚨 Emergency departure</span>
            </label>
          </div>
        </div>
        
        <div id="trailerLocationField" class="mb-4 hidden">
          <label class="block text-sm font-medium text-gray-700 mb-2">Trailer Drop Location</label>
          <input type="text" name="dropped_trailer_location" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md"
                 placeholder="e.g., PARK1, YARD-A, etc.">
        </div>
        
        <div id="trailerSwapField" class="mb-4 hidden">
          <label class="block text-sm font-medium text-gray-700 mb-2">Collected Trailer Details</label>
          <input type="text" name="collected_trailer_number" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md"
                 placeholder="Enter trailer/container number">
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
          <textarea name="departure_notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                    placeholder="Optional notes about the departure..."></textarea>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeDepartureModal()"
                  class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Record Departure
          </button>
        </div>
      </form>
    </div>
  </div>

  
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
      document.getElementById('emailAddress').value = '<?php echo e(auth()->user()->email); ?>';
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
      fetch('<?php echo e(route("admin.bookings.email-pdf", $booking)); ?>', {
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
      fetch('<?php echo e(route("admin.bookings.cancel", $booking)); ?>', {
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

    // Departure modal functions
    function openDepartureModal(bookingId) {
      document.getElementById('departureModal').classList.remove('hidden');
      document.getElementById('departureModal').classList.add('flex');
      document.getElementById('departureForm').action = `/admin/bookings/${bookingId}/departure`;
    }
    
    function closeDepartureModal() {
      document.getElementById('departureModal').classList.add('hidden');
      document.getElementById('departureModal').classList.remove('flex');
    }

    // Show/hide trailer location and swap fields based on radio selection
    document.querySelectorAll('input[name="departure_scenario"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
        const trailerLocationField = document.getElementById('trailerLocationField');
        const trailerSwapField = document.getElementById('trailerSwapField');
        
        // Hide all fields first
        trailerLocationField.classList.add('hidden');
        trailerSwapField.classList.add('hidden');
        
        // Show appropriate field based on selection
        if (this.value === 'completed_dropped_trailer') {
          trailerLocationField.classList.remove('hidden');
        } else if (this.value === 'trailer_swap') {
          trailerSwapField.classList.remove('hidden');
        }
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
    
    document.getElementById('departureModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeDepartureModal();
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/show.blade.php ENDPATH**/ ?>