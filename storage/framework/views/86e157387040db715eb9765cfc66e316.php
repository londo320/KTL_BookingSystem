<?php $__env->startSection('title', 'Review Import - ' . $fileUpload->original_filename); ?>

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
                        <h1 class="text-3xl font-bold text-gray-900">Review Import</h1>
                        <p class="text-gray-600 mt-1"><?php echo e($fileUpload->original_filename); ?></p>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">
                        Pending Review
                    </span>
                </div>
                <div class="flex space-x-3">
                    <form method="POST" action="<?php echo e(route('outbound.imports.reject', $fileUpload)); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md"
                                onclick="return confirm('Are you sure you want to reject this import?')">
                            Reject Import
                        </button>
                    </form>
                    <form method="POST" action="<?php echo e(route('outbound.imports.approve', $fileUpload)); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
                                onclick="return confirm('Are you sure you want to approve and process this import?')">
                            Approve & Process
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- File Preview -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">File Preview</h2>
                        <p class="text-sm text-gray-600">Review the file contents before processing</p>
                    </div>
                    <div class="p-6">
                        <?php if($fileUpload->preview_data): ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <?php 
                                            $headers = array_keys($fileUpload->preview_data[0] ?? []);
                                            ?>
                                            <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    <?php echo e($header); ?>

                                                </th>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = array_slice($fileUpload->preview_data, 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-gray-50">
                                                <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        <?php echo e($row[$header] ?? ''); ?>

                                                    </td>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <?php if(count($fileUpload->preview_data) > 10): ?>
                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                        <p class="text-sm text-gray-600 text-center">
                                            Showing first 10 rows of <?php echo e(count($fileUpload->preview_data)); ?> total rows
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-gray-500">No preview data available</p>
                                <p class="text-sm text-gray-400 mt-1">File may need to be processed to view contents</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Validation Results -->
                <?php if($fileUpload->validation_errors): ?>
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Validation Issues</h2>
                            <p class="text-sm text-gray-600">Issues found during initial validation</p>
                        </div>
                        <div class="p-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Validation Warnings</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                <?php $__currentLoopData = $fileUpload->validation_errors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li><?php echo e($error); ?></li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Review Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Review Checklist</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Verify the file format matches the expected template</li>
                                    <li>Check that all required columns are present</li>
                                    <li>Review sample data for accuracy</li>
                                    <li>Ensure load references follow the expected format</li>
                                    <li>Confirm customer and order information looks correct</li>
                                </ul>
                            </div>
                            <div class="mt-4 text-sm text-blue-700">
                                <strong>Note:</strong> Approving this import will process all rows and attempt to match them with registered physical loads.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- File Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">File Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Original Filename</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->original_filename); ?></dd>
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
                            <dt class="text-sm font-medium text-gray-500">Upload Time</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->uploaded_at->format('M j, Y H:i')); ?></dd>
                        </div>
                        <?php if($fileUpload->total_rows): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estimated Rows</dt>
                                <dd class="text-sm text-gray-900"><?php echo e(number_format($fileUpload->total_rows)); ?></dd>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Template Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Template Configuration</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Template Name</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->name); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Source System</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->source_system); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">File Type</dt>
                            <dd class="text-sm text-gray-900"><?php echo e(strtoupper($fileUpload->importTemplate->file_type)); ?></dd>
                        </div>
                        <?php if($fileUpload->importTemplate->description): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($fileUpload->importTemplate->description); ?></dd>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="<?php echo e(route('outbound.imports.download', $fileUpload)); ?>" 
                           class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-md text-sm">
                            Download Original File
                        </a>
                        <a href="<?php echo e(route('outbound.imports.dashboard')); ?>" 
                           class="block w-full text-center bg-blue-100 hover:bg-blue-200 text-blue-800 py-2 px-4 rounded-md text-sm">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/imports/review.blade.php ENDPATH**/ ?>