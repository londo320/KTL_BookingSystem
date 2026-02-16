
<?php
    $depotId = null;
    if ($booking->exists && $booking->slot) {
        $depotId = $booking->slot->depot_id;
    } elseif (old('slot_id')) {
        $depotId = \App\Models\Slot::find(old('slot_id'))?->depot_id;
    }

    $customerId = old('customer_id', $booking->customer_id);

    $config = \App\Models\CustomerBookingConfig::getConfig($customerId, $depotId);
    $showSkuFields = $config['sku_fields_enabled'];
    $requirePoData = $config['require_po_data'];
?>


<div class="space-y-4" data-show-sku="<?php echo e($showSkuFields ? 'true' : 'false'); ?>">
  
  <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
    <h3 class="text-sm font-semibold text-blue-900 mb-3">📋 Required Information</h3>
    <div class="grid grid-cols-3 gap-4">
      
      <?php if(auth()->user()->hasRole('admin') || auth()->user()->hasFunction('customers.view') || auth()->user()->hasFunction('bookings.create') || request()->routeIs('app.*')): ?>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Customer <span class="text-red-500">*</span></label>
          <select name="customer_id" required class="block w-full border-gray-300 rounded bg-white text-sm py-2">
            <option value="">– Choose customer –</option>
            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($customer->id); ?>" <?php if(old('customer_id', $booking->customer_id) == $customer->id): echo 'selected'; endif; ?>>
                <?php echo e($customer->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      <?php endif; ?>

      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Type <span class="text-red-500">*</span></label>
        <select name="booking_type_id" required class="block w-full border-gray-300 rounded bg-white text-sm py-2">
          <option value="">– Choose type –</option>
          <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type->id); ?>" <?php if(old('booking_type_id', $booking->booking_type_id) == $type->id): echo 'selected'; endif; ?>>
              <?php echo e($type->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['booking_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Slot <span class="text-red-500">*</span>
          <?php if($booking->exists): ?>
            <span class="text-xs text-red-600 ml-1">⚠️ Use Rebook</span>
          <?php endif; ?>
        </label>
        <select name="slot_id" required <?php if($booking->exists): ?> disabled <?php endif; ?> class="block w-full border-gray-300 rounded text-sm py-2 <?php if($booking->exists): ?> bg-gray-100 text-gray-500 cursor-not-allowed <?php else: ?> bg-white <?php endif; ?>">
          <?php if($booking->exists && $booking->slot): ?>
            <option value="<?php echo e($booking->slot->id); ?>" selected>
              <?php echo e($booking->slot->depot->name); ?> - <?php echo e($booking->slot->start_at->format('D d-M H:i')); ?> → <?php echo e($booking->slot->end_at->format('H:i')); ?>

            </option>
          <?php else: ?>
            <option value="">– Select customer & booking type first –</option>
          <?php endif; ?>
        </select>
        <?php if($booking->exists && $booking->slot): ?>
          <input type="hidden" name="slot_id" value="<?php echo e($booking->slot->id); ?>">
        <?php endif; ?>
        <?php $__errorArgs = ['slot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    
    <div id="expected-bay-info" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded">
      <div class="text-xs font-medium text-green-800 mb-1">Expected Bay on Arrival:</div>
      <div id="expected-bay-display" class="text-sm font-semibold text-green-900"></div>
      <div class="text-[10px] text-green-700 mt-1">Based on customer priority and equipment requirements</div>
    </div>
  </div>

  
  <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
    <h3 class="text-sm font-semibold text-blue-900 mb-3">📦 Supplier & Contact</h3>
    <div class="grid grid-cols-3 gap-4">
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
        <div class="relative">
          <input type="text" id="admin-supplier-search" name="supplier_name"
                 value="<?php echo e(old('supplier_name', $booking->supplier?->name ?? $booking->supplier)); ?>"
                 placeholder="Search or type supplier..." autocomplete="off"
                 class="block w-full border-gray-300 rounded bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10 text-sm py-2">
          <input type="hidden" id="admin-supplier-id" name="supplier_id" value="<?php echo e(old('supplier_id', $booking->supplier_id)); ?>">
          <div id="admin-supplier-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2">
            <span id="admin-supplier-status" class="text-xs"></span>
          </div>
        </div>
        <div class="mt-1">
          <a href="<?php echo e(route('app.suppliers.index')); ?>" target="_blank" class="text-[10px] text-blue-600 hover:text-blue-800 underline">📦 Manage suppliers</a>
        </div>
        <?php $__errorArgs = ['supplier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['supplier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Name</label>
        <div class="relative">
          <input type="text" id="admin-contact-name-input" name="contact_name"
                 value="<?php echo e(old('contact_name', $booking->contact_name)); ?>"
                 placeholder="Search or type contact..." autocomplete="off"
                 class="block w-full border-gray-300 rounded pr-10 text-sm py-2">
          <div id="admin-contact-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2">
            <span id="admin-contact-status" class="text-xs"></span>
          </div>
        </div>
        <?php $__errorArgs = ['contact_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
        <input type="text" id="admin-contact-phone-input" name="contact_phone"
               value="<?php echo e(old('contact_phone', $booking->contact_phone)); ?>"
               placeholder="e.g., 07123456789"
               class="block w-full border-gray-300 rounded text-sm py-2">
        <?php $__errorArgs = ['contact_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
  </div>

  
  <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">🚛 Haulier & Vehicle Details</h3>

    
    <div class="grid grid-cols-2 gap-4 mb-4">
      
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Haulier <span class="text-red-500">*</span></label>
        <div class="relative">
          <input type="text" id="admin-carrier-search" name="carrier_name"
                 value="<?php echo e(old('carrier_name', $booking->carrier?->name ?? $booking->carrier_company)); ?>"
                 placeholder="Search or type haulier..." required autocomplete="off"
                 class="block w-full border-gray-300 rounded bg-white focus:ring-2 focus:ring-gray-500 focus:border-gray-500 pr-10 text-sm py-2">
          <input type="hidden" id="admin-carrier-id" name="carrier_id" value="<?php echo e(old('carrier_id', $booking->carrier_id)); ?>">
          <div id="admin-carrier-dropdown" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
          <div class="absolute inset-y-0 right-0 flex items-center pr-2">
            <span id="admin-carrier-status" class="text-xs"></span>
          </div>
        </div>
        <div class="mt-1">
          <a href="<?php echo e(route('app.carriers.create')); ?>" target="_blank" class="text-[10px] text-gray-600 hover:text-gray-800 underline">🚚 Manage hauliers</a>
        </div>
        <?php $__errorArgs = ['carrier_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php $__errorArgs = ['carrier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Trailer Type</label>
        <select name="trailer_type_id" class="block w-full border-gray-300 rounded text-sm py-2">
          <option value="">– Select –</option>
          <?php $__currentLoopData = $trailerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailerType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($trailerType->id); ?>" <?php if(old('trailer_type_id', $booking->trailer_type_id) == $trailerType->id): echo 'selected'; endif; ?>>
              <?php echo e($trailerType->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['trailer_type_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    
    <div class="grid grid-cols-3 gap-4">
      
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Vehicle Registration</label>
        <input type="text" name="vehicle_registration"
               value="<?php echo e(old('vehicle_registration', $booking->vehicle_registration)); ?>"
               placeholder="e.g., AB12 CDE"
               class="block w-full border-gray-300 rounded text-sm py-2">
        <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Container/Trailer Number</label>
        <input type="text" name="container_number"
               value="<?php echo e(old('container_number', $booking->container_number)); ?>"
               placeholder="e.g., CONT123456"
               class="block w-full border-gray-300 rounded text-sm py-2">
        <?php $__errorArgs = ['container_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Seal Number</label>
        <input type="text" name="seal_number"
               value="<?php echo e(old('seal_number', $booking->seal_number)); ?>"
               placeholder="e.g., SEAL123456"
               class="block w-full border-gray-300 rounded text-sm py-2">
        <?php $__errorArgs = ['seal_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
  </div>

  
  <div class="bg-purple-50 rounded-lg border border-purple-200 p-4">
    <h3 class="text-sm font-semibold text-purple-900 mb-3">📦 Tipping Details</h3>
    <div class="grid grid-cols-2 gap-4">
      
      <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Tipping Type</label>
        <div class="flex items-center gap-6">
          <div class="flex items-center">
            <input type="radio" id="tipping_type_live" name="tipping_type" value="live_tip"
                   <?php if(old('tipping_type', $booking->tipping_type) == 'live_tip'): echo 'checked'; endif; ?>
                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
            <label for="tipping_type_live" class="ml-2 flex items-center">
              <span class="text-base mr-2">🚛📦</span>
              <span class="text-sm font-medium text-gray-900">Live Tip</span>
            </label>
          </div>
          <div class="flex items-center">
            <input type="radio" id="tipping_type_drop" name="tipping_type" value="drop"
                   <?php if(old('tipping_type', $booking->tipping_type) == 'drop'): echo 'checked'; endif; ?>
                   class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300">
            <label for="tipping_type_drop" class="ml-2 flex items-center">
              <span class="text-base mr-2">📦</span>
              <span class="text-sm font-medium text-gray-900">Drop</span>
            </label>
          </div>
        </div>
        <?php $__errorArgs = ['tipping_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    
    <?php if($booking->exists): ?>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-600 mb-1">Tipping Bay</label>
        <select name="tipping_bay_id" class="block w-full border-gray-300 rounded text-sm py-2 max-w-md">
          <option value="">– Select Bay –</option>
          <?php if(isset($tippingBays)): ?>
            <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($bay->id); ?>"
                      <?php if(old('tipping_bay_id', $booking->tipping_bay_id) == $bay->id): echo 'selected'; endif; ?>
                      <?php if($bay->is_occupied && $bay->id != $booking->tipping_bay_id): echo 'disabled'; endif; ?>>
                <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>)
                <?php if($bay->is_occupied && $bay->id != $booking->tipping_bay_id): ?>
                  - Occupied
                <?php elseif($bay->is_occupied): ?>
                  - Current
                <?php else: ?>
                  - Available
                <?php endif; ?>
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
        </select>
        <?php $__errorArgs = ['tipping_bay_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    <?php endif; ?>
  </div>

  
  <div id="po-section-container" <?php if(!$booking->exists): ?> style="display: none;" <?php endif; ?> data-show-sku="<?php echo e($showSkuFields ? 'true' : 'false'); ?>">
    <?php if($booking->exists): ?>
      
      <div class="bg-green-50 p-4 rounded-lg border border-green-200">
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-base font-semibold text-green-900">📦 PO Numbers & Expected Quantities <span class="text-red-500">*</span></h3>
        <div class="flex gap-2">
          <button type="button" onclick="document.getElementById('manual-entry-section').classList.toggle('hidden')" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-medium">
            ✏️ Manual Entry
          </button>
          <a href="<?php echo e(route('app.bookings.download-csv-template', $booking)); ?>" class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
            📥 Download CSV Template
          </a>
          <button type="button" id="upload-csv-btn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
            📤 Upload CSV Template
          </button>
          <a href="<?php echo e(route('app.products.index')); ?>" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
            📦 Manage Products
          </a>
        </div>
      </div>

      
      <div id="manual-entry-section" class="hidden mb-4 p-4 bg-indigo-50 rounded-lg border-2 border-indigo-400">
        <h4 class="text-sm font-semibold text-indigo-900 mb-2">✏️ Manual PO Entry Active</h4>
        <div class="space-y-2 text-sm text-indigo-800">
          <p><strong>The manual entry form is displayed below.</strong> To add PO details:</p>
          <ol class="list-decimal list-inside ml-2 space-y-1">
            <li>Scroll down to the "PO Numbers & Lines" section</li>
            <li>Click <span class="inline-block px-2 py-0.5 bg-blue-500 text-white rounded text-xs">+ Add PO Number</span> to add a new PO</li>
            <li>Fill in the PO Number, then click <span class="inline-block px-2 py-0.5 bg-blue-600 text-white rounded text-xs">+ Add Line</span></li>
            <li>For each line enter:
              <ul class="list-disc list-inside ml-6 mt-1">
                <li><strong>SKU</strong> - Start typing to search existing products</li>
                <li><strong>Expected:</strong> Cases, Pallets, Pallet Type</li>
                <li><strong>Actual:</strong> Cases, Pallets (filled when goods arrive)</li>
              </ul>
            </li>
            <li>Click <span class="inline-block px-2 py-0.5 bg-blue-600 text-white rounded text-xs">+ Add Line</span> to add more products to the same PO</li>
            <li>Click <span class="inline-block px-2 py-0.5 bg-blue-500 text-white rounded text-xs">+ Add PO Number</span> to add additional POs</li>
          </ol>
          <div class="mt-3 p-2 bg-white rounded border border-indigo-200">
            <p class="text-xs text-indigo-700">
              💡 <strong>Tip:</strong> Products must exist before you can select them. Use the <strong>"📦 Manage Products"</strong> button above to add new products if needed.
            </p>
          </div>
        </div>
      </div>

      
      <div id="csv-upload-section" class="hidden mb-4 p-4 bg-white rounded-lg border border-green-300">
        <h4 class="text-sm font-semibold text-gray-800 mb-2">Upload CSV Template</h4>
        <p class="text-xs text-gray-600 mb-3">
          <strong>Tip:</strong> You can use ONE CSV file for multiple bookings! Just use different Booking References.<br>
          CSV format: Customer ID, Booking Reference, PO Number, SKU, Product Description, Expected Cases, Expected Pallets
        </p>
        <form id="csv-upload-form" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="booking_id" value="<?php echo e($booking->id); ?>">
          <input type="file" name="csv_file" id="csv-file-input" accept=".csv" class="block w-full text-sm text-gray-900 border border-gray-300 rounded cursor-pointer bg-gray-50 mb-2">

          
          <div id="reference-selector" class="hidden mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Booking Reference to Import:</label>
            <select name="booking_reference" id="booking-reference-select" class="block w-full border-gray-300 rounded text-sm py-2 mb-2">
              <option value="">-- All References (Import Everything) --</option>
            </select>
            <div id="reference-info" class="text-xs text-gray-600"></div>
          </div>

          <div class="flex gap-2">
            <button type="submit" id="upload-submit-btn" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
              Upload & Process
            </button>
            <button type="button" id="cancel-upload-btn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
              Cancel
            </button>
          </div>
        </form>
        <div id="csv-upload-result" class="mt-3 hidden"></div>
      </div>

      <?php if (isset($component)) { $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $booking,'hideActuals' => !$booking->exists,'customerId' => old('customer_id', $booking->customer_id),'showSkuFields' => $showSkuFields]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking),'hide_actuals' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!$booking->exists),'customer_id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('customer_id', $booking->customer_id)),'show_sku_fields' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showSkuFields)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $attributes = $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $component = $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>

      <?php $__errorArgs = ['po_numbers'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>
    <?php else: ?>
      
      <div id="po-section-content" style="display: none;">
        <div id="po-section-header" class="p-4 rounded-lg border mb-4">
          <h3 id="po-section-title" class="text-base font-semibold mb-2"></h3>
          <p id="po-section-message" class="text-sm"></p>
        </div>

        <?php if (isset($component)) { $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.booking-po-numbers','data' => ['booking' => $booking,'hideActuals' => true,'customerId' => old('customer_id', $booking->customer_id),'showSkuFields' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('booking-po-numbers'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['booking' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($booking),'hide_actuals' => true,'customer_id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('customer_id', $booking->customer_id)),'show_sku_fields' => true]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $attributes = $__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__attributesOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d)): ?>
<?php $component = $__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d; ?>
<?php unset($__componentOriginal9295010a4cc8ee6f1ca21fe0662a366d); ?>
<?php endif; ?>

        <?php $__errorArgs = ['po_numbers'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm mt-2"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    <?php endif; ?>
  </div>

  
  <?php if(!$booking->exists): ?>
  <div id="po-section-placeholder" class="bg-gray-50 p-4 rounded-lg border border-gray-200">
    <h3 class="text-base font-semibold text-gray-700 mb-2">📦 Product Details</h3>
    <p class="text-sm text-gray-600">
      ℹ️ Please select a customer and slot to see PO/Product requirements.
    </p>
  </div>
  <?php endif; ?>

  
  <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
    <h3 class="text-base font-semibold text-yellow-900 mb-3">📝 Notes & Instructions</h3>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      
      <div>
        <label class="block text-sm font-medium text-yellow-800 mb-1">General Notes</label>
        <textarea name="notes" rows="3"
                  placeholder="Internal notes about this booking..."
                  class="block w-full border-yellow-300 rounded bg-white text-sm py-2"><?php echo e(old('notes', $booking->notes)); ?></textarea>
        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div>
        <label class="block text-sm font-medium text-yellow-800 mb-1">Special Instructions</label>
        <textarea name="special_instructions" rows="3"
                  placeholder="Special handling instructions..."
                  class="block w-full border-yellow-300 rounded bg-white text-sm py-2"><?php echo e(old('special_instructions', $booking->special_instructions)); ?></textarea>
        <?php $__errorArgs = ['special_instructions'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-xs"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>
  </div>

  
  <?php if($booking->exists && $booking->arrived_at): ?>
    <div class="bg-green-100 p-4 rounded-lg border border-green-300">
      <h3 class="text-base font-semibold text-green-900 mb-2">✅ Arrival Status</h3>
      <p class="text-sm text-green-800">
        <strong>Vehicle Arrived:</strong> <?php echo e($booking->arrived_at->format('d-M-Y H:i:s')); ?>

        <?php if($booking->departed_at): ?>
          <br><strong>Departed:</strong> <?php echo e($booking->departed_at->format('d-M-Y H:i:s')); ?>

        <?php endif; ?>
      </p>
    </div>
  <?php endif; ?>
</div>

<?php echo $__env->make('admin.bookings._form_scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php if($booking->exists): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.getElementById('upload-csv-btn');
    const cancelBtn = document.getElementById('cancel-upload-btn');
    const uploadSection = document.getElementById('csv-upload-section');
    const uploadForm = document.getElementById('csv-upload-form');
    const uploadResult = document.getElementById('csv-upload-result');
    const fileInput = document.getElementById('csv-file-input');
    const referenceSelector = document.getElementById('reference-selector');
    const referenceSelect = document.getElementById('booking-reference-select');
    const referenceInfo = document.getElementById('reference-info');

    let csvReferences = [];

    if (uploadBtn) {
        uploadBtn.addEventListener('click', function() {
            uploadSection.classList.remove('hidden');
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            uploadSection.classList.add('hidden');
            uploadForm.reset();
            uploadResult.classList.add('hidden');
            referenceSelector.classList.add('hidden');
        });
    }

    // Preview CSV when file is selected
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (!this.files.length) {
                referenceSelector.classList.add('hidden');
                return;
            }

            const formData = new FormData();
            formData.append('csv_file', this.files[0]);
            formData.append('booking_id', '<?php echo e($booking->id); ?>');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            uploadResult.className = 'mt-3 p-3 bg-blue-100 border border-blue-300 rounded text-sm text-blue-800';
            uploadResult.textContent = '⏳ Reading CSV file...';
            uploadResult.classList.remove('hidden');

            fetch('<?php echo e(route("app.bookings.preview-csv")); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    csvReferences = data.references;

                    // Populate dropdown
                    referenceSelect.innerHTML = '<option value="">-- All References (Import Everything) --</option>';

                    data.references.forEach(ref => {
                        const option = document.createElement('option');
                        option.value = ref.reference;
                        option.textContent = `${ref.reference} (${ref.row_count} items, ${ref.po_numbers.length} POs)`;
                        referenceSelect.appendChild(option);
                    });

                    if (data.references.length > 0) {
                        referenceSelector.classList.remove('hidden');
                        uploadResult.className = 'mt-3 p-3 bg-green-100 border border-green-300 rounded text-sm text-green-800';
                        uploadResult.textContent = `✅ Found ${data.references.length} booking reference(s) in your CSV`;
                    } else {
                        uploadResult.className = 'mt-3 p-3 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800';
                        uploadResult.textContent = '⚠️ No booking references found. All rows will be imported.';
                    }
                } else {
                    uploadResult.className = 'mt-3 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
                    uploadResult.textContent = `❌ Error: ${data.message}`;
                }
            })
            .catch(error => {
                console.error('Preview error:', error);
                uploadResult.className = 'mt-3 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
                uploadResult.textContent = '❌ Failed to read CSV file.';
            });
        });
    }

    // Update info when reference is selected
    if (referenceSelect) {
        referenceSelect.addEventListener('change', function() {
            const selectedRef = csvReferences.find(r => r.reference === this.value);
            if (selectedRef) {
                referenceInfo.textContent = `Will import ${selectedRef.row_count} items from PO(s): ${selectedRef.po_numbers.join(', ')}`;
            } else {
                referenceInfo.textContent = 'Will import all references from the CSV file';
            }
        });
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(uploadForm);

            if (!fileInput.files.length) {
                uploadResult.className = 'mt-3 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
                uploadResult.textContent = '⚠️ Please select a CSV file to upload.';
                uploadResult.classList.remove('hidden');
                return;
            }

            // Show loading state
            uploadResult.className = 'mt-3 p-3 bg-blue-100 border border-blue-300 rounded text-sm text-blue-800';
            uploadResult.textContent = '⏳ Processing CSV file...';
            uploadResult.classList.remove('hidden');

            fetch('<?php echo e(route("app.bookings.upload-csv")); ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    uploadResult.className = 'mt-3 p-3 bg-green-100 border border-green-300 rounded text-sm text-green-800';
                    uploadResult.innerHTML = `
                        <strong>✅ Success!</strong><br>
                        Processed ${data.rows_processed} rows<br>
                        ${data.products_added} products added<br>
                        ${data.errors?.length ? '<br><strong>Warnings:</strong><br>' + data.errors.join('<br>') : ''}
                    `;

                    // Reload page after 2 seconds to show updated PO data
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    uploadResult.className = 'mt-3 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
                    uploadResult.innerHTML = `<strong>❌ Error:</strong><br>${data.message || 'Failed to process CSV'}`;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                uploadResult.className = 'mt-3 p-3 bg-red-100 border border-red-300 rounded text-sm text-red-800';
                uploadResult.textContent = '❌ An error occurred while uploading the file.';
            });
        });
    }
});
</script>
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/_form.blade.php ENDPATH**/ ?>