<?php $__env->startSection('title', 'Import Templates'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Import Templates</h1>
                    <p class="text-gray-600 mt-1">Configure file formats and field mappings for different WMS systems</p>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('outbound.imports.dashboard')); ?>" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md font-medium">
                        Back to Imports
                    </a>
                    <a href="<?php echo e(route('outbound.imports.templates.create')); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Create Template
                    </a>
                </div>
            </div>
        </div>

        <!-- Templates Grid -->
        <?php if($templates->count() > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-<?php echo e($template->is_active ? 'green' : 'gray'); ?>-100 rounded-lg flex items-center justify-center">
                                            <?php if($template->file_type === 'csv'): ?>
                                                📄
                                            <?php elseif($template->file_type === 'xlsx'): ?>
                                                📊
                                            <?php elseif($template->file_type === 'xml'): ?>
                                                🔖
                                            <?php else: ?>
                                                📁
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900"><?php echo e($template->name); ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo e($template->source_system); ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?php if($template->is_active): ?>
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                    <?php endif; ?>
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                        <?php echo e(strtoupper($template->file_type)); ?>

                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600"><?php echo e($template->description ?: 'No description provided'); ?></p>
                            </div>

                            <!-- Template Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-4 text-center">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-lg font-semibold text-gray-900"><?php echo e($template->files_processed ?? 0); ?></div>
                                    <div class="text-xs text-gray-500">Files Processed</div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <div class="text-lg font-semibold text-gray-900"><?php echo e(count($template->column_mapping ?? [])); ?></div>
                                    <div class="text-xs text-gray-500">Mapped Fields</div>
                                </div>
                            </div>

                            <!-- Processing Options -->
                            <div class="mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?php echo e($template->auto_process ? 'Auto-process files' : 'Manual review required'); ?>

                                </div>
                                <?php if($template->header_row): ?>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                        </svg>
                                        Header row: <?php echo e($template->header_row); ?>

                                    </div>
                                <?php endif; ?>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Data starts: Row <?php echo e($template->data_start_row); ?>

                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-between">
                                <div class="flex space-x-2">
                                    <a href="<?php echo e(route('outbound.imports.templates.edit', $template)); ?>" 
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form method="POST" action="<?php echo e(route('outbound.imports.templates.toggle', $template)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                                            <?php echo e($template->is_active ? 'Deactivate' : 'Activate'); ?>

                                        </button>
                                    </form>
                                </div>
                                <div>
                                    <?php if($template->last_used_at): ?>
                                        <span class="text-xs text-gray-500">
                                            Used <?php echo e($template->last_used_at->diffForHumans()); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">Never used</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

        <?php else: ?>
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="h-12 w-12">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Import Templates</h3>
                <p class="mt-2 text-gray-500 max-w-sm mx-auto">
                    Create your first import template to define how different file formats should be processed.
                </p>
                <div class="mt-6">
                    <a href="<?php echo e(route('outbound.imports.templates.create')); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Create First Template
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Help Section -->
        <div class="mt-12 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-900">About Import Templates</h3>
                    <div class="mt-2 text-blue-700">
                        <p class="mb-4">Import templates define how to process files from different WMS systems:</p>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            <li><strong>Column Mapping:</strong> Map file columns to standard fields (Load Reference, Order Number, etc.)</li>
                            <li><strong>File Format:</strong> Configure CSV delimiters, Excel sheets, header rows, etc.</li>
                            <li><strong>Validation Rules:</strong> Define required fields and data validation</li>
                            <li><strong>Processing Mode:</strong> Auto-process or require manual review</li>
                            <li><strong>Transformation Rules:</strong> Apply data transformations (uppercase, date formats, etc.)</li>
                        </ul>
                        <p class="mt-4 text-sm">
                            <strong>Tip:</strong> Create separate templates for each WMS system or file variant you need to process.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/imports/templates/index.blade.php ENDPATH**/ ?>