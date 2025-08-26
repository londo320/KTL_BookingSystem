<?php $__env->startSection('title', 'WMS File Imports Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">WMS File Imports</h1>
                    <p class="text-gray-600 mt-1">Upload and process files from WMS systems</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('outbound.imports.templates')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium">
                        Manage Templates
                    </a>
                    <a href="<?php echo e(route('outbound.imports.create')); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Upload File
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Total Uploads</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($statistics['total_uploads']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Processing</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($statistics['processing']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Pending Review</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($statistics['pending_review']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Failed Today</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($statistics['failed_today']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Uploads -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Uploads</h2>
                </div>
                <div class="p-6">
                    <?php if($recentUploads->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $recentUploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $upload): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900"><?php echo e($upload->original_filename); ?></h3>
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($upload->status_badge); ?>">
                                                <?php echo e($upload->status_display); ?>

                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <?php echo e($upload->importTemplate->name); ?> • <?php echo e($upload->file_size_formatted); ?>

                                        </p>
                                        <?php if($upload->total_rows): ?>
                                            <p class="text-xs text-gray-500">
                                                <?php echo e($upload->successful_rows ?? 0); ?> / <?php echo e($upload->total_rows); ?> rows processed
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-xs text-gray-500">
                                            <?php echo e($upload->uploaded_at->diffForHumans()); ?> by <?php echo e($upload->uploadedBy->name); ?>

                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        <a href="<?php echo e(route('outbound.imports.show', $upload)); ?>" 
                                           class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No files uploaded yet</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Available Templates -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Import Templates</h2>
                    <a href="<?php echo e(route('outbound.imports.templates')); ?>" 
                       class="text-blue-600 hover:text-blue-900 text-sm">Manage All</a>
                </div>
                <div class="p-6">
                    <?php if($templates->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900"><?php echo e($template->name); ?></h3>
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                <?php echo e(strtoupper($template->file_type)); ?>

                                            </span>
                                            <?php if(!$template->is_active): ?>
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactive</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo e($template->source_system); ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo e($template->files_processed); ?> files processed
                                            <?php if($template->last_used_at): ?>
                                                • Last used <?php echo e($template->last_used_at->diffForHumans()); ?>

                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="ml-4">
                                        <a href="<?php echo e(route('outbound.imports.create')); ?>?template=<?php echo e($template->id); ?>" 
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                            Use Template
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">No templates configured</p>
                            <a href="<?php echo e(route('outbound.imports.templates.create')); ?>" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                                Create First Template
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/imports/dashboard.blade.php ENDPATH**/ ?>