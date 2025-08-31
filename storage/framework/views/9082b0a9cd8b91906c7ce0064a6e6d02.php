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
   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Factory Booking History #<?php echo e($factoryBooking->reference); ?></h2>
      <a href="<?php echo e(route('app.factory-bookings.show', $factoryBooking)); ?>"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Factory Booking
      </a>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-6xl mx-auto">
    
    <div class="mb-6 p-6 bg-orange-50 border border-orange-200 rounded-lg">
      <h3 class="text-lg font-semibold text-orange-800 mb-3">🏭 Factory Delivery Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-sm text-gray-600">Reference</p>
          <p class="font-medium">#<?php echo e($factoryBooking->reference); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium"><?php echo e($factoryBooking->customer->name ?? 'Not assigned'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Status</p>
          <p class="font-medium">
            <?php if($factoryBooking->departed_at): ?>
              <span class="text-purple-600">🏁 Departed</span>
            <?php elseif($factoryBooking->completed_at): ?>
              <span class="text-green-600">✅ Completed</span>
            <?php elseif($factoryBooking->processing_started_at): ?>
              <span class="text-orange-600">⚡ Processing</span>
            <?php elseif($factoryBooking->arrived_at): ?>
              <span class="text-blue-600">🚪 Arrived</span>
            <?php else: ?>
              <span class="text-gray-600">📅 Registered</span>
            <?php endif; ?>
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium"><?php echo e($factoryBooking->depot->name); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Vehicle</p>
          <p class="font-medium"><?php echo e($factoryBooking->vehicle_registration ?? 'Not specified'); ?></p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Trailer</p>
          <p class="font-medium"><?php echo e($factoryBooking->trailer_registration ?? 'Not specified'); ?></p>
        </div>
      </div>
    </div>

    
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">📋 Complete Timeline</h3>
            <p class="text-sm text-gray-600 mt-1">
              All actions and changes made to this factory booking (<?php echo e($history->count()); ?> entries)
            </p>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Sort:</span>
            <div class="flex bg-gray-100 rounded-lg p-1">
              <a href="<?php echo e(route('app.factory-bookings.history', $factoryBooking)); ?>?sort=asc" 
                 class="px-3 py-1 text-xs rounded-md <?php echo e($sortOrder === 'asc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800'); ?>">
                📈 Oldest First
              </a>
              <a href="<?php echo e(route('app.factory-bookings.history', $factoryBooking)); ?>?sort=desc" 
                 class="px-3 py-1 text-xs rounded-md <?php echo e($sortOrder === 'desc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800'); ?>">
                📉 Newest First
              </a>
            </div>
          </div>
        </div>
      </div>

      <?php if($history->isEmpty()): ?>
        <div class="p-6 text-center text-gray-500">
          <p>No history records found for this factory booking.</p>
        </div>
      <?php else: ?>
        
        <div class="mb-6">
          <div class="bg-white rounded-lg p-4 border">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">🚛 Movement Timeline</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              
              <div class="text-center p-3 bg-orange-50 rounded-lg border">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                  <span class="text-orange-600 text-sm">🏭</span>
                </div>
                <div class="text-sm font-medium text-orange-800">Factory Booking</div>
                <div class="text-xs text-gray-600"><?php echo e($factoryBooking->created_at->format('d M Y, H:i')); ?></div>
                <div class="text-xs text-gray-500"><?php echo e($factoryBooking->depot->name); ?></div>
              </div>

              
              <?php if($factoryBooking->arrived_at): ?>
                <div class="text-center p-3 bg-blue-50 rounded-lg border">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-blue-800">Site Arrival</div>
                  <div class="text-xs text-gray-600"><?php echo e($factoryBooking->arrived_at->format('d M Y, H:i')); ?></div>
                  <?php if($factoryBooking->vehicle_registration): ?>
                    <div class="text-xs text-gray-500"><?php echo e($factoryBooking->vehicle_registration); ?></div>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Arrival</div>
                  <div class="text-xs text-gray-400">Factory delivery</div>
                </div>
              <?php endif; ?>

              
              <?php
                $movement = $factoryBooking->movements->last();
                $tippingCompleted = $movement && $movement->unloading_completed_at;
                $tippingStarted = $movement && $movement->unloading_started_at;
              ?>
              <?php if($tippingCompleted): ?>
                <div class="text-center p-3 bg-purple-50 rounded-lg border">
                  <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-purple-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-purple-800">Processing Complete</div>
                  <div class="text-xs text-gray-600"><?php echo e($movement->unloading_completed_at->format('d M Y, H:i')); ?></div>
                  <?php if($tippingStarted): ?>
                    <?php
                      $durationMinutes = round($movement->unloading_started_at->diffInMinutes($movement->unloading_completed_at));
                      if ($durationMinutes >= 10080) {
                        $weeks = floor($durationMinutes / 10080);
                        $days = floor(($durationMinutes % 10080) / 1440);
                        $formattedDuration = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                      } elseif ($durationMinutes >= 1440) {
                        $days = floor($durationMinutes / 1440);
                        $hours = floor(($durationMinutes % 1440) / 60);
                        $mins = $durationMinutes % 60;
                        $formattedDuration = $days . 'd ' . ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                      } elseif ($durationMinutes >= 60) {
                        $hours = floor($durationMinutes / 60);
                        $mins = $durationMinutes % 60;
                        $formattedDuration = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                      } else {
                        $formattedDuration = $durationMinutes . ' min';
                      }
                    ?>
                    <div class="text-xs text-gray-500">Duration: <?php echo e($formattedDuration); ?></div>
                  <?php endif; ?>
                </div>
              <?php elseif($tippingStarted): ?>
                <div class="text-center p-3 bg-yellow-50 rounded-lg border">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-yellow-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-yellow-800">Processing</div>
                  <div class="text-xs text-gray-600"><?php echo e($movement->unloading_started_at->format('d M Y, H:i')); ?></div>
                  <div class="text-xs text-gray-500"><?php echo e($movement->unloading_started_at->diffForHumans()); ?></div>
                </div>
              <?php elseif($factoryBooking->processing_started_at): ?>
                <div class="text-center p-3 bg-yellow-50 rounded-lg border">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-yellow-600 text-sm">⚡</span>
                  </div>
                  <div class="text-sm font-medium text-yellow-800">Processing Started</div>
                  <div class="text-xs text-gray-600"><?php echo e($factoryBooking->processing_started_at->format('d M Y, H:i')); ?></div>
                </div>
              <?php else: ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Processing</div>
                  <?php if($factoryBooking->arrived_at): ?>
                    <div class="text-xs text-gray-400">On site <?php echo e($factoryBooking->arrived_at->diffForHumans()); ?></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              
              <?php if($factoryBooking->departed_at): ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-600 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-800">Site Departure</div>
                  <div class="text-xs text-gray-600"><?php echo e($factoryBooking->departed_at->format('d M Y, H:i')); ?></div>
                  <?php if($factoryBooking->arrived_at): ?>
                    <?php
                      $totalMinutes = round($factoryBooking->arrived_at->diffInMinutes($factoryBooking->departed_at));
                      if ($totalMinutes >= 10080) {
                        $weeks = floor($totalMinutes / 10080);
                        $days = floor(($totalMinutes % 10080) / 1440);
                        $totalTimeFormatted = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                      } elseif ($totalMinutes >= 1440) {
                        $days = floor($totalMinutes / 1440);
                        $hours = floor(($totalMinutes % 1440) / 60);
                        $mins = $totalMinutes % 60;
                        $totalTimeFormatted = $days . 'd ' . ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                      } elseif ($totalMinutes >= 60) {
                        $hours = floor($totalMinutes / 60);
                        $mins = $totalMinutes % 60;
                        $totalTimeFormatted = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                      } else {
                        $totalTimeFormatted = $totalMinutes . ' min';
                      }
                    ?>
                    <div class="text-xs text-gray-500">Total time: <?php echo e($totalTimeFormatted); ?></div>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Departure</div>
                  <?php if($factoryBooking->arrived_at): ?>
                    <div class="text-xs text-gray-400">On site <?php echo e($factoryBooking->arrived_at->diffForHumans()); ?></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        
        <div class="bg-white rounded-lg border">
          <div class="p-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">📋 Event Timeline</h4>
            <p class="text-sm text-gray-600">All activities in chronological order</p>
          </div>
          <div class="p-4">
            <div class="space-y-3">
              <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start space-x-3">
                  
                  <div class="flex-shrink-0 mt-1">
                    <?php
                      $color = match($event->action) {
                        'created' => 'orange',
                        'arrival' => 'blue',
                        'modified' => 'indigo',
                        'completed' => 'purple',
                        default => 'gray'
                      };
                      $icon = match($event->action) {
                        'created' => '🏭',
                        'arrival' => '🚪',
                        'modified' => '🔄',
                        'completed' => '🏁',
                        default => '•'
                      };
                    ?>
                    <div class="w-8 h-8 bg-<?php echo e($color); ?>-100 rounded-full flex items-center justify-center">
                      <span class="text-<?php echo e($color); ?>-600 text-sm"><?php echo e($icon); ?></span>
                    </div>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <h5 class="text-sm font-medium text-gray-900 capitalize"><?php echo e($event->action); ?></h5>
                      <span class="text-xs text-gray-500"><?php echo e($event->created_at->format('d M Y, H:i')); ?></span>
                    </div>
                    <?php if($event->reason): ?>
                      <p class="text-xs text-gray-600 mt-1"><?php echo e($event->reason); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php if($history->isEmpty()): ?>
                <div class="text-center py-8 text-gray-500">
                  <p class="text-sm">No timeline events recorded yet.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-orange-600"><?php echo e($history->where('action', 'created')->count()); ?></div>
        <div class="text-sm text-gray-600">Created</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-blue-600"><?php echo e($history->where('action', 'modified')->count()); ?></div>
        <div class="text-sm text-gray-600">Movements</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-purple-600"><?php echo e($history->where('action', 'completed')->count()); ?></div>
        <div class="text-sm text-gray-600">Completed</div>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-bookings/history.blade.php ENDPATH**/ ?>