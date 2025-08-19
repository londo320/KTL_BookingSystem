<?php $__env->startSection('title', 'Arrival Time Setting Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    👁️ Arrival Time Setting Details
                </h1>
                <p class="mt-2 text-gray-600">View arrival time tolerance configuration and examples</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('admin.arrival-time-settings.edit', $arrivalTimeSetting)); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    ✏️ Edit Setting
                </a>
                <a href="<?php echo e(route('admin.arrival-time-settings.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Setting Details -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">⚙️ Setting Configuration</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Level and Scope -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Level & Scope</label>
                    <div class="space-y-2">
                        <?php
                            $levelData = [
                                'global' => ['emoji' => '🌐', 'class' => 'bg-blue-100 text-blue-800', 'label' => 'Global'],
                                'depot' => ['emoji' => '🏢', 'class' => 'bg-green-100 text-green-800', 'label' => 'Depot'],
                                'customer' => ['emoji' => '👤', 'class' => 'bg-purple-100 text-purple-800', 'label' => 'Customer'],
                            ];
                            $data = $levelData[$arrivalTimeSetting->level] ?? $levelData['global'];
                        ?>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($data['class']); ?>">
                                <?php echo e($data['emoji']); ?> <?php echo e($data['label']); ?> Level
                            </span>
                        </div>
                        <div class="text-sm text-gray-900">
                            <?php if($arrivalTimeSetting->level === 'global'): ?>
                                Applied to all bookings as fallback when no specific rules exist
                            <?php elseif($arrivalTimeSetting->level === 'depot'): ?>
                                Applied to all bookings at: <strong><?php echo e($arrivalTimeSetting->depot->name ?? 'Unknown Depot'); ?></strong>
                            <?php elseif($arrivalTimeSetting->level === 'customer'): ?>
                                Applied to all bookings for: <strong><?php echo e($arrivalTimeSetting->customer->name ?? 'Unknown Customer'); ?></strong>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Thresholds -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tolerance Thresholds</label>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="text-blue-600">⏪</span>
                                <span class="text-sm font-medium text-blue-800">Early Threshold</span>
                            </div>
                            <span class="text-lg font-bold text-blue-600"><?php echo e($arrivalTimeSetting->early_threshold_minutes); ?> min</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="text-red-600">⏰</span>
                                <span class="text-sm font-medium text-red-800">Late Threshold</span>
                            </div>
                            <span class="text-lg font-bold text-red-600"><?php echo e($arrivalTimeSetting->late_threshold_minutes); ?> min</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if($arrivalTimeSetting->description): ?>
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-700"><?php echo e($arrivalTimeSetting->description); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Metadata -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                    <span class="font-medium">Created:</span> <?php echo e($arrivalTimeSetting->created_at->format('M j, Y H:i')); ?>

                </div>
                <div>
                    <span class="font-medium">Updated:</span> <?php echo e($arrivalTimeSetting->updated_at->format('M j, Y H:i')); ?>

                </div>
                <div>
                    <span class="font-medium">Status:</span> 
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ✅ Active
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Example Scenarios -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">📋 Example Scenarios</h3>
            <p class="text-sm text-gray-600 mt-1">How different arrival times would be classified using this setting</p>
        </div>
        <div class="p-6">
            <?php if(count($examples) > 0): ?>
            <div class="space-y-4">
                <?php $__currentLoopData = $examples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $example): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($example['details']['css_class']); ?>">
                                <?php echo e($example['details']['emoji']); ?> <?php echo e($example['name']); ?>

                            </span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">
                                Scheduled: 10:00 AM → Actual: <?php echo e($example['actual_time']->format('g:i A')); ?>

                            </div>
                            <div class="text-xs text-gray-500">
                                <?php echo e($example['details']['message']); ?>

                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">
                            Status: <?php echo e(ucfirst(str_replace('_', ' ', $example['details']['status']))); ?>

                        </div>
                        <div class="text-xs text-gray-500">
                            <?php echo e($example['details']['difference_minutes']); ?> min difference
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8">
                <div class="text-4xl mb-2">📋</div>
                <p class="text-gray-600">No example scenarios available</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Current Tolerance Window -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">🎯 Current Tolerance Window</h3>
            <p class="text-sm text-gray-600 mt-1">Visual representation of the acceptable arrival time range</p>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Visual Timeline -->
                <div class="relative">
                    <div class="flex items-center justify-center h-16 bg-gradient-to-r from-blue-100 via-green-100 to-red-100 rounded-lg">
                        <div class="absolute left-0 top-0 bottom-0 w-1/3 flex items-center justify-center">
                            <span class="text-sm font-medium text-blue-800">⏪ Too Early</span>
                        </div>
                        <div class="absolute left-1/3 right-1/3 top-0 bottom-0 flex items-center justify-center bg-green-200 bg-opacity-50">
                            <span class="text-sm font-medium text-green-800">✅ On Time Window</span>
                        </div>
                        <div class="absolute right-0 top-0 bottom-0 w-1/3 flex items-center justify-center">
                            <span class="text-sm font-medium text-red-800">⏰ Too Late</span>
                        </div>
                    </div>
                </div>

                <!-- Time Examples -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-lg font-bold text-blue-600">
                            <?php if($arrivalTimeSetting->early_threshold_minutes > 0): ?>
                                Before <?php echo e(\Carbon\Carbon::create(2024, 1, 1, 10, 0)->subMinutes($arrivalTimeSetting->early_threshold_minutes)->format('g:i A')); ?>

                            <?php else: ?>
                                Before 10:00 AM
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-blue-700 mt-1">Early Arrival</div>
                        <div class="text-xs text-blue-600 mt-1">
                            <?php if($arrivalTimeSetting->early_threshold_minutes > 0): ?>
                                More than <?php echo e($arrivalTimeSetting->early_threshold_minutes); ?> min early
                            <?php else: ?>
                                Any time before scheduled
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="text-lg font-bold text-green-600">
                            <?php if($arrivalTimeSetting->early_threshold_minutes > 0 || $arrivalTimeSetting->late_threshold_minutes > 0): ?>
                                <?php echo e(\Carbon\Carbon::create(2024, 1, 1, 10, 0)->subMinutes($arrivalTimeSetting->early_threshold_minutes)->format('g:i A')); ?> - 
                                <?php echo e(\Carbon\Carbon::create(2024, 1, 1, 10, 0)->addMinutes($arrivalTimeSetting->late_threshold_minutes)->format('g:i A')); ?>

                            <?php else: ?>
                                Exactly 10:00 AM
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-green-700 mt-1">On Time</div>
                        <div class="text-xs text-green-600 mt-1">Acceptable window</div>
                    </div>

                    <div class="p-4 bg-red-50 rounded-lg">
                        <div class="text-lg font-bold text-red-600">
                            <?php if($arrivalTimeSetting->late_threshold_minutes > 0): ?>
                                After <?php echo e(\Carbon\Carbon::create(2024, 1, 1, 10, 0)->addMinutes($arrivalTimeSetting->late_threshold_minutes)->format('g:i A')); ?>

                            <?php else: ?>
                                After 10:00 AM
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-red-700 mt-1">Late Arrival</div>
                        <div class="text-xs text-red-600 mt-1">
                            <?php if($arrivalTimeSetting->late_threshold_minutes > 0): ?>
                                More than <?php echo e($arrivalTimeSetting->late_threshold_minutes); ?> min late
                            <?php else: ?>
                                Any time after scheduled
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-800 mb-2">📊 Summary</h4>
                    <div class="text-sm text-gray-600">
                        <?php if($arrivalTimeSetting->early_threshold_minutes === 0 && $arrivalTimeSetting->late_threshold_minutes === 0): ?>
                            <p><strong>Strict timing:</strong> Only exact arrival times are considered on-time. Any deviation will be flagged as early or late.</p>
                        <?php elseif($arrivalTimeSetting->early_threshold_minutes === $arrivalTimeSetting->late_threshold_minutes): ?>
                            <p><strong>Symmetric tolerance:</strong> ±<?php echo e($arrivalTimeSetting->early_threshold_minutes); ?> minutes around scheduled time is acceptable.</p>
                        <?php else: ?>
                            <p><strong>Asymmetric tolerance:</strong> <?php echo e($arrivalTimeSetting->early_threshold_minutes); ?> minutes early tolerance, <?php echo e($arrivalTimeSetting->late_threshold_minutes); ?> minutes late tolerance.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/arrival-time-settings/show.blade.php ENDPATH**/ ?>