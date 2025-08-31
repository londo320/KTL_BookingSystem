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
      <h2 class="font-semibold text-xl">Booking History #<?php echo e($booking->id); ?></h2>
      <a href="<?php echo e(route('app.bookings.show', $booking)); ?>"
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
              <a href="<?php echo e(route('app.bookings.history', $booking)); ?>?sort=asc" 
                 class="px-3 py-1 text-xs rounded-md <?php echo e($sortOrder === 'asc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800'); ?>">
                📈 Oldest First
              </a>
              <a href="<?php echo e(route('app.bookings.history', $booking)); ?>?sort=desc" 
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
        
        <div class="mb-6">
          <div class="bg-white rounded-lg p-4 border">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">🚛 Movement Timeline</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              
              <div class="text-center p-3 bg-green-50 rounded-lg border">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                  <span class="text-green-600 text-sm">📅</span>
                </div>
                <div class="text-sm font-medium text-green-800">Booking Created</div>
                <div class="text-xs text-gray-600"><?php echo e($booking->created_at->format('d M Y, H:i')); ?></div>
                <div class="text-xs text-gray-500"><?php echo e($booking->slot->depot->name); ?></div>
              </div>
              
              <?php if($booking->arrived_at): ?>
                <div class="text-center p-3 bg-blue-50 rounded-lg border">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-blue-800">Site Arrival</div>
                  <div class="text-xs text-gray-600"><?php echo e($booking->arrived_at->format('d M Y, H:i')); ?></div>
                  <?php if($booking->vehicle_registration): ?>
                    <div class="text-xs text-gray-500"><?php echo e($booking->vehicle_registration); ?></div>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Arrival</div>
                  <div class="text-xs text-gray-400">Expected: <?php echo e($booking->slot->start_at->format('d M Y, H:i')); ?></div>
                </div>
              <?php endif; ?>
              
              <?php if($booking->tipping_completed_at): ?>
                <div class="text-center p-3 bg-purple-50 rounded-lg border">
                  <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-purple-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-purple-800">Tipping Complete</div>
                  <div class="text-xs text-gray-600"><?php echo e($booking->tipping_completed_at->format('d M Y, H:i')); ?></div>
                  <?php if($booking->tipping_started_at): ?>
                    <?php
                      $durationMinutes = round($booking->tipping_started_at->diffInMinutes($booking->tipping_completed_at));
                      if ($durationMinutes >= 10080) { // 7+ days (1 week)
                        $weeks = floor($durationMinutes / 10080);
                        $days = floor(($durationMinutes % 10080) / 1440);
                        $formattedDuration = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                      } elseif ($durationMinutes >= 1440) { // 24+ hours
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
                  <?php if($booking->bay_number): ?>
                    <div class="text-xs text-green-600">Bay <?php echo e($booking->bay_number); ?></div>
                  <?php endif; ?>
                </div>
              <?php elseif($booking->tipping_started_at): ?>
                <div class="text-center p-3 bg-yellow-50 rounded-lg border">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-yellow-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-yellow-800">Tipping In Progress</div>
                  <div class="text-xs text-gray-600"><?php echo e($booking->tipping_started_at->format('d M Y, H:i')); ?></div>
                  <div class="text-xs text-gray-500"><?php echo e($booking->tipping_started_at->diffForHumans()); ?></div>
                  <?php if($booking->bay_number): ?>
                    <div class="text-xs text-green-600">Bay <?php echo e($booking->bay_number); ?></div>
                  <?php endif; ?>
                </div>
              <?php elseif($booking->moved_to_bay_at): ?>
                <div class="text-center p-3 bg-blue-50 rounded-lg border">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-blue-800">Moved to Bay</div>
                  <div class="text-xs text-gray-600"><?php echo e($booking->moved_to_bay_at->format('d M Y, H:i')); ?></div>
                  <?php if($booking->bay_number): ?>
                    <div class="text-xs text-green-600">Bay <?php echo e($booking->bay_number); ?></div>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Tipping</div>
                  <?php if($booking->arrived_at): ?>
                    <div class="text-xs text-gray-400">On site <?php echo e($booking->arrived_at->diffForHumans()); ?></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
              
              <?php if($booking->departed_at): ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-600 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-800">Site Departure</div>
                  <div class="text-xs text-gray-600"><?php echo e($booking->departed_at->format('d M Y, H:i')); ?></div>
                  <?php if($booking->arrived_at): ?>
                    <?php
                      $totalMinutes = round($booking->arrived_at->diffInMinutes($booking->departed_at));
                    ?>
                    <div class="text-xs text-gray-500">Total time: <?php echo e($totalMinutes); ?> min</div>
                  <?php endif; ?>
                </div>
              <?php elseif($booking->isCancelled()): ?>
                <div class="text-center p-3 bg-red-50 rounded-lg border">
                  <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-red-600 text-sm">❌</span>
                  </div>
                  <div class="text-sm font-medium text-red-800">Cancelled</div>
                  <div class="text-xs text-gray-600"><?php echo e($booking->cancelled_at->format('d M Y, H:i')); ?></div>
                </div>
              <?php else: ?>
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Departure</div>
                  <?php if($booking->arrived_at): ?>
                    <div class="text-xs text-gray-400">On site <?php echo e($booking->arrived_at->diffForHumans()); ?></div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg border">
          <div class="p-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">📋 Event Timeline</h4>
            <p class="text-sm text-gray-600">Key milestones and movements in chronological order</p>
          </div>
          <div class="p-4">
            <?php
              // Create simplified timeline with only essential events
              $timeline = collect();
              // Add booking creation
              $timeline->push((object)[
                'timestamp' => $booking->created_at,
                'type' => 'created',
                'title' => 'Booking Created',
                'description' => 'Scheduled for ' . $booking->slot->start_at->format('d M Y, H:i') . ' at ' . $booking->slot->depot->name,
                'icon' => '📅',
                'color' => 'green'
              ]);
              // Add arrival if available
              if($booking->arrived_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->arrived_at,
                  'type' => 'arrival',
                  'title' => 'Site Arrival',
                  'description' => ($booking->vehicle_registration ? $booking->vehicle_registration . ' ' : '') . 'arrived on site',
                  'icon' => '🚪',
                  'color' => 'blue'
                ]);
              }
              // Add trailer drop if available
              if($booking->trailer_dropped_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->trailer_dropped_at,
                  'type' => 'dropped',
                  'title' => 'Trailer Dropped',
                  'description' => 'Trailer unhitched and positioned',
                  'icon' => '🔻',
                  'color' => 'purple'
                ]);
              }
              // Add bay movement if available
              if($booking->moved_to_bay_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->moved_to_bay_at,
                  'type' => 'moved_to_bay',
                  'title' => 'Moved to Bay',
                  'description' => 'Positioned at Bay ' . ($booking->bay_number ?: 'TBD'),
                  'icon' => '➡️',
                  'color' => 'indigo'
                ]);
              }
              // Add tipping start if available
              if($booking->tipping_started_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->tipping_started_at,
                  'type' => 'tipping_start',
                  'title' => 'Tipping Started',
                  'description' => 'Unloading commenced' . ($booking->bay_number ? ' in Bay ' . $booking->bay_number : ''),
                  'icon' => '🏗️',
                  'color' => 'yellow'
                ]);
              }
              // Add tipping completion if available
              if($booking->tipping_completed_at) {
                $duration = $booking->tipping_started_at ? round($booking->tipping_started_at->diffInMinutes($booking->tipping_completed_at)) : null;
                if ($duration && $duration >= 10080) { // 7+ days (1 week)
                  $weeks = floor($duration / 10080);
                  $days = floor(($duration % 10080) / 1440);
                  $formattedDuration = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                } elseif ($duration && $duration >= 1440) {
                  $days = floor($duration / 1440);
                  $hours = floor(($duration % 1440) / 60);
                  $mins = $duration % 60;
                  $formattedDuration = $days . 'd ' . ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                } elseif ($duration && $duration >= 60) {
                  $hours = floor($duration / 60);
                  $mins = $duration % 60;
                  $formattedDuration = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                } else {
                  $formattedDuration = $duration ? $duration . ' min' : null;
                }
                $timeline->push((object)[
                  'timestamp' => $booking->tipping_completed_at,
                  'type' => 'tipping_complete',
                  'title' => 'Tipping Complete',
                  'description' => 'Unloading finished' . ($formattedDuration ? ' (' . $formattedDuration . ')' : ''),
                  'icon' => '✅',
                  'color' => 'green'
                ]);
              }
              // Add departure if available
              if($booking->departed_at) {
                $totalTime = $booking->arrived_at ? $booking->arrived_at->diffInMinutes($booking->departed_at) : null;
                $timeline->push((object)[
                  'timestamp' => $booking->departed_at,
                  'type' => 'departure',
                  'title' => 'Site Departure',
                  'description' => 'Left site' . ($totalTime ? ' (total time: ' . $totalTime . ' minutes)' : ''),
                  'icon' => '🚛',
                  'color' => 'gray'
                ]);
              }
              // Add cancellation if applicable
              if($booking->cancelled_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->cancelled_at,
                  'type' => 'cancelled',
                  'title' => 'Booking Cancelled',
                  'description' => $booking->cancellation_reason ?: 'Booking was cancelled',
                  'icon' => '❌',
                  'color' => 'red'
                ]);
              }
              // Add any rebooks from history (only major ones)
              foreach($history->where('action', 'rebooked') as $rebook) {
                if($rebook->originalSlot && $rebook->newSlot) {
                  $timeline->push((object)[
                    'timestamp' => $rebook->created_at,
                    'type' => 'rebook',
                    'title' => 'Rebooked',
                    'description' => 'Moved from ' . $rebook->originalSlot->start_at->format('d M, H:i') . ' to ' . $rebook->newSlot->start_at->format('d M, H:i'),
                    'icon' => '🔄',
                    'color' => 'orange'
                  ]);
                }
              }
              // Sort timeline chronologically
              $timeline = $timeline->sortBy('timestamp');
            ?>
            <div class="space-y-3">
              <?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-start space-x-3">
                  
                  <div class="flex-shrink-0 mt-1">
                    <div class="w-8 h-8 bg-<?php echo e($event->color); ?>-100 rounded-full flex items-center justify-center">
                      <span class="text-<?php echo e($event->color); ?>-600 text-sm"><?php echo e($event->icon); ?></span>
                    </div>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <h5 class="text-sm font-medium text-gray-900"><?php echo e($event->title); ?></h5>
                      <span class="text-xs text-gray-500"><?php echo e($event->timestamp->format('d M Y, H:i')); ?></span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1"><?php echo e($event->description); ?></p>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php if($timeline->isEmpty()): ?>
                <div class="text-center py-8 text-gray-500">
                  <p class="text-sm">No timeline events recorded yet.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
          
          <?php if($history->count() > 0): ?>
            <div class="border-t border-gray-200 p-4">
              <button 
                id="toggleFullHistory" 
                class="flex items-center justify-between w-full text-left hover:bg-gray-50 p-2 rounded"
                onclick="toggleFullHistory()"
              >
                <div>
                  <h5 class="text-sm font-medium text-gray-700">📄 Full Administrative History</h5>
                  <p class="text-xs text-gray-500">All <?php echo e($history->count()); ?> booking changes and system entries</p>
                </div>
                <svg id="fullHistoryIcon" class="w-4 h-4 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              <div id="fullHistoryContent" class="hidden mt-3 pt-3 border-t border-gray-100">
                <div class="space-y-2 max-h-96 overflow-y-auto">
                  <?php $__currentLoopData = $history->sortBy('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start space-x-2 text-xs bg-gray-50 rounded p-2">
                      <span class="text-gray-400 mt-0.5">•</span>
                      <div class="flex-1">
                        <div class="flex items-center justify-between">
                          <span class="font-medium text-gray-700 capitalize"><?php echo e($record->action); ?></span>
                          <span class="text-gray-400"><?php echo e($record->created_at->format('d M H:i')); ?></span>
                        </div>
                        <?php if($record->reason): ?>
                          <p class="text-gray-600 mt-0.5"><?php echo e(Str::limit($record->reason, 60)); ?></p>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <script>
          function toggleFullHistory() {
            const content = document.getElementById('fullHistoryContent');
            const icon = document.getElementById('fullHistoryIcon');
            if (content.classList.contains('hidden')) {
              content.classList.remove('hidden');
              icon.classList.add('rotate-180');
            } else {
              content.classList.add('hidden');
              icon.classList.remove('rotate-180');
            }
          }
        </script>
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
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/history.blade.php ENDPATH**/ ?>