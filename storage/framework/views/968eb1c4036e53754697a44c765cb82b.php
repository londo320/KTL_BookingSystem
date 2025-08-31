<?php $__env->startSection('title', 'Create Import Template'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="<?php echo e(route('outbound.imports.templates')); ?>" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Import Template</h1>
                    <p class="text-gray-600 mt-1">Configure field mappings and file format settings for a new WMS system</p>
                </div>
            </div>
        </div>

        <form method="POST" action="<?php echo e(route('outbound.imports.templates.store')); ?>" class="space-y-8">
            <?php echo csrf_field(); ?>

            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
                    <p class="text-sm text-gray-600">Provide details about this import template</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Template Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?php echo e(old('name')); ?>"
                               placeholder="e.g., SAP WMS Export Format"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="source_system" class="block text-sm font-medium text-gray-700 mb-2">Source System</label>
                        <input type="text" 
                               id="source_system" 
                               name="source_system" 
                               value="<?php echo e(old('source_system')); ?>"
                               placeholder="e.g., SAP WMS, Manhattan, Custom ERP"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['source_system'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               required>
                        <?php $__errorArgs = ['source_system'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="file_type" class="block text-sm font-medium text-gray-700 mb-2">File Type</label>
                        <select id="file_type" 
                                name="file_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['file_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                required
                                onchange="toggleFileTypeSettings()">
                            <option value="">Select file type...</option>
                            <option value="csv" <?php echo e(old('file_type') === 'csv' ? 'selected' : ''); ?>>CSV</option>
                            <option value="xlsx" <?php echo e(old('file_type') === 'xlsx' ? 'selected' : ''); ?>>Excel (XLSX)</option>
                            <option value="txt" <?php echo e(old('file_type') === 'txt' ? 'selected' : ''); ?>>Text (TXT)</option>
                            <option value="xml" <?php echo e(old('file_type') === 'xml' ? 'selected' : ''); ?>>XML</option>
                            <option value="json" <?php echo e(old('file_type') === 'json' ? 'selected' : ''); ?>>JSON</option>
                        </select>
                        <?php $__errorArgs = ['file_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Optional description of this template and when to use it..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('description')); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- File Format Settings -->
            <div class="bg-white rounded-lg shadow" id="file-format-settings">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">File Format Settings</h2>
                    <p class="text-sm text-gray-600">Configure how the file should be parsed</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div id="csv-settings">
                            <label for="delimiter" class="block text-sm font-medium text-gray-700 mb-2">Column Delimiter</label>
                            <select id="delimiter" 
                                    name="delimiter" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="," <?php echo e(old('delimiter') === ',' ? 'selected' : ''); ?>>Comma (,)</option>
                                <option value=";" <?php echo e(old('delimiter') === ';' ? 'selected' : ''); ?>>Semicolon (;)</option>
                                <option value="|" <?php echo e(old('delimiter') === '|' ? 'selected' : ''); ?>>Pipe (|)</option>
                                <option value="\t" <?php echo e(old('delimiter') === "\t" ? 'selected' : ''); ?>>Tab</option>
                            </select>
                        </div>

                        <div id="text-qualifier-settings">
                            <label for="text_qualifier" class="block text-sm font-medium text-gray-700 mb-2">Text Qualifier</label>
                            <select id="text_qualifier" 
                                    name="text_qualifier" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="\"" <?php echo e(old('text_qualifier') === '"' ? 'selected' : ''); ?>>Double Quote (")</option>
                                <option value="'" <?php echo e(old('text_qualifier') === "'" ? 'selected' : ''); ?>>Single Quote (')</option>
                                <option value="" <?php echo e(old('text_qualifier') === '' ? 'selected' : ''); ?>>None</option>
                            </select>
                        </div>

                        <div>
                            <label for="header_row" class="block text-sm font-medium text-gray-700 mb-2">Header Row</label>
                            <input type="number" 
                                   id="header_row" 
                                   name="header_row" 
                                   value="<?php echo e(old('header_row', 1)); ?>"
                                   min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Set to 0 if no header row</p>
                        </div>

                        <div>
                            <label for="data_start_row" class="block text-sm font-medium text-gray-700 mb-2">Data Start Row</label>
                            <input type="number" 
                                   id="data_start_row" 
                                   name="data_start_row" 
                                   value="<?php echo e(old('data_start_row', 2)); ?>"
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Row where actual data begins</p>
                        </div>

                        <div id="encoding-settings" class="md:col-span-2">
                            <label for="encoding" class="block text-sm font-medium text-gray-700 mb-2">File Encoding</label>
                            <select id="encoding" 
                                    name="encoding" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="UTF-8" <?php echo e(old('encoding') === 'UTF-8' ? 'selected' : ''); ?>>UTF-8</option>
                                <option value="ISO-8859-1" <?php echo e(old('encoding') === 'ISO-8859-1' ? 'selected' : ''); ?>>ISO-8859-1 (Latin-1)</option>
                                <option value="Windows-1252" <?php echo e(old('encoding') === 'Windows-1252' ? 'selected' : ''); ?>>Windows-1252</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Column Mapping -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Column Mapping</h2>
                    <p class="text-sm text-gray-600">Map file columns to standard fields. Use column names from your file or column numbers (A, B, C...)</p>
                </div>
                <div class="p-6">
                    <div id="column-mapping-container" class="space-y-4">
                        <?php
                        $standardFields = [
                            'load_reference' => 'Load Reference',
                            'order_number' => 'Order Number',
                            'customer_reference' => 'Customer Reference',
                            'delivery_date' => 'Delivery Date',
                            'delivery_time' => 'Delivery Time',
                            'delivery_address' => 'Delivery Address',
                            'delivery_postcode' => 'Delivery Postcode',
                            'delivery_city' => 'Delivery City',
                            'product_code' => 'Product Code',
                            'quantity' => 'Quantity',
                            'weight' => 'Weight',
                            'volume' => 'Volume',
                            'special_instructions' => 'Special Instructions',
                            'priority' => 'Priority Level',
                            'carrier' => 'Carrier',
                            'driver_name' => 'Driver Name',
                            'driver_phone' => 'Driver Phone',
                            'vehicle_registration' => 'Vehicle Registration',
                            'trailer_reference' => 'Trailer Reference'
                        ];
                        $oldMapping = old('column_mapping', []);
                        ?>

                        <?php $__currentLoopData = $standardFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0 w-48">
                                    <label class="text-sm font-medium text-gray-700"><?php echo e($label); ?></label>
                                    <?php if(in_array($key, ['load_reference', 'order_number'])): ?>
                                        <span class="text-xs text-red-500">(Required)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <input type="text" 
                                           name="column_mapping[<?php echo e($key); ?>]" 
                                           value="<?php echo e($oldMapping[$key] ?? ''); ?>"
                                           placeholder="Column name or number (e.g., 'Load_Ref' or 'A')"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                                </div>
                                <div class="flex-shrink-0">
                                    <?php if(in_array($key, ['load_reference', 'order_number'])): ?>
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Required</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">Optional</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Mapping Tips</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>Use exact column headers from your files (case-sensitive)</li>
                                        <li>For Excel files, you can use column letters: A, B, C, etc.</li>
                                        <li>For CSV files, use the actual column names or numbers</li>
                                        <li>Load Reference and Order Number are required for processing</li>
                                        <li>Leave fields blank if not present in your files</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Options -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Processing Options</h2>
                    <p class="text-sm text-gray-600">Configure how files using this template should be processed</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="flex items-center space-x-3">
                        <input type="checkbox" 
                               id="auto_process" 
                               name="auto_process" 
                               value="1"
                               <?php echo e(old('auto_process') ? 'checked' : ''); ?>

                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="auto_process" class="text-sm font-medium text-gray-700">Auto-process files</label>
                        <span class="text-xs text-gray-500">(If unchecked, files will require manual review before processing)</span>
                    </div>

                    <div>
                        <label for="duplicate_handling" class="block text-sm font-medium text-gray-700 mb-2">Duplicate Handling</label>
                        <select id="duplicate_handling" 
                                name="duplicate_handling" 
                                class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="skip" <?php echo e(old('duplicate_handling') === 'skip' ? 'selected' : ''); ?>>Skip duplicates</option>
                            <option value="overwrite" <?php echo e(old('duplicate_handling') === 'overwrite' ? 'selected' : ''); ?>>Overwrite existing</option>
                            <option value="create_new" <?php echo e(old('duplicate_handling') === 'create_new' ? 'selected' : ''); ?>>Create new record</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">How to handle records with duplicate Load References</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3">
                <a href="<?php echo e(route('outbound.imports.templates')); ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFileTypeSettings() {
    const fileType = document.getElementById('file_type').value;
    const csvSettings = document.getElementById('csv-settings');
    const textQualifierSettings = document.getElementById('text-qualifier-settings');
    
    if (fileType === 'csv' || fileType === 'txt') {
        csvSettings.style.display = 'block';
        textQualifierSettings.style.display = 'block';
    } else if (fileType === 'xlsx') {
        csvSettings.style.display = 'none';
        textQualifierSettings.style.display = 'none';
    } else {
        csvSettings.style.display = 'block';
        textQualifierSettings.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFileTypeSettings();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/imports/templates/create.blade.php ENDPATH**/ ?>