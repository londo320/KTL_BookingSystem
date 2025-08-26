<?php $__env->startSection('title', 'Import Details - ' . $fileUpload->original_filename); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="<?php echo e(route('outbound.imports.dashboard')); ?>" 
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo e($fileUpload->original_filename); ?></h1>
                        <p class="text-gray-600 mt-1">Import processing details</p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo e($fileUpload->status_badge); ?>">
                        <?php echo e($fileUpload->status_display); ?>

                    </span>
                </div>
                <div class="flex space-x-3">
                    <?php if($fileUpload->canBeReprocessed()): ?>
                        <form method="POST" action="<?php echo e(route('outbound.imports.reprocess', $fileUpload)); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md"
                                    onclick="return confirm('Are you sure you want to reprocess this file?')">
                                Reprocess
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="<?php echo e(route('outbound.imports.download', $fileUpload)); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                        Download Original
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Processing Summary -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Processing Summary</h2>
                    </div>
                    <div class="p-6">
                        <?php if($fileUpload->total_rows): ?>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600"><?php echo e($fileUpload->total_rows); ?></div>
                                    <div class="text-sm text-gray-500">Total Rows</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600"><?php echo e($fileUpload->successful_rows ?? 0); ?></div>
                                    <div class="text-sm text-gray-500">Successful</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600"><?php echo e($fileUpload->failed_rows ?? 0); ?></div>
                                    <div class="text-sm text-gray-500">Failed</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-600"><?php echo e($fileUpload->duplicate_rows ?? 0); ?></div>
                                    <div class="text-sm text-gray-500">Duplicates</div>
                                </div>
                            </div>

                            <?php if($fileUpload->processed_rows > 0): ?>
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span><?php echo e(number_format($fileUpload->progress_percentage, 1)); ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($fileUpload->progress_percentage); ?>%"></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Success Rate</span>
                                        <span><?php echo e(number_format($fileUpload->success_rate, 1)); ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: <?php echo e($fileUpload->success_rate); ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-gray-500">Processing not started or no data available</p>
                        <?php endif; ?>

                        <?php if($fileUpload->processing_duration_formatted): ?>
                            <p class="text-sm text-gray-600">
                                <strong>Processing Time:</strong> <?php echo e($fileUpload->processing_duration_formatted); ?>

                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Row Results -->
                <?php if($fileUpload->rowResults->count() > 0): ?>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Row Processing Results</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Row</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Error</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">WMS Order</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $fileUpload->rowResults->take(100); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $result): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo e($result->row_number); ?>

                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full <?php echo e($result->status_badge); ?>">
                                                    <?php echo e($result->status_display); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900"><?php echo e($result->data_summary); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if($result->error_message): ?>
                                                    <div class="text-sm text-red-600"><?php echo e($result->error_message); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if($result->wmsStagingOrder): ?>
                                                    <a href="#" class="text-blue-600 hover:text-blue-900 text-sm">
                                                        <?php echo e($result->wmsStagingOrder->order_reference); ?>

                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            
                            <?php if($fileUpload->rowResults->count() > 100): ?>
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                    <p class="text-sm text-gray-600 text-center">
                                        Showing first 100 rows of <?php echo e($fileUpload->rowResults->count()); ?> total results
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Error Log -->
                <?php if($fileUpload->hasErrors()): ?>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Error Details</h2>
                        </div>
                        <div class="p-6">
                            <?php if($fileUpload->error_log): ?>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                    <h4 class="text-sm font-medium text-red-800 mb-2">Processing Error:</h4>
                                    <pre class="text-sm text-red-700 whitespace-pre-wrap"><?php echo e($fileUpload->error_log); ?></pre>
                                </div>
                            <?php endif; ?>
                            
                            <?php $errors = $fileUpload->getErrorSummary(); ?>
                            <?php if(count($errors) > 0): ?>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-yellow-800 mb-2">Row Errors:</h4>
                                    <ul class="list-disc list-inside text-sm text-yellow-700 space-y-1">
                                        <?php $__currentLoopData = array_slice($errors, 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(count($errors) > 10): ?>
                                            <li class="text-yellow-600">... and <?php echo e(count($errors) - 10); ?> more errors</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- File Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">File Details</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Template</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->name); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Size</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->file_size_formatted); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">MIME Type</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->mime_type); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Uploaded By</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->uploadedBy->name); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Uploaded At</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->uploaded_at->format('M j, Y H:i')); ?></dd>
                        </div>
                        <?php if($fileUpload->processing_started_at): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Processing Started</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($fileUpload->processing_started_at->format('M j, Y H:i')); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if($fileUpload->processing_completed_at): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Processing Completed</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($fileUpload->processing_completed_at->format('M j, Y H:i')); ?></dd>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Template Configuration -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Template Configuration</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Source System</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->source_system); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Type</dt>
                            <dd class="text-sm text-gray-900"><?php echo e(strtoupper($fileUpload->importTemplate->file_type)); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Header Row</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->header_row ?: 'None'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Data Start Row</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->data_start_row); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Delimiter</dt>
                            <dd class="text-sm text-gray-900 font-mono"><?php echo e($fileUpload->importTemplate->delimiter === "\t" ? '\\t (tab)' : $fileUpload->importTemplate->delimiter); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Auto Process</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->auto_process ? 'Yes' : 'No'); ?></dd>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <?php if($fileUpload->canBeDeleted()): ?>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="<?php echo e(route('outbound.imports.destroy', $fileUpload)); ?>" 
                                  onsubmit="return confirm('Are you sure you want to delete this import? This cannot be undone.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" 
                                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-md font-medium">
                                    Delete Import
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/imports/show.blade.php ENDPATH**/ ?>