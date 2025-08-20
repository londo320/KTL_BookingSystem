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
        <h2 class="font-semibold text-xl">Bookings</h2>
        <div class="text-sm mt-1">
          <?php if(!$currentDepotId): ?>
            <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
          <?php else: ?>
            <?php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); ?>
            <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600"><?php echo e($currentDepot?->name ?? 'Unknown Depot'); ?></span></span>
            <?php if($currentDepotId == $defaultDepotId): ?>
              <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
            <?php else: ?>
              <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="flex gap-2">
        <?php
          $routePrefix = request()->route()->getPrefix() === 'depot-admin' ? 'depot.' : 'admin.';
        ?>
        <a href="<?php echo e(route($routePrefix . 'bookings.fix-historical-departures')); ?>"
           class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
          🔧 Fix Historical Data
        </a>
        <a href="<?php echo e(route('admin.customer-behavior.index')); ?>"
           class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
          📊 Customer Analysis
        </a>
        <a href="<?php echo e(route('admin.bookings.create')); ?>"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + New Booking
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto">
    <?php if(session('success')): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    
    <div class="mb-6 bg-white rounded-lg shadow-sm border">
      
      <div class="p-4 border-b border-gray-200">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 items-center">
          
          <div class="xl:col-span-1">
            <form method="GET" action="<?php echo e(route('admin.bookings.index')); ?>" class="flex gap-2">
              
              <?php $__currentLoopData = request()->except(['search', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              
              <input type="text" 
                     name="search" 
                     value="<?php echo e(request('search')); ?>"
                     placeholder="🔍 Search booking ref, customer, vehicle, container..."
                     class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Search
              </button>
              <?php if(request('search')): ?>
                <a href="<?php echo e(route('admin.bookings.index', request()->except(['search', 'page']))); ?>"
                   class="px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                  Clear
                </a>
              <?php endif; ?>
            </form>
          </div>
          
          
          <div class="xl:col-span-1 flex justify-center gap-2">
            <button onclick="openTrailerCollectionModal()" 
                    class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
              🚛 Trailer Collection
            </button>
            <a href="<?php echo e(route('admin.trailer-location-report')); ?>" 
               class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
              📍 Trailers on Site
            </a>
          </div>
          
          
          <div class="xl:col-span-1 flex justify-end gap-1">
            <div class="flex gap-1">
              <a href="<?php echo e(route('admin.bookings.export.pdf', request()->query())); ?>" 
                 class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs" target="_blank" title="Export PDF">
                📄 PDF
              </a>
              <a href="<?php echo e(route('admin.bookings.export.excel', request()->query())); ?>" 
                 class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs" title="Export Excel">
                📊 Excel
              </a>
              <a href="<?php echo e(route('admin.bookings.export.csv', request()->query())); ?>" 
                 class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs" title="Export CSV">
                📝 CSV
              </a>
            </div>
          </div>
        </div>
      </div>
      
      
      <div class="p-4">
        <div class="flex flex-wrap items-center gap-4">
          
          <div class="flex gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Status:</span>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['status', 'page']), ['status' => 'outstanding']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('status', 'outstanding') == 'outstanding' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              ⏳ Outstanding
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['status', 'page']), ['status' => 'completed']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('status') == 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              ✅ Completed
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['status', 'page']), ['status' => 'all']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('status') == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📋 All
            </a>
          </div>
          
          
          <div class="flex flex-wrap gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Date:</span>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'yesterday']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'yesterday' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📅 Yesterday
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'today']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📅 Today
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'tomorrow']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              🗓️ Tomorrow
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'last_week']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'last_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📉 Last Week
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'this_week']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📊 This Week
            </a>
            <a href="<?php echo e(route('admin.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'next_week']))); ?>" 
               class="px-3 py-1 rounded text-sm <?php echo e(request('filter') == 'next_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📈 Next Week
            </a>
          </div>
          
          
          <?php if(request()->hasAny(['filter', 'status', 'search'])): ?>
            <a href="<?php echo e(route('admin.bookings.index')); ?>"
               class="px-3 py-1 rounded text-sm bg-gray-500 text-white hover:bg-gray-600 ml-auto">
              🔄 Reset All
            </a>
          <?php endif; ?>
        </div>
      </div>
    </div>

    
    <div class="mb-4 bg-white rounded-lg shadow-sm border">
      <button type="button" onclick="toggleAdvancedFilters()" class="w-full p-3 text-left flex items-center justify-between hover:bg-gray-50">
        <span class="text-sm font-medium text-gray-700">🔧 Advanced Filters</span>
        <svg id="advanced-filters-icon" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </button>
      
      <div id="advanced-filters-content" class="hidden border-t border-gray-200">
        <form method="GET" class="p-4">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">🏭 Depot</label>
              <select name="depot_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="" <?php echo e(!$currentDepotId ? 'selected' : ''); ?>>All Depots</option>
                <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
                    <?php echo e($depot->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">👥 Customer</label>
              <select name="customer_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($customer->id); ?>" <?php if(request('customer_id') == $customer->id): echo 'selected'; endif; ?>><?php echo e($customer->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📋 Type</label>
              <select name="booking_type_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($type->id); ?>" <?php if(request('booking_type_id') == $type->id): echo 'selected'; endif; ?>><?php echo e($type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📅 Week</label>
              <select name="week_number" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All Weeks</option>
                <?php $__currentLoopData = $weeks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $isCurrentWeek = $week['number'] === \Carbon\Carbon::now()->weekOfYear;
                  ?>
                  <option value="<?php echo e($week['number']); ?>" <?php if(request('week_number') == $week['number']): echo 'selected'; endif; ?>>
                    Week <?php echo e($week['number']); ?><?php echo e($isCurrentWeek ? ' (Current)' : ''); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📅 From Date</label>
              <input type="date" name="from" value="<?php echo e(request('from')); ?>" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📅 To Date</label>
              <input type="date" name="to" value="<?php echo e(request('to')); ?>" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">🚛 Arrival Status</label>
              <select name="arrival" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <option value="not_arrived" <?php if(request('arrival')=='not_arrived'): echo 'selected'; endif; ?>>📋 Not Arrived</option>
                <option value="late_runners" <?php if(request('arrival')=='late_runners'): echo 'selected'; endif; ?>>⏰ Late Runners</option>
                <option value="arrived" <?php if(request('arrival')=='arrived'): echo 'selected'; endif; ?>>✅ Arrived</option>
                <option value="on_time" <?php if(request('arrival')=='on_time'): echo 'selected'; endif; ?>>🎯 On Time</option>
                <option value="arrived_late" <?php if(request('arrival')=='arrived_late'): echo 'selected'; endif; ?>>🔶 Arrived Late</option>
                <option value="onsite" <?php if(request('arrival')=='onsite'): echo 'selected'; endif; ?>>🚛 On Site</option>
                <option value="completed" <?php if(request('arrival')=='completed'): echo 'selected'; endif; ?>>✅ Completed</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📊 Booking Status</label>
              <select name="status" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="" <?php if(!request('status')): echo 'selected'; endif; ?>>🔍 Active Only</option>
                <option value="all" <?php if(request('status') == 'all'): echo 'selected'; endif; ?>>📋 Show All</option>
                <option value="pending" <?php if(request('status') == 'pending'): echo 'selected'; endif; ?>>⏳ Pending</option>
                <option value="confirmed" <?php if(request('status') == 'confirmed'): echo 'selected'; endif; ?>>✅ Confirmed</option>
                <option value="in_progress" <?php if(request('status') == 'in_progress'): echo 'selected'; endif; ?>>🚛 In Progress</option>
                <option value="completed" <?php if(request('status') == 'completed'): echo 'selected'; endif; ?>>✅ Completed</option>
                <option value="cancelled" <?php if(request('status') == 'cancelled'): echo 'selected'; endif; ?>>❌ Cancelled</option>
              </select>
            </div>
          </div>
          
          <div class="mt-4 flex justify-end gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
              🔍 Apply Filters
            </button>
            <a href="<?php echo e(route('admin.bookings.index')); ?>" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm font-medium">
              🔄 Reset All
            </a>
          </div>
        </form>
      </div>
    </div>

    
    <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Booking Reference</th>
          <th class="px-4 py-2 text-left">Start → End</th>
          <th class="px-4 py-2 text-left">Customer / Collection</th>
          <th class="px-4 py-2 text-left">Type / PO Numbers</th>
          <th class="px-4 py-2 text-left">Cases</th>
          <th class="px-4 py-2 text-left">Pallets</th>
          <th class="px-4 py-2 text-left">Arrival</th>
          <th class="px-4 py-2 text-left">Tipping Status</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
  <?php $__currentLoopData = $bookings->groupBy(fn($b) => $b->slot->depot->name); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr><td colspan="9" class="bg-gray-200 font-semibold px-4 py-2">Depot: <?php echo e($depotName); ?></td></tr>
    <?php $__currentLoopData = $group->sortBy(fn($b) => $b->slot->start_at); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="border-t hover:bg-gray-50 
        <?php if($booking->cancelled_at): ?> bg-red-50 border-red-200 
        <?php elseif($booking->status === 'completed'): ?> bg-green-50 border-green-200 
        <?php elseif($booking->arrived_at && !$booking->departed_at): ?> bg-blue-50 border-blue-200 
        <?php elseif($booking->trailer_left_on_site && !$booking->trailer_collected_at): ?> bg-orange-50 border-orange-200 
        <?php endif; ?>">
        
        <td class="px-4 py-2 align-top">
          <div class="flex items-center">
            <?php if($booking->cancelled_at): ?>
              <div class="w-3 h-3 bg-red-500 rounded-full mr-2" title="Cancelled"></div>
            <?php elseif($booking->status === 'completed'): ?>
              <div class="w-3 h-3 bg-green-500 rounded-full mr-2" title="Completed"></div>
            <?php elseif($booking->arrived_at && !$booking->departed_at): ?>
              <div class="w-3 h-3 bg-blue-500 rounded-full mr-2 animate-pulse" title="On Site"></div>
            <?php elseif($booking->trailer_left_on_site && !$booking->trailer_collected_at): ?>
              <div class="w-3 h-3 bg-orange-500 rounded-full mr-2" title="Trailer On Site"></div>
            <?php elseif($booking->status === 'confirmed'): ?>
              <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2" title="Confirmed"></div>
            <?php else: ?>
              <div class="w-3 h-3 bg-gray-400 rounded-full mr-2" title="Pending"></div>
            <?php endif; ?>
            <div class="font-mono text-sm font-semibold text-blue-600"><?php echo e($booking->booking_reference); ?></div>
          </div>
        </td>

        
              <td class="px-4 py-2 align-top">
              <?php
                $slotStart = $booking->slot->start_at;
                $now = now();
                $arrivedAt = $booking->arrived_at;

                // Determine if booking is late
                $isLateNotArrived = $now->greaterThan($slotStart) && !$arrivedAt;
                $isLateArrived = $arrivedAt && $arrivedAt->greaterThan($slotStart);
              ?>

              <?php if($isLateNotArrived): ?>
                <?php if($booking->estimated_arrival): ?>
                  <div class="text-blue-600 text-xs font-semibold">
                    💬 Updated ETA: <?php echo e(\Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                  </div>
                <?php endif; ?>
                <div class="text-red-600 text-xs font-semibold">
                  Original: <?php echo e($slotStart->format('d-M H:i')); ?>

                </div>
                <div id="late-<?php echo e($booking->id); ?>" class="text-red-600 text-xs font-semibold">Late by: calculating…</div>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    const start = new Date("<?php echo e($slotStart->format('Y-m-d H:i:s')); ?>");
                    const el = document.getElementById('late-<?php echo e($booking->id); ?>');
                    function update() {
                      const now = new Date();
                      let diff = Math.floor((now - start) / 60000);
                      const d = Math.floor(diff / 1440); diff %= 1440;
                      const h = Math.floor(diff / 60); const m = diff % 60;
                      el.textContent = `Late by: ${d}d ${h}h ${m}m`;
                    }
                    update(); setInterval(update, 60000);
                  });
                </script>
              <?php elseif($isLateArrived): ?>
                <?php
                  $lateMinutes = $arrivedAt->diffInMinutes($slotStart);
                  $d = intdiv($lateMinutes, 1440);
                  $h = intdiv($lateMinutes % 1440, 60);
                  $m = $lateMinutes % 60;
                ?>
                <?php if($booking->estimated_arrival): ?>
                  <div class="text-blue-600 text-xs font-semibold">
                    💬 Updated ETA: <?php echo e(\Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                  </div>
                <?php endif; ?>
                <div class="text-orange-600 text-xs font-semibold">
                  Original: <?php echo e($slotStart->format('d-M H:i')); ?>

                </div>
                <div class="text-yellow-600 text-xs font-semibold">
                  Arrived Late by: <?php echo e($d); ?>d <?php echo e($h); ?>h <?php echo e($m); ?>m
                </div>
              <?php endif; ?>

              <?php echo e($slotStart->format('d-M H:i')); ?> → <?php echo e($booking->slot->end_at->format('d-M H:i')); ?>

</td>

        
        <td class="px-4 py-2 align-top">
          <div class="text-sm font-medium text-gray-900"><?php echo e($booking->customer->name ?? '-'); ?></div>
          <?php if($booking->reference): ?>
            <div class="text-xs text-gray-600">Collection: <?php echo e($booking->reference); ?></div>
          <?php endif; ?>
        </td>

        
        <td class="px-4 py-2 align-top">
          <div class="text-sm font-medium"><?php echo e(optional($booking->bookingType)->name ?? '-'); ?></div>
          <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
            <div class="text-xs text-gray-600 mt-1">
              <div class="font-medium text-blue-600"><?php echo e($booking->poNumbers->count()); ?> PO<?php echo e($booking->poNumbers->count() > 1 ? 's' : ''); ?>:</div>
              <div class="space-y-1">
                <?php $__currentLoopData = $booking->poNumbers->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="flex items-center space-x-1">
                    <span class="font-mono"><?php echo e($po->po_number); ?></span>
                    <?php if($po->hasVariance()): ?>
                      <span class="w-2 h-2 bg-red-500 rounded-full" title="Has variance"></span>
                    <?php elseif($po->isComplete()): ?>
                      <span class="w-2 h-2 bg-green-500 rounded-full" title="Complete"></span>
                    <?php endif; ?>
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php if($booking->poNumbers->count() > 3): ?>
                  <div class="text-gray-500">+<?php echo e($booking->poNumbers->count() - 3); ?> more</div>
                <?php endif; ?>
              </div>
            </div>
          <?php else: ?>
            <div class="text-xs text-gray-400">No PO numbers</div>
          <?php endif; ?>
        </td>

        
        <td class="px-4 py-2 align-top">
          <?php
            // Use PO number totals (new structure)
            $actualCases = $booking->total_actual_cases;
            $expectedCases = $booking->total_expected_cases;
            $caseVariance = $booking->total_case_variance;
          ?>
          
          <div class="text-sm">
            <?php echo e($actualCases > 0 ? number_format($actualCases) : '-'); ?> / <?php echo e($expectedCases > 0 ? number_format($expectedCases) : '-'); ?>

          </div>
          <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
            <div class="text-xs text-gray-500">From <?php echo e($booking->poNumbers->count()); ?> PO<?php echo e($booking->poNumbers->count() > 1 ? 's' : ''); ?></div>
          <?php endif; ?>
          
          <?php if($actualCases > 0 && $expectedCases > 0): ?>
            <?php
              if ($caseVariance < 0) {
                $icon = '↓';
                $color = 'text-red-600';
                $text = 'Under by ' . abs($caseVariance);
              } elseif ($caseVariance > 0) {
                $icon = '↑';
                $color = 'text-blue-600';
                $text = 'Over by ' . $caseVariance;
              } else {
                $icon = '=';
                $color = 'text-green-600';
                $text = 'Matched';
              }
            ?>
            <div class="text-xs <?php echo e($color); ?> font-medium">
              <span class="text-base"><?php echo e($icon); ?></span> <?php echo e($text); ?>

            </div>
          <?php endif; ?>
        </td>

        
        <td class="px-4 py-2 align-top">
          <?php
            // Use PO number totals (new structure)
            $actualPallets = $booking->total_actual_pallets;
            $expectedPallets = $booking->total_expected_pallets;
            $palletVariance = $booking->total_pallet_variance;
          ?>
          
          <div class="text-sm">
            <?php echo e($actualPallets > 0 ? number_format($actualPallets) : '-'); ?> / <?php echo e($expectedPallets > 0 ? number_format($expectedPallets) : '-'); ?>

          </div>
          <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
            <div class="text-xs text-gray-500">From <?php echo e($booking->poNumbers->count()); ?> PO<?php echo e($booking->poNumbers->count() > 1 ? 's' : ''); ?></div>
          <?php endif; ?>
          
          <?php if($actualPallets > 0 && $expectedPallets > 0): ?>
            <?php
              if ($palletVariance < 0) {
                $icon = '↓';
                $color = 'text-red-600';
                $text = 'Under by ' . abs($palletVariance);
              } elseif ($palletVariance > 0) {
                $icon = '↑';
                $color = 'text-blue-600';
                $text = 'Over by ' . $palletVariance;
              } else {
                $icon = '=';
                $color = 'text-green-600';
                $text = 'Matched';
              }
            ?>
            <div class="text-xs <?php echo e($color); ?> font-medium">
              <span class="text-base"><?php echo e($icon); ?></span> <?php echo e($text); ?>

            </div>
          <?php endif; ?>
        </td>

        
        <td class="px-4 py-2 align-top space-y-1">
          <?php if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'))): ?>
            <div class="inline-block px-2 py-1 bg-black text-white rounded text-xs font-semibold">
              ❌ Cancelled
            </div>
            <div class="text-xs text-gray-500">
              <?php echo e($booking->cancelled_at->format('d-M H:i')); ?>

            </div>
            <?php if($booking->cancellation_reason): ?>
              <div class="text-xs text-gray-600" title="<?php echo e($booking->cancellation_reason); ?>">
                <?php echo e(Str::limit($booking->cancellation_reason, 25)); ?>

              </div>
            <?php endif; ?>
          <?php elseif(!$booking->arrived_at): ?>
            <?php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; ?>
            <?php if($canTakeAction): ?>
              <a href="<?php echo e(route($routePrefix . 'bookings.arrival.form', $booking)); ?>" 
                 class="inline-block bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 cursor-pointer">
                🚛 Process Arrival
              </a>
            <?php else: ?>
              <span class="inline-block bg-gray-300 text-gray-500 px-3 py-1 rounded text-xs cursor-not-allowed"
                    title="Actions only available for your default depot">
                🚛 Process Arrival
              </span>
            <?php endif; ?>
          <?php else: ?>
            <div>✅ Arrived: <?php echo e($booking->arrived_at->format('d-M H:i')); ?></div>
            <?php if($booking->vehicle_registration): ?>
              <div class="text-xs text-gray-600">🚛 <?php echo e($booking->vehicle_registration); ?></div>
            <?php endif; ?>
            <?php if($booking->container_number): ?>
              <div class="text-xs text-gray-600">📦 <?php echo e($booking->container_number); ?></div>
            <?php endif; ?>
            
            
            <?php if($booking->current_location === 'waiting_area' && $booking->waiting_area_location): ?>
              <div class="text-xs text-blue-600 mt-1">🅿️ Waiting: <?php echo e($booking->waiting_area_location); ?></div>
              <?php if(!$booking->tipping_bay_id): ?>
                <?php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; ?>
                <?php if($canTakeAction): ?>
                  <button onclick="openBayAssignmentModal(<?php echo e($booking->id); ?>, '<?php echo e($booking->booking_reference); ?>', '<?php echo e($booking->waiting_area_location); ?>')" 
                          class="inline-block bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700 cursor-pointer mt-1">
                    🏗️ Assign Bay
                  </button>
                <?php else: ?>
                  <span class="inline-block bg-gray-300 text-gray-500 px-2 py-1 rounded text-xs cursor-not-allowed mt-1"
                        title="Actions only available for your default depot">
                    🏗️ Assign Bay
                  </span>
                <?php endif; ?>
              <?php endif; ?>
            <?php elseif($booking->current_location === 'tipping_bay' && $booking->tippingBay): ?>
              <div class="text-xs text-green-600 mt-1">🏗️ Bay: <?php echo e($booking->tippingBay->name); ?></div>
            <?php endif; ?>
            
            
            <?php if($booking->trailer_left_on_site && !$booking->trailer_collected_at): ?>
              <div class="text-xs mt-1">
                <span class="px-1 py-0.5 rounded text-xs
                  <?php if($booking->dropped_trailer_status === 'empty_available'): ?> bg-green-100 text-green-700
                  <?php elseif($booking->dropped_trailer_status === 'awaiting_collection'): ?> bg-orange-100 text-orange-700
                  <?php elseif($booking->dropped_trailer_status === 'being_tipped'): ?> bg-blue-100 text-blue-700
                  <?php else: ?> bg-gray-100 text-gray-700 <?php endif; ?>">
                  📦 <?php echo e(ucwords(str_replace('_', ' ', $booking->dropped_trailer_status ?? 'On Site'))); ?>

                </span>
                <?php if($booking->dropped_trailer_location): ?>
                  <br><span class="text-xs text-gray-500">📍 <?php echo e($booking->dropped_trailer_location); ?></span>
                <?php endif; ?>
                <?php if($booking->trailer_collection_scheduled && $booking->trailer_collection_scheduled->isPast()): ?>
                  <br><span class="text-xs text-red-600">⚠️ Collection Overdue</span>
                <?php elseif($booking->trailer_collection_scheduled): ?>
                  <br><span class="text-xs text-gray-500">📅 Due: <?php echo e($booking->trailer_collection_scheduled->format('d-M H:i')); ?></span>
                <?php endif; ?>
              </div>
            <?php elseif($booking->trailer_collected_at): ?>
              <div class="text-xs text-green-600 mt-1">✅ Trailer Collected</div>
            <?php endif; ?>
          <?php endif; ?>

          <?php if(!$booking->cancelled_at && $booking->arrived_at && !$booking->departed_at): ?>
            <?php 
              $canTakeAction = $booking->slot->depot_id == $defaultDepotId;
              $movement = $booking->movements->first();
              $unitHasDeparted = $movement && $movement->unit_departed_at;
            ?>
            
            <?php if($unitHasDeparted): ?>
              
              <div class="text-xs text-orange-600 font-medium">
                🚗 Unit Left: <?php echo e($movement->unit_departed_at->format('d-M H:i')); ?>

              </div>
            <?php elseif($canTakeAction): ?>
              <button onclick="openDepartureModal(<?php echo e($booking->id); ?>, '<?php echo e($booking->booking_reference); ?>', '<?php echo e(addslashes($booking->customer->name ?? 'N/A')); ?>', '<?php echo e($booking->vehicle_registration ?? ''); ?>', 
                <?php
                  $currentLocation = null;
                  $currentLocationName = 'Unknown';
                  $tippingStatus = $movement ? $movement->current_status : null;
                  
                  // Check movement status and determine location based on trailer status (full/empty)
                  if ($movement) {
                    $isEmptyTrailer = in_array($movement->current_status, ['empty', 'awaiting_collection']);
                    
                    if ($isEmptyTrailer) {
                      // EMPTY TRAILER OPTIONS:
                      
                      // 1. Collection Zone (awaiting pickup)
                      if ($movement->current_status === 'awaiting_collection' && $movement->tippingLocation) {
                        $currentLocation = 'COLLECTION_' . $movement->tippingLocation->id;
                        $currentLocationName = '📦 ' . $movement->tippingLocation->name . ' (Collection Zone)';
                      }
                      // 2. Still in Tipping Bay (needs to be moved)
                      elseif ($movement->tippingBay) {
                        $currentLocation = 'BAY_' . $movement->tippingBay->id;
                        $currentLocationName = '🚛 ' . $movement->tippingBay->name . ' (Empty - Ready to Move)';
                      }
                      // 3. Waiting Area
                      elseif ($movement->custom_fields && isset($movement->custom_fields['waiting_area_location'])) {
                        $waitingArea = $movement->custom_fields['waiting_area_location'];
                        $currentLocation = 'WAITING_' . $waitingArea;
                        $currentLocationName = '⏳ Waiting Area ' . $waitingArea . ' (Empty)';
                      }
                      // 4. General drop location
                      elseif ($movement->tippingLocation) {
                        $currentLocation = 'DROP_' . $movement->tippingLocation->id;
                        $currentLocationName = '📍 ' . $movement->tippingLocation->name . ' (Empty)';
                      }
                      else {
                        $currentLocation = 'ONSITE_EMPTY';
                        $currentLocationName = '✅ Empty Trailer On Site';
                      }
                    } else {
                      // FULL TRAILER OPTIONS:
                      
                      // 1. In Tipping Bay (being tipped)
                      if ($movement->tippingBay) {
                        $currentLocation = 'BAY_' . $movement->tippingBay->id;
                        $currentLocationName = '🏗️ ' . $movement->tippingBay->name . ' (Tipping)';
                      }
                      // 2. In Drop Zone (awaiting tipping)
                      elseif ($movement->tippingLocation) {
                        $currentLocation = 'DROP_' . $movement->tippingLocation->id;
                        $currentLocationName = '📍 ' . $movement->tippingLocation->name . ' (Full - Awaiting)';
                      }
                      // 3. Waiting Area
                      elseif ($movement->custom_fields && isset($movement->custom_fields['waiting_area_location'])) {
                        $waitingArea = $movement->custom_fields['waiting_area_location'];
                        $currentLocation = 'WAITING_' . $waitingArea;
                        $currentLocationName = '⏳ Waiting Area ' . $waitingArea . ' (Full)';
                      }
                      else {
                        $currentLocation = 'ONSITE_FULL';
                        $currentLocationName = '📦 Full Trailer On Site';
                      }
                    }
                  }
                ?>
                '<?php echo e($currentLocation); ?>', '<?php echo e(addslashes($currentLocationName)); ?>', '<?php echo e($tippingStatus); ?>')" 
                      class="text-green-600 text-xs underline hover:text-green-800">
                🏁 Mark Departed
              </button>
            <?php else: ?>
              <span class="text-gray-400 text-xs cursor-not-allowed" 
                    title="Actions only available for your default depot">
                🏁 Mark Departed
              </span>
            <?php endif; ?>
          <?php elseif(!$booking->cancelled_at && $booking->departed_at): ?>
            <div>🕒 Departed: <?php echo e($booking->departed_at->format('d-M H:i')); ?></div>
            <?php
              $arr = \Carbon\Carbon::parse($booking->arrived_at);
              $dep = \Carbon\Carbon::parse($booking->departed_at);
              $dur = $arr->diffInMinutes($dep);
              $slotDur = $booking->slot->start_at->diffInMinutes($booking->slot->end_at);
              $badge = $dur > $slotDur
                ? ['Over Time', 'bg-red-600']
                : ['On Time', 'bg-green-600'];
              $d = floor($dur / 1440);
              $h = floor(($dur % 1440) / 60);
              $m = $dur % 60;
            ?>
            <div class="text-xs text-gray-700 mt-1">
              ⏱ Duration: <?php echo e("$d d $h h $m m"); ?>

              <span class="ml-2 inline-block px-2 py-0.5 rounded text-white text-xs font-semibold <?php echo e($badge[1]); ?>">
                Tip: <?php echo e($badge[0]); ?>

              </span>
            </div>
          <?php endif; ?>
        </td>

        
        <td class="px-4 py-2 align-top">
          <?php if($booking->arrived_at && !$booking->departed_at): ?>
            <?php $movement = $booking->movements->first(); ?>
            <div class="space-y-1">
              
              <div><?php echo $booking->tipping_status_badge; ?></div>
              
              
              <?php if($movement && ($movement->tippingLocation || $movement->tippingBay)): ?>
                <?php
                  $isEmptyTrailer = in_array($movement->current_status, ['empty', 'awaiting_collection']);
                ?>
                <div class="text-xs text-gray-600">
                  <?php if($isEmptyTrailer): ?>
                    
                    <?php if($movement->current_status === 'awaiting_collection' && $movement->tippingLocation): ?>
                      <div class="text-green-700">📦 <?php echo e($movement->tippingLocation->name); ?> (Collection Zone)</div>
                    <?php elseif($movement->tippingBay): ?>
                      <div class="text-yellow-700">🚛 <?php echo e($movement->tippingBay->name); ?> (Empty - Ready to Move)</div>
                    <?php elseif($movement->tippingLocation): ?>
                      <div class="text-blue-700">📍 <?php echo e($movement->tippingLocation->name); ?> (Empty)</div>
                    <?php endif; ?>
                  <?php else: ?>
                    
                    <?php if($movement->tippingBay): ?>
                      <div class="text-yellow-700">🏗️ <?php echo e($movement->tippingBay->name); ?> (Tipping)</div>
                    <?php elseif($movement->tippingLocation): ?>
                      <div class="text-blue-700">📍 <?php echo e($movement->tippingLocation->name); ?> (Full - Awaiting)</div>
                    <?php endif; ?>
                  <?php endif; ?>
                  <div class="text-gray-400">(<?php echo e($movement->tippingLocation?->depot?->name ?? $movement->tippingBay?->depot?->name); ?>)</div>
                </div>
              <?php endif; ?>
              
              
              <?php if($booking->tipping_status && $booking->tipping_status !== 'departed'): ?>
                <?php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; ?>
                <?php if($canTakeAction): ?>
                  <a href="<?php echo e(route('admin.tipping-workflow.show', $booking)); ?>" 
                     class="text-xs text-blue-600 hover:text-blue-800 block">
                    Manage →
                  </a>
                <?php else: ?>
                  <span class="text-xs text-gray-400 cursor-not-allowed block" 
                        title="Actions only available for your default depot">
                    Manage →
                  </span>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          <?php elseif($booking->status === 'completed'): ?>
            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
              ✅ Complete
            </span>
          <?php else: ?>
            <span class="text-xs text-gray-400">Not started</span>
          <?php endif; ?>
        </td>

        
        <td class="px-4 py-2 align-top space-y-1">
          <div class="flex flex-col space-y-1">
            
            <?php
              $hasHistory = false;
              // Check if this booking has history (was rebooked or is part of a rebook chain)
              try {
                $hasHistory = $booking->original_booking_id || 
                             $booking->is_rebooked || 
                             ($booking->cancellation_reason && str_contains($booking->cancellation_reason, 'Rebooked'));
              } catch (\Exception $e) {
                $hasHistory = false;
              }
            ?>
            
            <?php if($hasHistory): ?>
              <a href="<?php echo e(route('admin.bookings.history', $booking)); ?>"
                 class="inline-block px-2 py-1 bg-purple-500 text-white rounded-full hover:bg-purple-600 text-xs text-center" 
                 title="This booking has history - view rebook/cancel history">
                📋 History
              </a>
            <?php endif; ?>
            
            
            <a href="<?php echo e(route('admin.bookings.show', $booking)); ?>"
               class="inline-block px-2 py-1 bg-blue-500 text-white rounded-full hover:bg-blue-600 text-xs text-center">
              View
            </a>
            
            
            <?php
              $isCancelled = $booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'));
            ?>
            
            <?php if(!$isCancelled): ?>
              <?php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; ?>
              <?php if($canTakeAction): ?>
                <a href="<?php echo e(route('admin.bookings.edit', $booking)); ?>"
                   class="inline-block px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs text-center">
                  Edit
                </a>
              <?php else: ?>
                <span class="inline-block px-2 py-1 bg-gray-300 text-gray-500 rounded-full text-xs text-center cursor-not-allowed"
                      title="Actions only available for your default depot">
                  Edit
                </span>
              <?php endif; ?>
            <?php endif; ?>
            
          </div>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</tbody>
    </table>

    <div class="mt-4"><?php echo e($bookings->links()); ?></div>

    
    <div class="mt-10">
      <?php $__currentLoopData = $summaryByDepotCustomer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dep => $custs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <h3 class="text-lg font-semibold mb-4 text-center bg-blue-600 text-white px-4 py-1 rounded"><?php echo e($dep); ?></h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <?php $__currentLoopData = $custs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $sum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white border rounded shadow p-4">
              <h4 class="font-semibold mb-2">🧾 <?php echo e($name==='_totals' ? 'Site Totals' : $name); ?></h4>
              <div class="space-y-1 text-sm">
                <div>✅ Arrived: <?php echo e($sum['arrived']); ?></div>
                <div>⏰ Late: <?php echo e($sum['late']); ?></div>
                <div>🚚 Outstanding: <?php echo e($sum['outstanding']); ?></div>
                <?php if($name==='__totals'): ?>
                  <div>🗓️ Slots Used: <?php echo e($sum['arrived'] + $sum['late'] + $sum['outstanding']); ?> of <?php echo e($bookings->count()); ?></div>
                <?php endif; ?>
                <div>📦 Exp Units: <?php echo e(number_format($sum['expected_cases'])); ?> / Act: <?php echo e(number_format($sum['actual_cases'])); ?></div>
                <div>🔺 Δ: <?php echo e(number_format($sum['case_variance'])); ?></div>
                <div>📦 Pal Exp: <?php echo e(number_format($sum['expected_pallets'])); ?> / Act: <?php echo e(number_format($sum['actual_pallets'])); ?></div>
                <div>🔺 Δ Pal: <?php echo e(number_format($sum['pallet_variance'])); ?></div>
              </div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  <!-- Arrival Modal -->
  <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing</h3>
          <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Booking Summary -->
        <div id="bookingSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <!-- Will be populated by JavaScript -->
        </div>

        <!-- Arrival Form -->
        <form id="arrivalForm" method="POST" class="mt-6">
          <?php echo csrf_field(); ?>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Required Vehicle Registration -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Vehicle Registration <span class="text-red-500">*</span>
              </label>
              <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                     placeholder="e.g., AB12 CDE"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
            </div>

            <!-- Container Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
              <input type="text" id="containerNumber" name="container_number"
                     placeholder="e.g., CONT123456 or TR123456"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
            </div>

            <!-- Carrier Company -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Carrier Company <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input type="text" 
                       id="carrierCompany" 
                       name="carrier_name"
                       placeholder="Search or type carrier name..."
                       required
                       autocomplete="off"
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 pr-10">
                
                
                <input type="hidden" 
                       id="carrierId" 
                       name="carrier_id">
                
                
                <div id="carrierDropdown" 
                     class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                  
                </div>
                
                
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <span id="carrierStatus" class="text-xs"></span>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
            </div>


            <!-- Trailer Size -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trailer Type</label>
              <select id="trailerType" name="trailer_type_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                <option value="">– Select Trailer Type –</option>
                <?php $__currentLoopData = $trailerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailerType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($trailerType->id); ?>" <?php echo e(request('trailer_type_id') == $trailerType->id ? 'selected' : ''); ?>>
                    <?php echo e($trailerType->name); ?>

                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <p class="text-xs text-gray-500 mt-1">Type and size of trailer/container</p>
            </div>

            <!-- Tipping Assignment Section -->
            <div class="md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
              <h4 class="font-medium text-blue-800 mb-3">🚛 Vehicle Assignment</h4>
              <p class="text-sm text-blue-700 mb-4">Assign vehicle to location upon arrival:</p>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Drop Location -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">🚛 Drop Location (Optional)</label>
                  <select id="tippingLocation" name="tipping_location_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">– Assign Drop Location –</option>
                    <?php if(isset($tippingLocations)): ?>
                      <?php $__currentLoopData = $tippingLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($location->id); ?>">
                          <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                  </select>
                  <p class="text-xs text-gray-600 mt-1">Pre-assign drop location if known</p>
                </div>

                <!-- Direct Bay Assignment -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">🏗️ Tipping Bay (Direct)</label>
                  <select id="tippingBay" name="tipping_bay_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">– No Direct Assignment –</option>
                    <?php if(isset($tippingBays)): ?>
                      <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($bay->id); ?>" <?php if($bay->is_occupied): echo 'disabled'; endif; ?>>
                          <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>) 
                          <?php if($bay->is_occupied): ?>
                            - Occupied
                          <?php else: ?>
                            - Available Now
                          <?php endif; ?>
                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                  </select>
                  <p class="text-xs text-gray-600 mt-1">Assign directly to tipping bay</p>
                </div>
              </div>
            </div>

          </div>

          <!-- Special Instructions -->
          <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
            <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
            <p id="specialInstructionsText" class="text-yellow-700"></p>
          </div>

          <!-- Arrival Time Display -->
          <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-800 mb-2">📅 Arrival Time:</h4>
            <p class="text-green-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
          </div>

          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeArrivalModal()" 
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" onclick="return validateArrivalForm()"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
              🚛 Mark Vehicle Arrived
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Departure Modal -->
  <div id="departureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">🏁 Vehicle Departure Processing</h3>
          <button onclick="closeDepartureModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div id="departureSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <!-- Will be populated by JavaScript -->
        </div>

        <!-- Departure Form -->
        <form id="departureForm" method="POST" class="mt-6">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PATCH'); ?>
          
          <div class="grid grid-cols-1 gap-4">
            
            <!-- Simple Departure Options -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                What happened with the vehicle? <span class="text-red-500">*</span>
              </label>
              <p class="text-xs text-gray-600 mb-3">Select the departure scenario that matches what actually happened:</p>
              <div class="grid grid-cols-2 gap-3">
                <!-- Vehicle Left With Trailer -->
                <div>
                  <input type="radio" name="departure_scenario" id="leftWithTrailer" value="completed_with_trailer" required
                         class="hidden peer">
                  <label for="leftWithTrailer" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🚛✅</div>
                    <div class="text-sm font-medium text-center">Vehicle Left<br>WITH Trailer</div>
                    <div class="text-xs text-gray-500 mt-1">Job complete - normal departure</div>
                  </label>
                </div>
                
                <!-- Vehicle Left Without Trailer -->
                <div>
                  <input type="radio" name="departure_scenario" id="leftWithoutTrailer" value="completed_dropped_trailer" required
                         class="hidden peer">
                  <label for="leftWithoutTrailer" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🚛📦</div>
                    <div class="text-sm font-medium text-center">Vehicle Left<br>WITHOUT Trailer</div>
                    <div class="text-xs text-gray-500 mt-1">Trailer left for collection</div>
                  </label>
                </div>
                
                <!-- Vehicle Still On Site -->
                <div>
                  <input type="radio" name="departure_scenario" id="stillOnSite" value="drop_and_wait" required
                         class="hidden peer">
                  <label for="stillOnSite" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🅿️🚛</div>
                    <div class="text-sm font-medium text-center">Vehicle Still<br>ON SITE</div>
                    <div class="text-xs text-gray-500 mt-1">Not yet departed</div>
                  </label>
                </div>
                
                <!-- Emergency/Problem -->
                <div>
                  <input type="radio" name="departure_scenario" id="problemDeparture" value="emergency_departure" required
                         class="hidden peer">
                  <label for="problemDeparture" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🚨❌</div>
                    <div class="text-sm font-medium text-center">Problem/<br>Emergency</div>
                    <div class="text-xs text-gray-500 mt-1">Issue or early departure</div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Departure Notes -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
              <textarea name="departure_notes" id="departureNotes"
                        placeholder="Optional notes about the departure..."
                        rows="3"
                        class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
              <p class="text-xs text-gray-500 mt-1">Any additional information about the departure</p>
            </div>

            <!-- Trailer Left On Site -->
            <div id="trailerLeftSection" class="hidden p-4 bg-orange-50 border border-orange-200 rounded-lg">
              <h4 class="font-medium text-orange-800 mb-3">📦 Trailer Left On Site</h4>
              
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Where is the trailer located on site?</label>
                  <select id="dropped_trailer_location" name="dropped_trailer_location" required class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <option value="">– Select Location –</option>
                    
                    <optgroup label="🏷️ Current Location">
                      
                    </optgroup>
                    
                    <optgroup label="📦 Drop Zones">
                      <?php $__currentLoopData = $tippingLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="DROP_<?php echo e($location->id); ?>" data-type="location" data-name="<?php echo e($location->name); ?>">
                          <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                    
                    <optgroup label="🏗️ Tipping Bays">
                      <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="BAY_<?php echo e($bay->id); ?>" data-type="bay" data-name="<?php echo e($bay->name); ?>"
                                <?php if($bay->is_occupied): ?> disabled <?php endif; ?>>
                          <?php echo e($bay->name); ?> 
                          <?php if($bay->is_occupied): ?> (Occupied) <?php else: ?> <?php endif; ?>
                        </option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">
                    Default: Current location. Select a different location if trailer needs to be moved.
                  </p>
                  <div id="currentLocationInfo" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-sm">
                    
                  </div>
                  
                  <div id="tippingWarning" class="hidden mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm">
                    <div class="text-yellow-800">
                      <strong>⚠️ Tipping In Progress:</strong><br>
                      <span class="text-yellow-700">• Trailer can remain at current bay until tipping completes</span><br>
                      <span class="text-yellow-700">• Moving to other bays is restricted during active tipping</span><br>
                      <span class="text-yellow-700">• Drop zones and collection zones are still available</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Automatic Status Information -->
              <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <h5 class="font-medium text-blue-800 mb-2">ℹ️ Automatic Status</h5>
                <ul class="text-sm text-blue-700 space-y-1">
                  <li>• Status will be set automatically based on tipping completion</li>
                  <li>• Collection scheduling managed by system workflow</li>
                  <li>• No driver communication required</li>
                </ul>
              </div>
            </div>
            
          </div>

          <!-- Departure Time Display -->
          <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-800 mb-2">📅 Departure Time:</h4>
            <p class="text-green-700 font-semibold" id="departureTime">Will be recorded as: [Current Time]</p>
          </div>

          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeDepartureModal()" 
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
              🏁 Mark Vehicle Departed
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bay Assignment Modal -->
  <div id="bayAssignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-bold text-gray-900 mb-4">🏗️ Assign Bay from Waiting Area</h3>
        
        <form id="bayAssignmentForm" method="POST">
          <?php echo csrf_field(); ?>
          
          <!-- Booking Info -->
          <div class="mb-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
              <strong>Booking:</strong> <span id="bayBookingRef"></span><br>
              <strong>Currently in:</strong> <span id="bayCurrentLocation"></span>
            </p>
          </div>
          
          <!-- Bay Selection -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select Tipping Bay <span class="text-red-500">*</span>
            </label>
            <select name="tipping_bay_id" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">– Select Available Bay –</option>
              <?php if(isset($tippingBays)): ?>
                <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($bay->id); ?>" <?php if($bay->is_occupied): echo 'disabled'; endif; ?>>
                    <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>) 
                    <?php if($bay->is_occupied): ?>
                      - Occupied
                    <?php else: ?>
                      - Available
                    <?php endif; ?>
                  </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <?php endif; ?>
            </select>
          </div>
          
          <!-- Assignment Notes -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Notes (Optional)</label>
            <textarea name="assignment_notes" rows="2" 
                      placeholder="e.g., Priority load, special handling required..."
                      class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
          </div>
          
          <!-- Time Display -->
          <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
            <p class="text-sm text-green-800">
              <strong>Assignment Time:</strong> <span id="bayAssignmentTime">Will be recorded as current time</span>
            </p>
          </div>
          
          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeBayAssignmentModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              🏗️ Assign Bay
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Quick Trailer Collection Modal -->
  <div id="quickTrailerCollectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-bold text-gray-900 mb-4">🚛 Quick Trailer Collection</h3>
        <p class="text-sm text-gray-600 mb-4">Record a vehicle arriving to collect a trailer (no booking required)</p>
        
        <form action="<?php echo e(route('admin.empty-unit-collection.process')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          
          <div class="space-y-3">
            <!-- Vehicle Registration -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Vehicle Registration <span class="text-red-500">*</span>
              </label>
              <input type="text" name="vehicle_registration" required
                     placeholder="e.g., AB12 CDE"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            
            
            <!-- Available Trailers Dropdown -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Select Trailer to Collect <span class="text-red-500">*</span>
              </label>
              <select name="collected_from_booking_id" id="availableTrailerSelect" required
                      onchange="updateTrailerDetails(this)"
                      class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                <option value="">– Choose Trailer to Collect –</option>
                <!-- Options will be loaded dynamically -->
              </select>
              <p class="text-xs text-gray-500 mt-1">Shows trailer number and current location on site</p>
            </div>
            
            <!-- Trailer Location Display (read-only) -->
            <div id="trailerLocationDisplay" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <h4 class="font-medium text-blue-800 mb-2">📍 Trailer Location</h4>
              <div class="text-sm">
                <div><strong>Trailer:</strong> <span id="displayTrailerNumber"></span></div>
                <div><strong>Current Location:</strong> <span id="displayTrailerLocation"></span></div>
                <div class="text-blue-700 mt-1">
                  <strong>→ Direct driver to this location</strong>
                </div>
              </div>
            </div>
            
            <!-- Company -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
              <input type="text" name="carrier_company"
                     placeholder="e.g., ABC Transport"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
          </div>
          
          <div class="mt-6 flex justify-end space-x-3">
            <button type="button" onclick="closeQuickTrailerCollectionModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
              🚛 Record Collection
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let currentBookingId = null;

    function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, carrierCompany, expectedCases, expectedPallets, specialInstructions) {
      currentBookingId = bookingId;
      
      // Update booking summary
      document.getElementById('bookingSummary').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <strong>Booking:</strong> ${bookingRef}<br>
            <strong>Customer:</strong> ${customer}
          </div>
          <div>
            <strong>Depot:</strong> ${depot}<br>
            <strong>Scheduled:</strong> ${scheduledTime}
          </div>
          <div>
            <strong>Expected:</strong> ${expectedCases} cases, ${expectedPallets} pallets
          </div>
        </div>
      `;

      // Update form action
      document.getElementById('arrivalForm').action = `/admin/bookings/${bookingId}/arrival`;

      // Populate form fields
      document.getElementById('vehicleRegistration').value = vehicleReg;
      document.getElementById('containerNumber').value = containerNum;
      // Set carrier name in search field
      document.getElementById('carrierCompany').value = carrierCompany;

      // Show special instructions if any
      if (specialInstructions && specialInstructions.trim() !== '') {
        document.getElementById('specialInstructionsText').textContent = specialInstructions;
        document.getElementById('specialInstructions').classList.remove('hidden');
      } else {
        document.getElementById('specialInstructions').classList.add('hidden');
      }

      // Update arrival time display
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;

      // Show modal
      document.getElementById('arrivalModal').classList.remove('hidden');
      
      // Focus on vehicle registration field
      setTimeout(() => {
        document.getElementById('vehicleRegistration').focus();
      }, 100);
    }

    function closeArrivalModal() {
      document.getElementById('arrivalModal').classList.add('hidden');
      currentBookingId = null;
    }

    // Close modal when clicking outside
    document.getElementById('arrivalModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeArrivalModal();
      }
    });

    // Update time display every second
    setInterval(() => {
      if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
        const now = new Date();
        const timeString = now.toLocaleString('en-GB', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        });
        document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;
      }
    }, 1000);

    // Carrier search functionality for arrival modal
    const searchInput = document.getElementById('carrierCompany');
    const carrierIdInput = document.getElementById('carrierId');
    const dropdown = document.getElementById('carrierDropdown');
    const statusSpan = document.getElementById('carrierStatus');
    
    if (searchInput) {
        let searchTimeout;
        let selectedCarrierId = '';
        let currentPage = 1;
        let isLoading = false;
        
        // Update status based on current state
        function updateCarrierStatus() {
            if (selectedCarrierId) {
                statusSpan.textContent = '✓';
                statusSpan.className = 'text-xs text-green-600';
            } else if (searchInput.value.trim()) {
                statusSpan.textContent = '+';
                statusSpan.className = 'text-xs text-blue-600';
                statusSpan.title = 'Will create new carrier';
            } else {
                statusSpan.textContent = '';
                statusSpan.className = 'text-xs';
            }
        }
        
        // Search carriers
        function searchCarriers(query, page = 1) {
            if (query.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }
            
            if (isLoading) return;
            isLoading = true;
            
            fetch(`<?php echo e(route('api.carriers.search')); ?>?q=${encodeURIComponent(query)}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (page === 1) {
                        populateCarrierDropdown(data, query);
                    } else {
                        appendToCarrierDropdown(data, query);
                    }
                    currentPage = page;
                    isLoading = false;
                })
                .catch(error => {
                    console.error('Search failed:', error);
                    dropdown.classList.add('hidden');
                    isLoading = false;
                });
        }
        
        // Populate dropdown with results
        function populateCarrierDropdown(data, query) {
            dropdown.innerHTML = '';
            
            // Show existing carriers
            data.carriers.forEach(carrier => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                item.innerHTML = `
                    <div class="font-medium text-gray-900">${carrier.name}</div>
                    <div class="text-xs text-gray-500">
                        ${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}
                    </div>
                `;
                item.onclick = () => selectCarrier(carrier.id, carrier.name);
                dropdown.appendChild(item);
            });
            
            // Add "Create new" option if no exact match
            if (!data.exact_match && query.trim()) {
                const createItem = document.createElement('div');
                createItem.className = 'px-3 py-2 hover:bg-green-50 cursor-pointer border-t-2 border-green-200 bg-green-25';
                createItem.innerHTML = `
                    <div class="font-medium text-green-800">➕ Create "${query}"</div>
                    <div class="text-xs text-green-600">Add as new carrier and use immediately</div>
                `;
                createItem.onclick = () => createNewCarrier(query);
                dropdown.appendChild(createItem);
            }
            
            dropdown.classList.remove('hidden');
        }
        
        // Select existing carrier
        function selectCarrier(id, name) {
            selectedCarrierId = id;
            carrierIdInput.value = id;
            searchInput.value = name;
            dropdown.classList.add('hidden');
            updateCarrierStatus();
        }
        
        // Create new carrier (simple version for arrival modal)
        function createNewCarrier(name) {
            selectedCarrierId = '';
            carrierIdInput.value = '';
            searchInput.value = name;
            dropdown.classList.add('hidden');
            updateCarrierStatus();
        }
        
        // Search input handler
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Reset selection when typing
            selectedCarrierId = '';
            carrierIdInput.value = '';
            currentPage = 1;
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchCarriers(query, 1);
            }, 300);
            
            updateCarrierStatus();
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Show dropdown on focus if there's content
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                searchCarriers(this.value);
            }
        });
        
        // Initial status update
        updateCarrierStatus();
    }

    function validateArrivalForm() {
      const vehicleReg = document.getElementById('vehicleRegistration').value.trim();
      const carrierCompany = document.getElementById('carrierCompany').value;
      const tippingLocation = document.getElementById('tippingLocation').value;
      const tippingBay = document.getElementById('tippingBay').value;

      // Check required fields
      if (!vehicleReg) {
        alert('Vehicle Registration is required');
        document.getElementById('vehicleRegistration').focus();
        return false;
      }

      if (!carrierCompany) {
        alert('Carrier Company is required');
        document.getElementById('carrierCompany').focus();
        return false;
      }

      // Check tipping assignment (must select one)
      if (!tippingLocation && !tippingBay) {
        alert('You must select either a Drop Location or assign directly to a Tipping Bay');
        return false;
      }

      if (tippingLocation && tippingBay) {
        alert('Please select either Drop Location OR Tipping Bay, not both');
        return false;
      }

      console.log('Form validation passed, submitting...');
      return true;
    }

    // Departure Modal Functions
    let currentDepartureBookingId = null;

    function openDepartureModal(bookingId, bookingRef, customer, vehicleReg, currentLocation, currentLocationName, tippingStatus = null) {
      currentDepartureBookingId = bookingId;
      
      // Update departure summary
      document.getElementById('departureSummary').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <strong>Booking:</strong> ${bookingRef}<br>
            <strong>Customer:</strong> ${customer}
          </div>
          <div>
            <strong>Vehicle:</strong> ${vehicleReg}<br>
            <strong>Departing:</strong> Now
          </div>
        </div>
      `;
      
      // Determine if tipping is in progress
      const isTippingInProgress = tippingStatus === 'unloading' || currentLocationName.includes('(Tipping)');
      const isAtBay = currentLocation && currentLocation.startsWith('BAY_');
      
      // Populate current location in the trailer dropdown
      const locationSelect = document.getElementById('dropped_trailer_location');
      const currentLocationInfo = document.getElementById('currentLocationInfo');
      const tippingWarning = document.getElementById('tippingWarning');
      const currentOptgroup = locationSelect.querySelector('optgroup[label="🏷️ Current Location"]');
      
      // Clear existing current location options
      currentOptgroup.innerHTML = '';
      
      // Apply smart restrictions for bay movements during tipping
      if (isTippingInProgress && isAtBay) {
        // Show tipping warning
        tippingWarning.classList.remove('hidden');
        
        // LOCK the dropdown entirely during active tipping
        locationSelect.disabled = true;
        locationSelect.style.backgroundColor = '#f3f4f6'; // Gray background
        locationSelect.style.cursor = 'not-allowed';
        
        // Hide all other option groups during tipping
        const allOptgroups = locationSelect.querySelectorAll('optgroup');
        allOptgroups.forEach(optgroup => {
          if (optgroup.label !== '🏷️ Current Location') {
            optgroup.style.display = 'none';
          }
        });
        
        // Update the help text
        const helpText = locationSelect.parentElement.querySelector('.text-xs.text-gray-500');
        if (helpText) {
          helpText.innerHTML = '<strong class="text-orange-600">🔒 Location locked during active tipping</strong> - Trailer must remain at current bay';
        }
      } else {
        // Unlock dropdown for normal operations
        tippingWarning.classList.add('hidden');
        locationSelect.disabled = false;
        locationSelect.style.backgroundColor = '';
        locationSelect.style.cursor = '';
        
        // Show all option groups again
        const allOptgroups = locationSelect.querySelectorAll('optgroup');
        allOptgroups.forEach(optgroup => {
          optgroup.style.display = '';
        });
        
        // Restore original help text
        const helpText = locationSelect.parentElement.querySelector('.text-xs.text-gray-500');
        if (helpText) {
          helpText.innerHTML = 'Default: Current location. Select a different location if trailer needs to be moved.';
        }
      }
      
      if (currentLocation && currentLocation !== 'null' && currentLocation !== '') {
        // Add current location as the first option and select it
        const currentOption = document.createElement('option');
        currentOption.value = currentLocation;
        currentOption.textContent = `${currentLocationName} (Current Location)`;
        currentOption.selected = true;
        currentOptgroup.appendChild(currentOption);
        
        // Update info display based on context
        let infoMessage = '';
        let infoClass = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm';
        
        if (isTippingInProgress && isAtBay) {
          infoMessage = `
            <strong>📍 Current Location:</strong> ${currentLocationName}<br>
            <span class="text-blue-700">✅ Default: Trailer will remain at bay during tipping</span><br>
            <span class="text-orange-700">⚠️ Other bay movements restricted until tipping completes</span>
          `;
        } else {
          infoMessage = `
            <strong>📍 Current Location:</strong> ${currentLocationName}<br>
            <span class="text-blue-700">✅ Default: Trailer will remain here unless you select a different location below</span>
          `;
        }
        
        currentLocationInfo.innerHTML = infoMessage;
        currentLocationInfo.style.display = 'block';
        currentLocationInfo.className = infoClass;
      } else {
        // No current location detected - vehicle might be in general area or not properly assigned
        if (currentLocation === 'ONSITE_GENERAL') {
          currentLocationInfo.innerHTML = `
            <strong>📍 Current Location:</strong> ${currentLocationName}<br>
            <span class="text-orange-700">⚠️ Vehicle is on site but not assigned to specific drop zone - please select location for trailer</span>
          `;
          currentLocationInfo.className = 'mt-2 p-3 bg-orange-50 border border-orange-200 rounded text-sm';
        } else {
          currentLocationInfo.innerHTML = `
            <strong>⚠️ Current Location Unknown:</strong> Vehicle location not detected<br>
            <span class="text-red-700">Please select where the trailer should be located for collection</span>
          `;
          currentLocationInfo.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded text-sm';
        }
        currentLocationInfo.style.display = 'block';
        locationSelect.selectedIndex = 0; // Reset to "Select Location"
      }

      // Update form action
      document.getElementById('departureForm').action = `/admin/bookings/${bookingId}/departure`;

      // Update departure time display
      updateDepartureTime();

      // Show modal
      document.getElementById('departureModal').classList.remove('hidden');
      
      // Focus on departure scenario dropdown
      setTimeout(() => {
        document.getElementById('departureScenario').focus();
      }, 100);
    }

    function closeDepartureModal() {
      document.getElementById('departureModal').classList.add('hidden');
      currentDepartureBookingId = null;
      
      // Reset form
      document.getElementById('departureForm').reset();
      document.getElementById('trailerLeftSection').classList.add('hidden');
    }

    function updateDepartureTime() {
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('departureTime').textContent = `Will be recorded as: ${timeString}`;
    }

    // Handle departure scenario change
    document.addEventListener('DOMContentLoaded', function() {
      const departureRadios = document.querySelectorAll('input[name="departure_scenario"]');
      departureRadios.forEach(radio => {
        radio.addEventListener('change', function() {
          const trailerSection = document.getElementById('trailerLeftSection');
          
          // Hide section initially
          trailerSection.classList.add('hidden');
          
          // Show trailer section only when vehicle leaves without trailer
          if (this.value === 'completed_dropped_trailer') {
            trailerSection.classList.remove('hidden');
          }
        });
      });

      // Close departure modal when clicking outside
      document.getElementById('departureModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeDepartureModal();
        }
      });

      // Update departure time every second
      setInterval(() => {
        if (!document.getElementById('departureModal').classList.contains('hidden')) {
          updateDepartureTime();
        }
      }, 1000);
    });
    
    // Bay Assignment Modal Functions
    function openBayAssignmentModal(bookingId, bookingRef, waitingArea) {
      document.getElementById('bayBookingRef').textContent = bookingRef;
      document.getElementById('bayCurrentLocation').textContent = waitingArea;
      
      // Set form action
      const form = document.getElementById('bayAssignmentForm');
      form.action = `/admin/bookings/${bookingId}/assign-bay`;
      
      // Update time display
      updateBayAssignmentTime();
      
      // Show modal
      document.getElementById('bayAssignmentModal').classList.remove('hidden');
    }

    function closeBayAssignmentModal() {
      document.getElementById('bayAssignmentModal').classList.add('hidden');
    }
    
    function updateBayAssignmentTime() {
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('bayAssignmentTime').textContent = `Will be recorded as: ${timeString}`;
    }
    
    // Close bay assignment modal when clicking outside
    document.getElementById('bayAssignmentModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeBayAssignmentModal();
      }
    });
    
    // Quick Trailer Collection Modal Functions
    function openQuickTrailerCollectionModal() {
      document.getElementById('quickTrailerCollectionModal').classList.remove('hidden');
      loadAvailableTrailers();
    }

    function closeQuickTrailerCollectionModal() {
      document.getElementById('quickTrailerCollectionModal').classList.add('hidden');
      // Reset form
      document.getElementById('availableTrailerSelect').value = '';
      document.getElementById('trailerLocationDisplay').classList.add('hidden');
      
      // Clear hidden fields if they exist
      const trailerNumberInput = document.getElementById('hiddenTrailerNumber');
      const locationInput = document.getElementById('hiddenLocation');
      if (trailerNumberInput) trailerNumberInput.remove();
      if (locationInput) locationInput.remove();
    }
    
    // Load available trailers from server
    async function loadAvailableTrailers() {
      try {
        const response = await fetch('/admin/api/available-trailers');
        const trailers = await response.json();
        
        const select = document.getElementById('availableTrailerSelect');
        // Clear existing options except the first one
        select.innerHTML = '<option value="">– Choose Trailer to Collect –</option>';
        
        trailers.forEach(trailer => {
          const option = document.createElement('option');
          option.value = trailer.id;
          option.textContent = `🚛 ${trailer.container_number || 'No Container#'} → 📍 ${trailer.dropped_trailer_location || 'Unknown Location'}`;
          option.dataset.containerNumber = trailer.container_number || 'No container number';
          option.dataset.location = trailer.dropped_trailer_location || 'Unknown location';
          option.dataset.bookingRef = trailer.booking_reference || '';
          select.appendChild(option);
        });
        
        if (trailers.length === 0) {
          const option = document.createElement('option');
          option.value = '';
          option.textContent = '– No trailers available for collection –';
          option.disabled = true;
          select.appendChild(option);
        }
      } catch (error) {
        console.error('Failed to load available trailers:', error);
        const select = document.getElementById('availableTrailerSelect');
        select.innerHTML = '<option value="">– Error loading trailers –</option>';
      }
    }
    
    // Update trailer details when selection changes
    function updateTrailerDetails(selectElement) {
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const locationDisplay = document.getElementById('trailerLocationDisplay');
      const displayTrailerNumber = document.getElementById('displayTrailerNumber');
      const displayTrailerLocation = document.getElementById('displayTrailerLocation');
      
      if (selectedOption.value) {
        // Show the location display
        locationDisplay.classList.remove('hidden');
        displayTrailerNumber.textContent = selectedOption.dataset.containerNumber;
        displayTrailerLocation.textContent = selectedOption.dataset.location;
        
        // Set hidden fields for form submission
        // Create hidden inputs if they don't exist
        let trailerNumberInput = document.getElementById('hiddenTrailerNumber');
        let locationInput = document.getElementById('hiddenLocation');
        
        if (!trailerNumberInput) {
          trailerNumberInput = document.createElement('input');
          trailerNumberInput.type = 'hidden';
          trailerNumberInput.name = 'collected_trailer_number';
          trailerNumberInput.id = 'hiddenTrailerNumber';
          selectElement.form.appendChild(trailerNumberInput);
        }
        
        if (!locationInput) {
          locationInput = document.createElement('input');
          locationInput.type = 'hidden';
          locationInput.name = 'collection_location';
          locationInput.id = 'hiddenLocation';
          selectElement.form.appendChild(locationInput);
        }
        
        trailerNumberInput.value = selectedOption.dataset.containerNumber;
        locationInput.value = selectedOption.dataset.location;
      } else {
        // Hide the location display
        locationDisplay.classList.add('hidden');
        
        // Clear hidden fields
        const trailerNumberInput = document.getElementById('hiddenTrailerNumber');
        const locationInput = document.getElementById('hiddenLocation');
        if (trailerNumberInput) trailerNumberInput.value = '';
        if (locationInput) locationInput.value = '';
      }
    }
    
    // Close quick collection modal when clicking outside
    document.getElementById('quickTrailerCollectionModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeQuickTrailerCollectionModal();
      }
    });

    // Trailer Collection Modal Functions
    function openTrailerCollectionModal() {
      // Open the empty unit collection page in a new window/tab
      window.open('<?php echo e(route("admin.empty-unit-collection")); ?>', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    }

    // Advanced Filters Toggle
    function toggleAdvancedFilters() {
      const content = document.getElementById('advanced-filters-content');
      const icon = document.getElementById('advanced-filters-icon');
      
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
      } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
      }
    }

    // Show advanced filters if any are active
    document.addEventListener('DOMContentLoaded', function() {
      const hasActiveFilters = <?php echo e(request()->hasAny(['depot_id', 'customer_id', 'booking_type_id', 'week_number', 'from', 'to', 'arrival']) ? 'true' : 'false'); ?>;
      if (hasActiveFilters) {
        toggleAdvancedFilters();
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
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/index.blade.php ENDPATH**/ ?>