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
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">WM</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Warehouse Manager</h1>
              <p class="text-sm text-gray-600">Customer Portal</p>
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
        ?>
        
        
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Documents</span>
          <a href="<?php echo e(route('customer.bookings.download-pdf', $booking)); ?>"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📄 PDF
          </a>
          <button onclick="emailBookingPDF(<?php echo e($booking->id); ?>)"
                  class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
            📧 Email
          </button>
        </div>
        
        
        <div class="flex items-center space-x-2 bg-blue-50 p-2 rounded-lg border border-blue-200">
          <span class="text-xs font-medium text-blue-700 uppercase">Management</span>
          
          <?php if($booking->cancelled_at): ?>
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-400 text-white text-sm font-medium rounded-md cursor-not-allowed">
              ❌ Cancelled
            </span>
          <?php else: ?>
            <?php if(!$hasArrived && !$isLocked && auth()->user()->can('update', $booking)): ?>
              <a href="<?php echo e(route('customer.bookings.edit', $booking)); ?>"
                 class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                ✏️ Edit
              </a>
            <?php endif; ?>
            
            <a href="<?php echo e(route('customer.bookings.rebook.show', $booking)); ?>"
               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
              🔄 <?php echo e($hasArrived ? 'Rebook/Reject' : 'Rebook'); ?>

            </a>
            
            <button onclick="showCancelModal()" 
                    class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
              ❌ <?php echo e($hasArrived ? 'Cancel/Reject' : 'Cancel'); ?>

            </button>
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
            <a href="<?php echo e(route('customer.bookings.history', $booking)); ?>"
               class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
              📋 History
            </a>
          <?php endif; ?>
        </div>
        
        
        <div class="flex items-center ml-auto">
          <a href="<?php echo e(route('customer.bookings.index')); ?>"
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
      </div>
    <?php elseif($isLocked): ?>
      <div class="mb-6 p-4 bg-orange-100 border border-orange-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-orange-600 text-2xl mr-3">🔒</span>
          <div>
            <h3 class="text-lg font-semibold text-orange-800">Booking Locked</h3>
            <p class="text-orange-700">
              This booking is locked and cannot be edited. Cut-off time: <?php echo e($booking->slot->locked_at->format('d M Y, H:i')); ?>

            </p>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-blue-600 text-2xl mr-3">📅</span>
          <div>
            <h3 class="text-lg font-semibold text-blue-800">Booking Confirmed</h3>
            <p class="text-blue-700">Your booking is confirmed and can be edited until the cut-off time.</p>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      
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
          
          <?php if($booking->reference): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Reference</label>
              <p class="text-lg font-mono"><?php echo e($booking->reference); ?></p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📦 Load Details</h3>
        
        <div class="space-y-4">
          
          <?php if($booking->poNumbers->count() > 0): ?>
            <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-start mb-3">
                  <h4 class="font-medium text-gray-800">PO: <?php echo e($po->po_number); ?></h4>
                  <?php if($po->hasVariance()): ?>
                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Has Variance</span>
                  <?php endif; ?>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="font-medium text-gray-600">Expected:</span>
                    <div class="mt-1 text-gray-800">
                      <?php echo str_replace('<br>', '<br>', $po->expected_summary_text); ?>

                    </div>
                  </div>
                  
                  <?php if($po->total_actual_units > 0 || $po->total_actual_pallets > 0): ?>
                    <div>
                      <span class="font-medium text-gray-600">Actual:</span>
                      <div class="mt-1 text-green-600">
                        <?php echo str_replace('<br>', '<br>', $po->actual_summary_text); ?>

                      </div>
                    </div>
                  <?php endif; ?>
                </div>
                
                <?php if($po->hasVariance()): ?>
                  <div class="mt-3 p-2 bg-yellow-50 rounded border border-yellow-200">
                    <div class="text-xs font-medium text-yellow-800 mb-1">Variances:</div>
                    <?php if($po->total_unit_variance != 0): ?>
                      <div class="text-xs text-yellow-700">
                        Units: <?php echo e($po->total_unit_variance > 0 ? '+' : ''); ?><?php echo e($po->total_unit_variance); ?>

                      </div>
                    <?php endif; ?>
                    <?php if($po->total_pallet_variance != 0): ?>
                      <div class="text-xs text-yellow-700">
                        Pallets: <?php echo e($po->total_pallet_variance > 0 ? '+' : ''); ?><?php echo e($po->total_pallet_variance); ?>

                      </div>
                    <?php endif; ?>
                    <?php if($po->hasTypeVariances()): ?>
                      <div class="text-xs text-yellow-700">
                        Type variances: <?php echo e(implode(', ', $po->type_variances)); ?>

                      </div>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
            <div class="text-gray-500 text-center py-4">
              No PO numbers specified for this booking
            </div>
          <?php endif; ?>
          
          <?php if($booking->container_size): ?>
            <div>
              <label class="text-sm font-medium text-gray-600">Container Size</label>
              <p class="text-lg"><?php echo e(number_format($booking->container_size)); ?> kg</p>
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

      
      <?php if($booking->vehicle_registration || $booking->driver_name || $booking->carrier_company): ?>
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Transportation</h3>
          
          <div class="space-y-3">
            <?php if($booking->vehicle_registration): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                <p class="text-lg font-mono"><?php echo e($booking->vehicle_registration); ?></p>
              </div>
            <?php endif; ?>
            
            <?php if($booking->container_number): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Container Number</label>
                <p class="text-lg font-mono"><?php echo e($booking->container_number); ?></p>
              </div>
            <?php endif; ?>
            
            
            <?php if($booking->carrier_company): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Carrier Company</label>
                <p class="text-lg"><?php echo e($booking->carrier_company); ?></p>
              </div>
            <?php endif; ?>
            
            <?php if($booking->estimated_arrival): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Estimated Arrival</label>
                <p class="text-lg"><?php echo e($booking->estimated_arrival->format('d M Y, H:i')); ?></p>
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
            </div>
            
            <?php if($booking->departed_at): ?>
              <div>
                <label class="text-sm font-medium text-gray-600">Departed At</label>
                <p class="text-lg"><?php echo e($booking->departed_at->format('l, d F Y - H:i')); ?></p>
              </div>
              
              <div>
                <label class="text-sm font-medium text-gray-600">Time On-Site</label>
                <p class="text-lg"><?php echo e($booking->arrived_at->diffForHumans($booking->departed_at, true)); ?></p>
              </div>
            <?php else: ?>
              <div class="p-3 bg-blue-100 rounded border border-blue-300">
                <p class="text-blue-800 font-medium">🚛 Currently on-site</p>
              </div>
            <?php endif; ?>
          </div>
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
      fetch('<?php echo e(route("customer.bookings.email-pdf", $booking)); ?>', {
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/customer/bookings/show.blade.php ENDPATH**/ ?>