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
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Factory Bookings</h2>
        <p class="text-sm text-gray-600 mt-1">Ad-hoc deliveries registered on arrival</p>
      </div>
      <div class="flex gap-2">
        <a href="<?php echo e(route('app.bookings.index')); ?>"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          📋 Scheduled Bookings
        </a>
        <a href="<?php echo e(route('app.factory-bookings.create')); ?>"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + Register Factory Delivery
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
    <?php if(session('error')): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>
    
    <div class="mb-6 bg-white rounded-lg shadow-sm border p-4">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        
        <div>
          <form method="GET" action="<?php echo e(route('app.factory-bookings.index')); ?>" class="flex gap-2">
            
            <?php $__currentLoopData = request()->except(['search', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <input type="text" 
                   name="search" 
                   value="<?php echo e(request('search')); ?>"
                   placeholder="🔍 Search reference, vehicle, driver, customer..."
                   class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
              Search
            </button>
          </form>
        </div>
        
        <div>
          <form method="GET" action="<?php echo e(route('app.factory-bookings.index')); ?>" class="flex gap-2">
            
            <?php $__currentLoopData = request()->except(['depot_id', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select name="depot_id" onchange="this.form.submit()" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
              <option value="">All Depots</option>
              <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($depot->id); ?>" <?php echo e(request('depot_id') == $depot->id ? 'selected' : ''); ?>>
                  <?php echo e($depot->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </form>
        </div>
        
        <div>
          <form method="GET" action="<?php echo e(route('app.factory-bookings.index')); ?>" class="flex gap-2">
            
            <?php $__currentLoopData = request()->except(['status', 'page']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <select name="status" onchange="this.form.submit()" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
              <option value="">All Status</option>
              <option value="arrived" <?php echo e(request('status') == 'arrived' ? 'selected' : ''); ?>>Arrived</option>
              <option value="processing" <?php echo e(request('status') == 'processing' ? 'selected' : ''); ?>>Processing</option>
              <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>Completed</option>
              <option value="departed" <?php echo e(request('status') == 'departed' ? 'selected' : ''); ?>>Departed</option>
            </select>
          </form>
        </div>
      </div>
      <?php if(request()->hasAny(['search', 'depot_id', 'status'])): ?>
        <div class="mt-3 pt-3 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              <?php if(request('search')): ?>
                <span class="mr-3">🔍 <strong>Search:</strong> "<?php echo e(request('search')); ?>"</span>
              <?php endif; ?>
              <?php if(request('depot_id')): ?>
                <span class="mr-3">🏭 <strong>Depot:</strong> <?php echo e($depots->find(request('depot_id'))->name ?? 'Unknown'); ?></span>
              <?php endif; ?>
              <?php if(request('status')): ?>
                <span class="mr-3">📊 <strong>Status:</strong> <?php echo e(ucfirst(request('status'))); ?></span>
              <?php endif; ?>
            </div>
            <a href="<?php echo e(route('app.factory-bookings.index')); ?>" class="text-sm text-blue-600 hover:text-blue-800">
              Clear Filters
            </a>
          </div>
        </div>
      <?php endif; ?>
    </div>
    
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
      <h3 class="text-sm font-semibold text-blue-800 mb-2">🎯 Priority System</h3>
      <div class="grid grid-cols-1 md:grid-cols-5 gap-2 text-xs">
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">!</span>
          <span>80-100: Urgent</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">↑</span>
          <span>60-79: High</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold">=</span>
          <span>40-59: Normal</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">↓</span>
          <span>20-39: Low</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-gray-500 text-white flex items-center justify-center font-bold">...</span>
          <span>0-19: Deferred</span>
        </div>
      </div>
    </div>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Reference</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Priority</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Vehicle Details</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Arrived</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php $__empty_1 = true; $__currentLoopData = $factoryBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $factoryBooking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50 
              <?php if($factoryBooking->status === 'departed'): ?> bg-gray-50 
              <?php elseif($factoryBooking->status === 'completed'): ?> bg-green-50 
              <?php elseif($factoryBooking->status === 'processing'): ?> bg-blue-50 
              <?php endif; ?>">
              
              <td class="px-4 py-3">
                <div class="flex items-center">
                  <?php
                    $priorityColor = match(true) {
                      $factoryBooking->priority >= 80 => 'bg-red-500',
                      $factoryBooking->priority >= 60 => 'bg-orange-500',
                      $factoryBooking->priority >= 40 => 'bg-yellow-500',
                      $factoryBooking->priority >= 20 => 'bg-blue-500',
                      default => 'bg-gray-500'
                    };
                  ?>
                  <div class="w-3 h-3 <?php echo e($priorityColor); ?> rounded-full mr-2"></div>
                  <div>
                    <div class="font-mono text-sm font-semibold text-blue-600"><?php echo e($factoryBooking->reference); ?></div>
                    <div class="text-xs text-gray-500"><?php echo e($factoryBooking->depot->name); ?></div>
                  </div>
                </div>
              </td>
              
              <td class="px-4 py-3">
                <div class="text-center">
                  <span class="inline-flex items-center justify-center w-8 h-8 <?php echo e($priorityColor); ?> text-white rounded-full text-sm font-bold">
                    <?php echo e($factoryBooking->priority); ?>

                  </span>
                </div>
              </td>
              
              <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900"><?php echo e($factoryBooking->customer->name); ?></div>
                <?php if($factoryBooking->carrier): ?>
                  <div class="text-xs text-gray-500">via <?php echo e($factoryBooking->carrier->name); ?></div>
                <?php endif; ?>
              </td>
              
              <td class="px-4 py-3">
                <div class="text-sm">
                  <div class="font-medium">🚛 <?php echo e($factoryBooking->vehicle_registration); ?></div>
                  <?php if($factoryBooking->trailer_registration): ?>
                    <div class="text-xs text-gray-600">📦 <?php echo e($factoryBooking->trailer_registration); ?></div>
                  <?php endif; ?>
                  <?php if($factoryBooking->driver_name): ?>
                    <div class="text-xs text-gray-600">👤 <?php echo e($factoryBooking->driver_name); ?></div>
                  <?php endif; ?>
                </div>
              </td>
              
              <td class="px-4 py-3">
                <div class="text-sm">
                  <div><?php echo e($factoryBooking->arrived_at->format('M j, H:i')); ?></div>
                  <div class="text-xs text-gray-500"><?php echo e($factoryBooking->getTimeOnSite()); ?> on site</div>
                </div>
              </td>
              
              <td class="px-4 py-3">
                <div>
                  <?php echo $factoryBooking->tipping_status_badge; ?>

                  <?php if($factoryBooking->processing_started_at && $factoryBooking->status === 'processing'): ?>
                    <div class="text-xs text-gray-500 mt-1">
                      Started <?php echo e($factoryBooking->processing_started_at->diffForHumans()); ?>

                    </div>
                  <?php endif; ?>
                </div>
              </td>
              
              <td class="px-4 py-3">
                <div class="flex flex-col space-y-1">
                  <a href="<?php echo e(route('app.factory-bookings.show', $factoryBooking)); ?>" 
                     class="text-sm text-blue-600 hover:text-blue-800">
                    View Details
                  </a>
                  <?php if($factoryBooking->status === 'arrived'): ?>
                    <form method="POST" action="<?php echo e(route('app.factory-bookings.start-processing', $factoryBooking)); ?>" class="inline">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="text-sm text-green-600 hover:text-green-800">
                        Start Processing
                      </button>
                    </form>
                  <?php endif; ?>
                  <?php if(in_array($factoryBooking->status, ['arrived', 'processing'])): ?>
                    <a href="<?php echo e(route('app.factory-bookings.workflow.show', $factoryBooking)); ?>" 
                       class="text-sm text-orange-600 hover:text-orange-800">
                      🚛 Manage Workflow
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <div class="text-6xl mb-4">📋</div>
                  <div class="text-lg font-medium mb-2">No Factory Bookings Found</div>
                  <div class="text-sm text-gray-400 mb-4">
                    <?php if(request()->hasAny(['search', 'depot_id', 'status'])): ?>
                      Try adjusting your filters or
                      <a href="<?php echo e(route('app.factory-bookings.index')); ?>" class="text-blue-600 hover:text-blue-800">clear all filters</a>
                    <?php else: ?>
                      Register the first factory delivery to get started
                    <?php endif; ?>
                  </div>
                  <a href="<?php echo e(route('app.factory-bookings.create')); ?>" 
                     class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Register Factory Delivery
                  </a>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      
      <?php if($factoryBookings->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-200">
          <?php echo e($factoryBookings->links()); ?>

        </div>
      <?php endif; ?>
    </div>
    
    <?php if($factoryBookings->count() > 0): ?>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Total Factory Bookings</div>
          <div class="text-2xl font-bold text-gray-900"><?php echo e($factoryBookings->total()); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Currently On Site</div>
          <div class="text-2xl font-bold text-blue-600">
            <?php echo e($factoryBookings->where('status', 'arrived')->count() + $factoryBookings->where('status', 'processing')->count()); ?>

          </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Processing</div>
          <div class="text-2xl font-bold text-orange-600"><?php echo e($factoryBookings->where('status', 'processing')->count()); ?></div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Completed Today</div>
          <div class="text-2xl font-bold text-green-600"><?php echo e($factoryBookings->where('status', 'completed')->count()); ?></div>
        </div>
      </div>
    <?php endif; ?>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-bookings/index.blade.php ENDPATH**/ ?>