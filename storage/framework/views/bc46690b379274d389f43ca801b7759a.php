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
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">FAC</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Factory Delivery</h1>
              <p class="text-sm text-gray-600">Ad-hoc Arrival Management</p>
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
          <a href="<?php echo e(route('app.factory-bookings.index')); ?>"
             class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
            ← Factory Bookings
          </a>
          <a href="<?php echo e(route('app.bookings.index')); ?>"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📋 Scheduled Bookings
          </a>
        </div>
        
        <?php if(!in_array($factoryBooking->status, ['departed'])): ?>
          <div class="flex items-center space-x-2 bg-orange-50 p-2 rounded-lg border border-orange-200">
            <span class="text-xs font-medium text-orange-700 uppercase">Operations</span>
            <?php if($factoryBooking->status === 'arrived'): ?>
              <form method="POST" action="<?php echo e(route('app.factory-bookings.start-processing', $factoryBooking)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                  ▶️ Start Processing
                </button>
              </form>
            <?php endif; ?>
            <?php if(in_array($factoryBooking->status, ['arrived', 'processing'])): ?>
              <a href="<?php echo e(route('app.factory-booking-workflow.show', $factoryBooking)); ?>"
                 class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                🚛 Manage Workflow
              </a>
            <?php endif; ?>
            
            <?php
              $movement = $factoryBooking->movements->last();
              $isOnSite = $movement && !in_array($movement->current_status, ['departed']);
            ?>
            <?php if($isOnSite): ?>
              <button onclick="showMovementModal()" 
                      class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
                🚚 Move Trailer
              </button>
            <?php endif; ?>
            <a href="<?php echo e(route('app.factory-bookings.history', $factoryBooking)); ?>"
               class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
              📋 History
            </a>
            <?php if(in_array($factoryBooking->status, ['processing', 'arrived'])): ?>
              <form method="POST" action="<?php echo e(route('app.factory-bookings.complete', $factoryBooking)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                  ✅ Mark Complete
                </button>
              </form>
            <?php endif; ?>
            <?php if($factoryBooking->status === 'completed'): ?>
              <form method="POST" action="<?php echo e(route('app.factory-bookings.mark-departed', $factoryBooking)); ?>" class="inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition-colors">
                  🏁 Mark Departed
                </button>
              </form>
            <?php endif; ?>
            <a href="<?php echo e(route('app.factory-bookings.edit', $factoryBooking)); ?>"
               class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
              ✏️ Edit
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-7xl mx-auto px-4">
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
    
    <?php
      $movement = $factoryBooking->movements->last();
    ?>
    
    <div class="mb-6 p-4 
      <?php if($factoryBooking->status === 'departed'): ?> bg-gray-100 border border-gray-300 
      <?php elseif($factoryBooking->status === 'completed'): ?> bg-green-100 border border-green-300 
      <?php elseif($factoryBooking->status === 'processing'): ?> bg-blue-100 border border-blue-300 
      <?php else: ?> bg-orange-100 border border-orange-300 
      <?php endif; ?> rounded-lg">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <?php if($factoryBooking->status === 'departed'): ?>
            <span class="text-gray-600 text-2xl mr-3">🏁</span>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Vehicle Departed</h3>
              <p class="text-gray-700">Departed: <?php echo e($factoryBooking->departed_at->format('d M Y, H:i')); ?></p>
            </div>
          <?php elseif($factoryBooking->status === 'completed'): ?>
            <span class="text-green-600 text-2xl mr-3">✅</span>
            <div>
              <h3 class="text-lg font-semibold text-green-800">Delivery Completed</h3>
              <p class="text-green-700">Completed: <?php echo e($factoryBooking->completed_at->format('d M Y, H:i')); ?></p>
            </div>
          <?php elseif($factoryBooking->status === 'processing'): ?>
            <span class="text-blue-600 text-2xl mr-3">⚡</span>
            <div>
              <h3 class="text-lg font-semibold text-blue-800">Currently Processing</h3>
              <p class="text-blue-700">
                Started: <?php echo e($factoryBooking->processing_started_at->format('d M Y, H:i')); ?>

                (<?php echo e($factoryBooking->processing_started_at->diffForHumans()); ?>)
              </p>
            </div>
          <?php else: ?>
            <span class="text-orange-600 text-2xl mr-3">📋</span>
            <div>
              <h3 class="text-lg font-semibold text-orange-800">Awaiting Processing</h3>
              <p class="text-orange-700">
                Arrived: <?php echo e($factoryBooking->arrived_at->format('d M Y, H:i')); ?>

                (<?php echo e($factoryBooking->getTimeOnSite()); ?> on site)
              </p>
            </div>
          <?php endif; ?>
        </div>
        
        <div class="text-right">
          <?php
            $priorityColor = match(true) {
              $factoryBooking->priority >= 80 => 'bg-red-500',
              $factoryBooking->priority >= 60 => 'bg-orange-500',
              $factoryBooking->priority >= 40 => 'bg-yellow-500',
              $factoryBooking->priority >= 20 => 'bg-blue-500',
              default => 'bg-gray-500'
            };
            $priorityLabel = match(true) {
              $factoryBooking->priority >= 80 => 'URGENT',
              $factoryBooking->priority >= 60 => 'HIGH',
              $factoryBooking->priority >= 40 => 'NORMAL',
              $factoryBooking->priority >= 20 => 'LOW',
              default => 'DEFERRED'
            };
          ?>
          <div class="text-sm text-gray-600 mb-1">Priority</div>
          <div class="inline-flex items-center <?php echo e($priorityColor); ?> text-white px-3 py-1 rounded-full">
            <span class="text-lg font-bold mr-2"><?php echo e($factoryBooking->priority); ?></span>
            <span class="text-xs font-medium"><?php echo e($priorityLabel); ?></span>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      
      <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Delivery Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Reference</label>
              <div class="mt-1 text-sm text-gray-900 font-mono"><?php echo e($factoryBooking->reference); ?></div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Depot</label>
              <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->depot->name); ?></div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Customer</label>
              <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->customer->name); ?></div>
            </div>
            <?php if($factoryBooking->carrier): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700">Carrier</label>
                <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->carrier->name); ?></div>
              </div>
            <?php endif; ?>
            <div>
              <label class="block text-sm font-medium text-gray-700">Arrived</label>
              <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->arrived_at->format('d M Y, H:i')); ?></div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Time on Site</label>
              <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->getTimeOnSite()); ?></div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">🚛 Vehicle Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Vehicle Registration</label>
              <div class="mt-1 text-sm text-gray-900 font-mono"><?php echo e($factoryBooking->vehicle_registration); ?></div>
            </div>
            <?php if($factoryBooking->trailer_registration): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trailer Registration</label>
                <div class="mt-1 text-sm text-gray-900 font-mono"><?php echo e($factoryBooking->trailer_registration); ?></div>
              </div>
            <?php endif; ?>
            <?php if($factoryBooking->trailerType): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700">Trailer Type</label>
                <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->trailerType->name); ?></div>
              </div>
            <?php endif; ?>
            <?php if($factoryBooking->driver_name): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700">Driver Name</label>
                <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->driver_name); ?></div>
              </div>
            <?php endif; ?>
            <?php if($factoryBooking->driver_phone): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700">Driver Phone</label>
                <div class="mt-1 text-sm text-gray-900"><?php echo e($factoryBooking->driver_phone); ?></div>
              </div>
            <?php endif; ?>
          </div>
        </div>
        
        <?php if($factoryBooking->delivery_notes || $factoryBooking->gate_notes): ?>
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes</h3>
            <?php if($factoryBooking->delivery_notes): ?>
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes</label>
                <div class="bg-gray-50 rounded-md p-3 text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($factoryBooking->delivery_notes); ?></div>
              </div>
            <?php endif; ?>
            <?php if($factoryBooking->gate_notes): ?>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gate Staff Notes</label>
                <div class="bg-blue-50 rounded-md p-3 text-sm text-gray-900 whitespace-pre-wrap"><?php echo e($factoryBooking->gate_notes); ?></div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">📦 PO Numbers</h3>
            <?php if($factoryBooking->poNumbers->count() === 0): ?>
              <button type="button" onclick="showAddPoModal()" class="text-sm text-blue-600 hover:text-blue-800">+ Add PO Numbers</button>
            <?php else: ?>
              <button type="button" onclick="showAddPoModal()" class="text-sm text-blue-600 hover:text-blue-800">+ Add More POs</button>
            <?php endif; ?>
          </div>
          <?php if($factoryBooking->poNumbers->count() > 0): ?>
            <div class="space-y-3">
              <?php $__currentLoopData = $factoryBooking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                  <div>
                    <div class="font-medium"><?php echo e($po->po_number); ?></div>
                    <?php if($po->description): ?>
                      <div class="text-sm text-gray-600"><?php echo e($po->description); ?></div>
                    <?php endif; ?>
                  </div>
                  <div class="text-right text-sm">
                    <?php if($po->expected_cases > 0): ?>
                      <div>Cases: <?php echo e($po->expected_cases); ?></div>
                    <?php endif; ?>
                    <?php if($po->expected_pallets > 0): ?>
                      <div>Pallets: <?php echo e($po->expected_pallets); ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          <?php else: ?>
            <div class="text-center py-8 text-gray-500">
              <div class="text-4xl mb-2">📦</div>
              <div class="text-sm">No PO numbers added yet</div>
              <div class="text-xs text-gray-400 mt-1">PO numbers can be added once delivery details are confirmed</div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="space-y-6">
        
        <?php if(in_array($factoryBooking->status, ['arrived', 'processing'])): ?>
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h4 class="font-medium text-gray-800 mb-2 flex items-center">
              <span class="mr-2">🚛</span>
              Tipping Operations
            </h4>
            <div class="text-sm text-gray-600 mb-3">
              Factory deliveries use the same tipping workflow as scheduled bookings.
            </div>
            <a href="<?php echo e(route('app.factory-booking-workflow.show', $factoryBooking)); ?>" 
               class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors w-full justify-center">
              🚛 Manage Tipping Workflow
            </a>
          </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h4 class="font-medium text-gray-800 mb-3">👤 Registration Details</h4>
          <div class="space-y-3 text-sm">
            <div>
              <label class="block text-gray-700 font-medium">Registered By</label>
              <div class="text-gray-900"><?php echo e($factoryBooking->registeredBy->name); ?></div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium">Registration Time</label>
              <div class="text-gray-900"><?php echo e($factoryBooking->created_at->format('d M Y, H:i')); ?></div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium">Last Updated</label>
              <div class="text-gray-900"><?php echo e($factoryBooking->updated_at->format('d M Y, H:i')); ?></div>
            </div>
          </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h4 class="font-medium text-gray-800 mb-3">📊 Status Timeline</h4>
          <div class="space-y-3">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
              <div class="text-sm">
                <div class="font-medium">Arrived</div>
                <div class="text-gray-500"><?php echo e($factoryBooking->arrived_at->format('M j, H:i')); ?></div>
              </div>
            </div>
            <?php if($factoryBooking->processing_started_at): ?>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                <div class="text-sm">
                  <div class="font-medium">Processing Started</div>
                  <div class="text-gray-500"><?php echo e($factoryBooking->processing_started_at->format('M j, H:i')); ?></div>
                </div>
              </div>
            <?php endif; ?>
            <?php if($factoryBooking->completed_at): ?>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                <div class="text-sm">
                  <div class="font-medium">Completed</div>
                  <div class="text-gray-500"><?php echo e($factoryBooking->completed_at->format('M j, H:i')); ?></div>
                </div>
              </div>
            <?php endif; ?>
            <?php if($factoryBooking->departed_at): ?>
              <div class="flex items-center">
                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                <div class="text-sm">
                  <div class="font-medium">Departed</div>
                  <div class="text-gray-500"><?php echo e($factoryBooking->departed_at->format('M j, H:i')); ?></div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
        
        <?php if(!in_array($factoryBooking->status, ['departed'])): ?>
          <div class="bg-orange-50 rounded-lg border border-orange-200 p-4">
            <h4 class="font-medium text-orange-800 mb-3">⚡ Quick Actions</h4>
            <div class="space-y-2">
              <?php if($factoryBooking->status === 'arrived'): ?>
                <form method="POST" action="<?php echo e(route('app.factory-bookings.start-processing', $factoryBooking)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    ▶️ Start Processing
                  </button>
                </form>
              <?php endif; ?>
              <?php if(in_array($factoryBooking->status, ['processing', 'arrived'])): ?>
                <form method="POST" action="<?php echo e(route('app.factory-bookings.complete', $factoryBooking)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    ✅ Mark Complete
                  </button>
                </form>
              <?php endif; ?>
              <?php if($factoryBooking->status === 'completed'): ?>
                <form method="POST" action="<?php echo e(route('app.factory-bookings.mark-departed', $factoryBooking)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="w-full px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                    🏁 Mark Departed
                  </button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Add PO Numbers Modal -->
  <div id="addPoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
      <div class="bg-white rounded-lg p-6 w-96 max-w-md">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add PO Numbers</h3>
        <form id="addPoForm" method="POST" action="<?php echo e(route('app.factory-bookings.add-po-numbers', $factoryBooking)); ?>">
          <?php echo csrf_field(); ?>
          <div id="poInputs">
            <div class="po-input-group mb-3">
              <label class="block text-sm font-medium text-gray-700 mb-1">PO Number</label>
              <div class="flex">
                <input type="text" name="po_numbers[]" class="flex-1 border border-gray-300 rounded-l-md px-3 py-2" placeholder="Enter PO number" required>
                <button type="button" onclick="removePoInput(this)" class="px-3 py-2 bg-red-500 text-white rounded-r-md hover:bg-red-600 text-sm">×</button>
              </div>
            </div>
          </div>
          <button type="button" onclick="addPoInput()" class="text-sm text-blue-600 hover:text-blue-800 mb-4">+ Add Another PO</button>
          <div class="flex justify-end space-x-2">
            <button type="button" onclick="hideAddPoModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add PO Numbers</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function showAddPoModal() {
      document.getElementById('addPoModal').classList.remove('hidden');
    }

    function hideAddPoModal() {
      document.getElementById('addPoModal').classList.add('hidden');
    }

    function addPoInput() {
      const poInputs = document.getElementById('poInputs');
      const newInput = document.createElement('div');
      newInput.className = 'po-input-group mb-3';
      newInput.innerHTML = `
        <label class="block text-sm font-medium text-gray-700 mb-1">PO Number</label>
        <div class="flex">
          <input type="text" name="po_numbers[]" class="flex-1 border border-gray-300 rounded-l-md px-3 py-2" placeholder="Enter PO number" required>
          <button type="button" onclick="removePoInput(this)" class="px-3 py-2 bg-red-500 text-white rounded-r-md hover:bg-red-600 text-sm">×</button>
        </div>
      `;
      poInputs.appendChild(newInput);
    }

    function removePoInput(button) {
      const group = button.closest('.po-input-group');
      const poInputs = document.getElementById('poInputs');
      if (poInputs.children.length > 1) {
        group.remove();
      }
    }

    // Close modal when clicking outside
    document.getElementById('addPoModal').addEventListener('click', function(e) {
      if (e.target === this) {
        hideAddPoModal();
      }
    });
    // Trailer Movement Modal Functions
    function showMovementModal() {
      document.getElementById('movementModal').classList.remove('hidden');
    }

    function hideMovementModal() {
      document.getElementById('movementModal').classList.add('hidden');
    }

    function moveTrailer(action) {
      const form = document.getElementById('movementForm');
      const actionInput = document.getElementById('movementAction');
      const locationSelect = document.getElementById('locationSelect');
      const baySelect = document.getElementById('baySelect');
      const notesInput = document.getElementById('movementNotes');

      actionInput.value = action;
      
      if (action === 'move_to_location') {
        if (!locationSelect.value) {
          alert('Please select a location');
          return;
        }
      } else if (action === 'move_to_bay') {
        if (!baySelect.value) {
          alert('Please select a bay');
          return;
        }
      }
      
      form.submit();
    }

    // Close modal when clicking outside
    document.getElementById('movementModal').addEventListener('click', function(e) {
      if (e.target === this) {
        hideMovementModal();
      }
    });
  </script>

  
  <?php if($movement && !in_array($movement->current_status, ['departed'])): ?>
    <div id="movementModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold">🚚 Move Trailer</h3>
          <button onclick="hideMovementModal()" class="text-gray-400 hover:text-gray-600">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <?php if($movement->tippingLocation || $movement->tippingBay): ?>
          <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded">
            <h4 class="font-medium text-blue-800 text-sm">Current Position</h4>
            <?php if($movement->tippingLocation): ?>
              <p class="text-blue-700 text-sm">📍 <?php echo e($movement->tippingLocation->name); ?></p>
            <?php endif; ?>
            <?php if($movement->tippingBay): ?>
              <p class="text-blue-700 text-sm">🚛 <?php echo e($movement->tippingBay->name); ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <form id="movementForm" method="POST" action="<?php echo e(route('app.factory-booking-workflow.move-trailer', $factoryBooking)); ?>">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="movementAction" name="action" value="">
          
          
          <?php if($availableLocations->count() > 0): ?>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Move to Location</label>
              <select id="locationSelect" name="location_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">Select location...</option>
                <?php $__currentLoopData = $availableLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($location->id); ?>"><?php echo e($location->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <button type="button" onclick="moveTrailer('move_to_location')" 
                      class="w-full mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                📍 Move to Location
              </button>
            </div>
          <?php endif; ?>

          
          <?php if($availableBays->count() > 0): ?>
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Move to Bay</label>
              <select id="baySelect" name="bay_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                <option value="">Select bay...</option>
                <?php $__currentLoopData = $availableBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($bay->id); ?>"><?php echo e($bay->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <button type="button" onclick="moveTrailer('move_to_bay')" 
                      class="w-full mt-2 px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
                🚛 Move to Bay
              </button>
            </div>
          <?php endif; ?>

          
          <div class="mb-4">
            <button type="button" onclick="moveTrailer('move_to_collection')" 
                    class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
              📦 Move to Collection Zone
            </button>
          </div>

          
          <?php if($movement->current_status === 'empty' || $movement->unloading_completed_at): ?>
            <div class="mb-4 border-t pt-4">
              <button type="button" onclick="moveTrailer('depart')" 
                      class="w-full px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
                🏁 Record Departure (Frees Bay)
              </button>
            </div>
          <?php endif; ?>

          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Movement Notes</label>
            <textarea id="movementNotes" name="notes" rows="2" 
                      class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" 
                      placeholder="Optional notes about this movement..."></textarea>
          </div>
        </form>

        <div class="flex justify-end">
          <button onclick="hideMovementModal()" 
                  class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50">
            Cancel
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/factory-bookings/show.blade.php ENDPATH**/ ?>