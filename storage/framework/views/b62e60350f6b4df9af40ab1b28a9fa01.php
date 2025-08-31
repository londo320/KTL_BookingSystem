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
      <h2 class="font-semibold text-xl">Booking History #<?php echo e($booking->id); ?></h2>
      <a href="<?php echo e(route('customer.bookings.show', $booking)); ?>"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Booking
      </a>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-6xl mx-auto">
    
    
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Current Booking Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-sm text-gray-600">Booking ID</p>
          <p class="font-medium">#<?php echo e($booking->id); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium"><?php echo e($booking->customer->name ?? 'Not assigned'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Status</p>
          <p class="font-medium">
            <?php if($booking->arrived_at): ?>
              <span class="text-green-600">✅ Arrived</span>
            <?php elseif($booking->isCancelled()): ?>
              <span class="text-red-600">❌ Cancelled</span>
            <?php else: ?>
              <span class="text-blue-600">📅 Active</span>
            <?php endif; ?>
          </p>
        </div>
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
          <p class="text-sm text-gray-600">Rebook Count</p>
          <p class="font-medium">
            <?php echo e($actualRebookCount ?? $booking->rebook_count ?? 0); ?> / <?php echo e($maxRebooksPerBooking); ?>

            <?php if(isset($actualRebookCount) && $actualRebookCount != ($booking->rebook_count ?? 0)): ?>
              <span class="text-xs text-orange-600" title="Field: <?php echo e($booking->rebook_count ?? 'null'); ?>, Calculated: <?php echo e($actualRebookCount); ?>">
                (corrected)
              </span>
            <?php endif; ?>
          </p>
        </div>
      </div>
    </div>

    
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">📋 Complete Timeline</h3>
            <p class="text-sm text-gray-600 mt-1">
              All actions and changes made to this booking (<?php echo e($history->count()); ?> entries)
            </p>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Sort:</span>
            <div class="flex bg-gray-100 rounded-lg p-1">
              <a href="<?php echo e(route('customer.bookings.history', $booking)); ?>?sort=asc" 
                 class="px-3 py-1 text-xs rounded-md <?php echo e($sortOrder === 'asc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800'); ?>">
                📈 Oldest First
              </a>
              <a href="<?php echo e(route('customer.bookings.history', $booking)); ?>?sort=desc" 
                 class="px-3 py-1 text-xs rounded-md <?php echo e($sortOrder === 'desc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800'); ?>">
                📉 Newest First
              </a>
            </div>
          </div>
        </div>
      </div>

      <?php if($history->isEmpty()): ?>
        <div class="p-6 text-center text-gray-500">
          <p>No history records found for this booking.</p>
        </div>
      <?php else: ?>
        <div class="divide-y divide-gray-200">
          <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="p-6">
              <div class="flex items-start space-x-4">
                
                <div class="flex-shrink-0">
                  <?php if($record->action === 'created'): ?>
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <span class="text-green-600 text-sm">➕</span>
                    </div>
                  <?php elseif($record->action === 'rebooked'): ?>
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                      <span class="text-orange-600 text-sm">🔄</span>
                    </div>
                  <?php elseif($record->action === 'cancelled'): ?>
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                      <span class="text-red-600 text-sm">❌</span>
                    </div>
                  <?php elseif($record->action === 'modified'): ?>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <span class="text-blue-600 text-sm">✏️</span>
                    </div>
                  <?php else: ?>
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                      <span class="text-gray-600 text-sm">📝</span>
                    </div>
                  <?php endif; ?>
                </div>

                
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-medium text-gray-900 capitalize">
                      <?php echo e($record->action); ?>

                      <?php if($record->is_last_minute): ?>
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                          ⚡ Last Minute
                        </span>
                      <?php endif; ?>
                    </h4>
                    <div class="text-right">
                      <p class="text-sm text-gray-500">
                        <?php echo e($record->created_at->format('d M Y, H:i')); ?>

                      </p>
                      <?php if($record->hours_before_slot !== null): ?>
                        <p class="text-xs text-gray-400">
                          <?php echo e($record->hours_before_slot); ?>h before slot
                        </p>
                      <?php endif; ?>
                    </div>
                  </div>

                  
                  <?php if($record->action === 'created'): ?>
                    <p class="text-sm text-gray-700 mb-2">
                      Booking was created for slot on 
                      <strong><?php echo e($record->booking->slot->start_at->format('D, d M Y - H:i')); ?></strong>
                      at <?php echo e($record->booking->slot->depot->name); ?>

                    </p>
                  <?php elseif($record->action === 'rebooked'): ?>
                    <div class="text-sm text-gray-700 mb-2">
                      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-2">
                        <div class="flex items-center justify-between mb-2">
                          <span class="text-yellow-800 font-semibold">📅 Slot Change</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                          <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">FROM</div>
                            <?php if($record->originalSlot): ?>
                              <div class="bg-red-100 border border-red-300 rounded p-2">
                                <div class="font-semibold text-red-800"><?php echo e($record->originalSlot->depot->name); ?></div>
                                <div class="text-sm text-red-700"><?php echo e($record->originalSlot->start_at->format('D, d M Y')); ?></div>
                                <div class="text-lg font-bold text-red-800"><?php echo e($record->originalSlot->start_at->format('H:i')); ?></div>
                              </div>
                            <?php endif; ?>
                          </div>
                          <div class="flex items-center justify-center md:mt-6">
                            <div class="text-2xl">➡️</div>
                          </div>
                          <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">TO</div>
                            <?php if($record->newSlot): ?>
                              <div class="bg-green-100 border border-green-300 rounded p-2">
                                <div class="font-semibold text-green-800"><?php echo e($record->newSlot->depot->name); ?></div>
                                <div class="text-sm text-green-700"><?php echo e($record->newSlot->start_at->format('D, d M Y')); ?></div>
                                <div class="text-lg font-bold text-green-800"><?php echo e($record->newSlot->start_at->format('H:i')); ?></div>
                              </div>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php elseif($record->action === 'cancelled'): ?>
                    <p class="text-sm text-gray-700 mb-2">
                      Booking was cancelled
                      <?php if($record->originalSlot): ?>
                        for slot on <strong><?php echo e($record->originalSlot->start_at->format('D, d M Y - H:i')); ?></strong>
                        at <?php echo e($record->originalSlot->depot->name); ?>

                      <?php endif; ?>
                    </p>
                  <?php elseif($record->action === 'modified'): ?>
                    <p class="text-sm text-gray-700 mb-2">
                      Booking details were updated
                      <?php if($record->changes): ?>
                        <span class="text-xs text-gray-500">(<?php echo e(count($record->changes)); ?> changes made)</span>
                      <?php endif; ?>
                    </p>
                  <?php endif; ?>

                  
                  <?php if($record->reason): ?>
                    <div class="mb-2">
                      <p class="text-sm font-medium text-gray-600">Reason:</p>
                      <p class="text-sm text-gray-700 italic"><?php echo e($record->reason); ?></p>
                    </div>
                  <?php endif; ?>

                  
                  <div class="flex items-center space-x-4 text-xs text-gray-500">
                    <?php if($record->customer_rebook_count_30days > 0): ?>
                      <span>Customer rebooks (30d): <?php echo e($record->customer_rebook_count_30days); ?></span>
                    <?php endif; ?>
                    <?php if($record->action === 'rebooked' && $record->is_last_minute): ?>
                      <span class="text-red-600">⚡ Less than 24h notice</span>
                    <?php endif; ?>
                  </div>

                  
                  <?php if($record->action === 'modified' && $record->changes && count($record->changes) > 0): ?>
                    <div class="mt-3 p-3 bg-gray-50 rounded border text-xs">
                      <p class="font-medium text-gray-600 mb-1">Changes made:</p>
                      <div class="space-y-1">
                        <?php $__currentLoopData = $record->changes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if(is_array($change) && (isset($change['old']) || isset($change['new']))): ?>
                            
                            <p>
                              <strong><?php echo e(ucfirst(str_replace('_', ' ', $field))); ?>:</strong>
                              "<?php echo e($change['old'] ?? '(empty)'); ?>" → "<?php echo e($change['new'] ?? '(empty)'); ?>"
                            </p>
                          <?php elseif(!is_array($change)): ?>
                            
                            <p>
                              <strong><?php echo e(ucfirst(str_replace('_', ' ', $field))); ?>:</strong>
                              <?php echo e($change ?: '(empty)'); ?>

                            </p>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </div>
                    </div>
                  <?php elseif($record->action === 'modified'): ?>
                    <div class="mt-3 p-3 bg-blue-50 rounded border text-xs">
                      <p class="text-blue-700 italic">
                        <strong>Note:</strong> Detailed change information not recorded for this update.
                      </p>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>
    </div>

    
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-blue-600"><?php echo e($history->where('action', 'created')->count()); ?></div>
        <div class="text-sm text-gray-600">Created</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-orange-600"><?php echo e($history->where('action', 'rebooked')->count()); ?></div>
        <div class="text-sm text-gray-600">Rebooked</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-red-600"><?php echo e($history->where('action', 'cancelled')->count()); ?></div>
        <div class="text-sm text-gray-600">Cancelled</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-red-600"><?php echo e($history->where('is_last_minute', true)->count()); ?></div>
        <div class="text-sm text-gray-600">Last Minute</div>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/customer/bookings/history.blade.php ENDPATH**/ ?>