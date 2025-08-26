<?php $__env->startSection('title', 'Upload WMS File'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="<?php echo e(route('outbound.imports.dashboard')); ?>" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Upload WMS File</h1>
                    <p class="text-gray-600 mt-1">Select template and upload your WMS file</p>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('outbound.imports.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-8">
            <?php echo csrf_field(); ?>

            <!-- Template Selection -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Select Import Template</h2>
                    <p class="text-sm text-gray-600">Choose the template that matches your file format</p>
                </div>
                <div class="p-6">
                    <?php if($templates->count() > 0): ?>
                        <div class="space-y-6">
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $system => $systemTemplates): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900 mb-3"><?php echo e(strtoupper(str_replace('_', ' ', $system))); ?></h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php $__currentLoopData = $systemTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <label class="relative">
                                                <input type="radio" name="import_template_id" value="<?php echo e($template->id); ?>" 
                                                       class="sr-only peer" 
                                                       <?php echo e(request('template') == $template->id ? 'checked' : ''); ?>

                                                       <?php echo e($loop->parent->first && $loop->first && !request('template') ? 'checked' : ''); ?>>
                                                <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex-1">
                                                            <h4 class="text-sm font-medium text-gray-900"><?php echo e($template->name); ?></h4>
                                                            <p class="text-xs text-gray-600 mt-1"><?php echo e($template->description); ?></p>
                                                            <div class="flex items-center space-x-2 mt-2">
                                                                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                                                    <?php echo e(strtoupper($template->file_type)); ?>

                                                                </span>
                                                                <?php if($template->auto_process): ?>
                                                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">
                                                                        Auto Process
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">
                                                                        Manual Review
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="w-4 h-4 border-2 border-gray-300 rounded-full peer-checked:border-blue-500 peer-checked:bg-blue-500 peer-checked:bg-opacity-20"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-gray-500 mb-4">No import templates configured</p>
                            <a href="<?php echo e(route('outbound.imports.templates.create')); ?>" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Create First Template
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php $__errorArgs = ['import_template_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-sm mt-2"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <!-- File Upload -->
            <?php if($templates->count() > 0): ?>
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Upload File</h2>
                    <p class="text-sm text-gray-600">Select your WMS file to import</p>
                </div>
                <div class="p-6">
                    <div class="max-w-lg mx-auto">
                        <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload a file</span>
                                        <input id="file-upload" name="file" type="file" class="sr-only" 
                                               accept=".csv,.xlsx,.txt,.xml" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">CSV, XLSX, TXT up to 10MB</p>
                            </div>
                        </div>
                        
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-sm mt-2"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div id="file-info" class="mt-4 hidden">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm font-medium text-blue-800" id="file-name"></p>
                                        <p class="text-sm text-blue-600" id="file-details"></p>
                                    </div>
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
                </div>
                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">How Processing Works</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li><strong>Auto Process:</strong> File is processed immediately after upload</li>
                                        <li><strong>Manual Review:</strong> You can preview the data before processing</li>
                                        <li>Orders are matched to registered driver arrivals by load reference</li>
                                        <li>Failed rows can be reviewed and manually corrected</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="<?php echo e(route('outbound.imports.dashboard')); ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Upload & Process
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.getElementById('file-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('file-name').textContent = file.name;
        document.getElementById('file-details').textContent = 
            `${(file.size / 1024 / 1024).toFixed(2)} MB • ${file.type || 'Unknown type'}`;
        document.getElementById('file-info').classList.remove('hidden');
    }
});

// Template selection validation
document.addEventListener('DOMContentLoaded', function() {
    const templateInputs = document.querySelectorAll('input[name="import_template_id"]');
    templateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // You could add template-specific file type validation here
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/imports/create.blade.php ENDPATH**/ ?>