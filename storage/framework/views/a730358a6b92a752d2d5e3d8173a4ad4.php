<?php
  // Define route prefix once for the entire partial
  $workflowRoutePrefix = 'app.';
?>

<?php $__currentLoopData = $bookings->groupBy(fn($b) => $b->slot->depot->name); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  
  <tr class="bg-gray-100 border-l-4 border-blue-500">
    <td colspan="5" class="px-4 py-2 font-semibold text-gray-800">
      🏭 <?php echo e($depotName); ?> 
      <span class="text-sm font-normal text-gray-600">
        (<?php echo e($group->where('arrived_at', '!=', null)->where('departed_at', null)->count()); ?> on site, 
        <?php echo e($group->where('arrived_at', null)->count()); ?> expected)
      </span>
    </td>
  </tr>

  <?php $__currentLoopData = $group->sortBy(fn($b) => $b->slot->start_at); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      // Determine row status for CSS classes and quick identification
      $rowStatus = 'awaiting';
      $statusIcon = '⏳';
      $statusColor = 'gray';
      $rowClass = '';
      
      if ($booking->cancelled_at) {
        $rowStatus = 'cancelled';
        $statusIcon = '❌';
        $statusColor = 'red';
        $rowClass = 'bg-red-50 border-l-4 border-red-500';
      } elseif ($booking->departed_at) {
        $rowStatus = 'completed';
        $statusIcon = '✅';
        $statusColor = 'green';
        $rowClass = 'bg-green-50 border-l-4 border-green-500 completed';
      } elseif ($booking->arrived_at) {
        $rowStatus = 'on-site';
        $statusIcon = '🚛';
        $statusColor = 'blue';
        $rowClass = 'bg-blue-50 border-l-4 border-blue-500 on-site';
      } else {
        $rowClass = 'hover:bg-gray-50 awaiting';
      }
      
      // Get current movement/location for display
      $movement = $booking->movements->first();
      $currentLocation = 'Not assigned';
      if ($movement) {
        if ($movement->tippingBay) {
          $currentLocation = "Bay: {$movement->tippingBay->name}";
        } elseif ($movement->tippingLocation) {
          $currentLocation = $movement->tippingLocation->name;
        } elseif ($movement->current_status === 'arrived') {
          $currentLocation = 'Arrived - needs assignment';
        }
      }
    ?>
    
    <tr class="border-t transition-colors <?php echo e($rowClass); ?>" data-booking-id="<?php echo e($booking->id); ?>">
      
      <td class="px-4 py-3">
        <div class="flex items-center gap-3">
          <div class="text-lg"><?php echo e($statusIcon); ?></div>
          <div>
            <div class="font-mono text-sm font-bold text-<?php echo e($statusColor); ?>-600">
              <?php echo e($booking->booking_reference); ?>

            </div>
            <div class="text-xs text-gray-500">
              <?php echo e($booking->bookingType->name ?? 'Standard'); ?>

            </div>
            <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
              <div class="text-xs text-blue-600">
                📦 <?php echo e($booking->poNumbers->count()); ?> PO(s)
              </div>
            <?php endif; ?>
          </div>
        </div>
      </td>

      
      <td class="px-4 py-3">
        <div class="text-sm font-medium text-gray-900">
          <?php echo e($booking->customer->name ?? 'Walk-in'); ?>

        </div>
        <div class="text-xs text-gray-600">
          📅 <?php echo e($booking->slot->start_at->format('H:i')); ?> - <?php echo e($booking->slot->end_at->format('H:i')); ?>

        </div>
        <?php if($booking->arrived_at): ?>
          <div class="text-xs text-green-600">
            ✅ Arrived: <?php echo e($booking->arrived_at->format('H:i')); ?>

          </div>
        <?php elseif($booking->slot->start_at->isPast() && !$booking->arrived_at): ?>
          <?php
            $lateMinutes = $booking->slot->start_at->diffInMinutes(now());
          ?>
          <div class="text-xs text-red-600 font-medium">
            🔴 Late: <?php echo e(floor($lateMinutes / 60)); ?>h <?php echo e($lateMinutes % 60); ?>m
          </div>
        <?php endif; ?>
        <?php if($booking->departed_at): ?>
          <div class="text-xs text-gray-600">
            🏁 Left: <?php echo e($booking->departed_at->format('H:i')); ?>

          </div>
        <?php endif; ?>
      </td>

      
      <td class="px-4 py-3">
        <?php if($booking->vehicle_registration): ?>
          <div class="text-sm font-medium">🚛 <?php echo e($booking->vehicle_registration); ?></div>
        <?php else: ?>
          <div class="text-sm text-gray-400">No vehicle registered</div>
        <?php endif; ?>
        
        <?php if($booking->container_number): ?>
          <div class="text-xs text-gray-600">📦 <?php echo e($booking->container_number); ?></div>
        <?php endif; ?>
        
        <?php if($booking->carrier_company): ?>
          <div class="text-xs text-gray-600">🏢 <?php echo e($booking->carrier_company); ?></div>
        <?php endif; ?>
        
        
        <?php if($booking->arrived_at && !$booking->departed_at): ?>
          <?php
            $movement = $booking->movements->first();
            $detailedLocation = 'Location unknown';
            $trailerStatus = 'attached';
            $statusBadgeClass = 'bg-gray-100 text-gray-800';
            
            if ($movement) {
              // Determine detailed location and trailer status
              if ($movement->tippingBay) {
                $detailedLocation = "Bay {$movement->tippingBay->name}";
                $statusBadgeClass = 'bg-orange-100 text-orange-800';
                
                if ($movement->current_status === 'unloading') {
                  $trailerStatus = 'tipping';
                } elseif ($movement->current_status === 'empty') {
                  $trailerStatus = 'empty';
                } else {
                  $trailerStatus = 'at bay';
                }
              } elseif ($movement->tippingLocation) {
                $detailedLocation = $movement->tippingLocation->name;
                
                if ($movement->current_status === 'trailer_dropped') {
                  $trailerStatus = 'dropped (empty)';
                  $statusBadgeClass = 'bg-purple-100 text-purple-800';
                } elseif ($movement->current_status === 'in_location') {
                  $trailerStatus = 'parked (loaded)';
                  $statusBadgeClass = 'bg-blue-100 text-blue-800';
                } else {
                  $trailerStatus = 'in parking';
                  $statusBadgeClass = 'bg-yellow-100 text-yellow-800';
                }
              } elseif ($movement->current_status === 'arrived') {
                $detailedLocation = 'Site entrance';
                $trailerStatus = 'needs assignment';
                $statusBadgeClass = 'bg-red-100 text-red-800';
              } elseif ($movement->unit_departed_at && !$movement->collection_unit_departed_at) {
                $detailedLocation = $movement->tippingLocation ? $movement->tippingLocation->name : 'parking area';
                $trailerStatus = 'unit departed';
                $statusBadgeClass = 'bg-orange-100 text-orange-800';
              }
              
              // Check if unit has left but trailer still on site
              if ($movement->unit_departed_at && !$movement->collection_unit_departed_at) {
                $trailerStatus = 'trailer only';
                $statusBadgeClass = 'bg-purple-100 text-purple-800';
              }
            }
            
            // Override with actual booking data if available
            if ($booking->trailer_left_on_site && !$booking->trailer_collected_at) {
              $trailerStatus = $booking->dropped_trailer_status ?? 'dropped';
              $detailedLocation = $booking->dropped_trailer_location ?? $detailedLocation;
              $statusBadgeClass = 'bg-purple-100 text-purple-800';
            }
          ?>
          
          <div class="text-xs mt-1 space-y-1">
            
            <div class="px-2 py-1 rounded bg-blue-50 text-blue-800">
              📍 <?php echo e($detailedLocation); ?>

            </div>
            
            
            <div class="px-2 py-1 rounded <?php echo e($statusBadgeClass); ?>">
              <?php if($trailerStatus === 'tipping'): ?>
                🏗️ Tipping in progress
              <?php elseif($trailerStatus === 'empty'): ?>
                📦 Empty - ready for collection
              <?php elseif($trailerStatus === 'dropped (empty)'): ?>
                🚚 Trailer dropped (empty)
              <?php elseif($trailerStatus === 'parked (loaded)'): ?>
                🚛 Parked - awaiting bay
              <?php elseif($trailerStatus === 'trailer only'): ?>
                🔴 Unit departed - trailer on site
              <?php elseif($trailerStatus === 'needs assignment'): ?>
                ⚠️ Needs location assignment
              <?php elseif($trailerStatus === 'at bay'): ?>
                🏗️ At tipping bay
              <?php else: ?>
                🚚 <?php echo e(ucfirst(str_replace('_', ' ', $trailerStatus))); ?>

              <?php endif; ?>
            </div>
            
            
            <?php if($movement): ?>
              <?php if($movement->moved_to_bay_at && $movement->current_status === 'unloading'): ?>
                <div class="text-xs text-gray-500">
                  ⏱️ Tipping <?php echo e($movement->moved_to_bay_at->diffForHumans(null, true)); ?>

                </div>
              <?php elseif($movement->moved_to_location_at): ?>
                <div class="text-xs text-gray-500">
                  ⏱️ In location <?php echo e($movement->moved_to_location_at->diffForHumans(null, true)); ?>

                </div>
              <?php elseif($movement->unit_departed_at): ?>
                <div class="text-xs text-gray-500">
                  🚗 Unit left <?php echo e($movement->unit_departed_at->diffForHumans(null, true)); ?>

                </div>
              <?php endif; ?>
            <?php endif; ?>
            
            
            <?php if($booking->trailer_collection_scheduled): ?>
              <?php if($booking->trailer_collection_scheduled->isPast()): ?>
                <div class="text-xs text-red-600 font-medium">
                  ⚠️ Collection overdue
                </div>
              <?php else: ?>
                <div class="text-xs text-green-600">
                  📅 Collection due <?php echo e($booking->trailer_collection_scheduled->format('H:i')); ?>

                </div>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </td>

      
      <td class="px-4 py-3">
        <?php
          $expectedCases = $booking->total_expected_cases;
          $actualCases = $booking->total_actual_cases;
          $expectedPallets = $booking->total_expected_pallets;
          $actualPallets = $booking->total_actual_pallets;
        ?>
        
        <div class="text-sm">
          <div>📦 <?php echo e($actualCases > 0 ? number_format($actualCases) : '-'); ?>/<?php echo e(number_format($expectedCases)); ?> cases</div>
          <div>🎯 <?php echo e($actualPallets > 0 ? number_format($actualPallets) : '-'); ?>/<?php echo e(number_format($expectedPallets)); ?> pallets</div>
        </div>
        
        <?php if($actualCases > 0 && $expectedCases > 0): ?>
          <?php
            $variance = $actualCases - $expectedCases;
          ?>
          <?php if($variance != 0): ?>
            <div class="text-xs <?php echo e($variance > 0 ? 'text-blue-600' : 'text-red-600'); ?> font-medium">
              <?php echo e($variance > 0 ? '↗' : '↘'); ?> <?php echo e(abs($variance)); ?>

            </div>
          <?php else: ?>
            <div class="text-xs text-green-600 font-medium">✓ Match</div>
          <?php endif; ?>
        <?php endif; ?>
      </td>

      
      <td class="px-4 py-3">
        <div class="flex flex-col gap-1">
          
          <?php if(!$booking->cancelled_at && !$booking->arrived_at): ?>
            
            <a href="<?php echo e(route($workflowRoutePrefix . 'bookings.arrival.form', $booking)); ?>" 
               class="w-full px-3 py-2 bg-green-600 text-white rounded text-xs font-medium hover:bg-green-700 transition-colors text-center block">
              🚛 Process Arrival
            </a>
          <?php elseif($booking->arrived_at && !$booking->departed_at): ?>
            
            <a href="<?php echo e(route($workflowRoutePrefix . 'tipping-workflow.show', $booking)); ?>"
               class="w-full px-3 py-2 bg-orange-600 text-white rounded text-xs font-medium hover:bg-orange-700 transition-colors text-center">
              🏗️ Manage Workflow
            </a>
            
            <?php
              $movement = $booking->movements->first();
              $unitHasDeparted = $movement && $movement->unit_departed_at;
            ?>
            
            <?php if($unitHasDeparted): ?>
              
              <div class="w-full px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs text-center">
                🚗 Unit Left: <?php echo e($movement->unit_departed_at->format('M j H:i')); ?>

              </div>
            <?php else: ?>
              
              <button onclick="openDepartureModal(<?php echo e($booking->id); ?>, '<?php echo e($booking->booking_reference); ?>', '<?php echo e(addslashes($booking->customer->name ?? 'N/A')); ?>', '<?php echo e($booking->vehicle_registration ?? ''); ?>', 'CURRENT', 'Current Location')" 
                      class="w-full px-2 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors">
                🏁 Mark Departed
              </button>
            <?php endif; ?>
          <?php elseif($booking->departed_at): ?>
            
            <a href="<?php echo e(route($workflowRoutePrefix . 'bookings.show', $booking)); ?>"
               class="w-full px-3 py-2 bg-gray-600 text-white rounded text-xs font-medium hover:bg-gray-700 transition-colors text-center">
              👁️ View Details
            </a>
          <?php else: ?>
            
            <a href="<?php echo e(route($workflowRoutePrefix . 'bookings.show', $booking)); ?>"
               class="w-full px-3 py-2 bg-gray-400 text-white rounded text-xs font-medium hover:bg-gray-500 transition-colors text-center">
              👁️ View
            </a>
          <?php endif; ?>

          
          <?php if(!$booking->cancelled_at && !$booking->departed_at): ?>
            <div class="flex gap-1">
              <a href="<?php echo e(route($workflowRoutePrefix . 'bookings.show', $booking)); ?>" 
                 class="flex-1 px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 transition-colors text-center"
                 title="View details">
                👁️
              </a>
              <a href="<?php echo e(route($workflowRoutePrefix . 'bookings.edit', $booking)); ?>" 
                 class="flex-1 px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs hover:bg-gray-200 transition-colors text-center"
                 title="Edit booking">
                ✏️
              </a>
            </div>
          <?php endif; ?>
        </div>
      </td>
    </tr>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($bookings->count() === 0): ?>
  <tr>
    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
      <div class="text-4xl mb-4">📋</div>
      <div class="text-lg font-medium">No bookings found</div>
      <div class="text-sm">Try adjusting your filters or date range</div>
    </td>
  </tr>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/partials/streamlined-rows.blade.php ENDPATH**/ ?>