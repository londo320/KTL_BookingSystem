<?php $__env->startSection('title', 'Arrival Time Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    🕐 Arrival Time Settings
                </h1>
                <p class="mt-2 text-gray-600">Configure early/late arrival tolerances for global, depot, and customer levels</p>
            </div>
            <div>
                <a href="<?php echo e(route('app.arrival-time-settings.create')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    ➕ Add New Setting
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🔍 Filter Settings</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                    <select name="level" id="level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all" <?php echo e($level == 'all' ? 'selected' : ''); ?>>🌐 All Levels</option>
                        <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php echo e($level == $key ? 'selected' : ''); ?>>
                                <?php echo e($key == 'global' ? '🌐' : ($key == 'depot' ? '🏢' : '👤')); ?> <?php echo e($label); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">Depot</label>
                    <select name="depot_id" id="depot_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">🏢 All Depots</option>
                        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($depot->id); ?>" <?php echo e($depotId == $depot->id ? 'selected' : ''); ?>><?php echo e($depot->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" id="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">👤 All Customers</option>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($customer->id); ?>" <?php echo e($customerId == $customer->id ? 'selected' : ''); ?>><?php echo e($customer->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        🔍 Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Help Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h4 class="text-sm font-medium text-blue-800">ℹ️ How Arrival Time Settings Work</h4>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li><strong>Hierarchical inheritance:</strong> Customer-specific → Depot-specific → Global (fallback)</li>
                        <li><strong>Early threshold:</strong> More than X minutes before scheduled = early</li>
                        <li><strong>Late threshold:</strong> More than X minutes after scheduled = late</li>
                        <li><strong>On-time window:</strong> Within both thresholds = acceptable timing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">⚙️ Current Settings (<?php echo e($settings->total()); ?> total)</h3>
            <p class="text-sm text-gray-600 mt-1">Manage arrival time tolerances for different levels</p>
        </div>
        <div class="overflow-hidden">
            <?php if($settings->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level & Scope</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Early Threshold</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Late Threshold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <?php
                                            $levelData = [
                                                'global' => ['emoji' => '🌐', 'class' => 'bg-blue-100 text-blue-800', 'label' => 'Global'],
                                                'depot' => ['emoji' => '🏢', 'class' => 'bg-green-100 text-green-800', 'label' => 'Depot'],
                                                'customer' => ['emoji' => '👤', 'class' => 'bg-purple-100 text-purple-800', 'label' => 'Customer'],
                                            ];
                                            $data = $levelData[$setting->level] ?? $levelData['global'];
                                        ?>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($data['class']); ?>">
                                                <?php echo e($data['emoji']); ?> <?php echo e($data['label']); ?>

                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-900">
                                            <?php if($setting->level === 'global'): ?>
                                                Applied to all bookings (fallback)
                                            <?php elseif($setting->level === 'depot'): ?>
                                                🏢 <?php echo e($setting->depot->name ?? 'Unknown Depot'); ?>

                                            <?php elseif($setting->level === 'customer'): ?>
                                                👤 <?php echo e($setting->customer->name ?? 'Unknown Customer'); ?>

                                                <?php if($setting->customer && $setting->customer->depot): ?>
                                                    <br><span class="text-xs text-gray-500">@ <?php echo e($setting->customer->depot->name); ?></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    ⏪ <?php echo e($setting->early_threshold_minutes); ?>min
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    ⏰ <?php echo e($setting->late_threshold_minutes); ?>min
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo e($setting->description ?: '-'); ?></div>
                                <div class="text-xs text-gray-500 mt-1">
                                    On-time window: <?php echo e($setting->early_threshold_minutes); ?>min early to <?php echo e($setting->late_threshold_minutes); ?>min late
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                <?php echo e($setting->created_at->format('M j, Y')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('app.arrival-time-settings.show', $setting)); ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-sm font-medium transition-colors"
                                       title="View Details">
                                        👁️ View
                                    </a>
                                    <a href="<?php echo e(route('app.arrival-time-settings.edit', $setting)); ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-md text-sm font-medium transition-colors"
                                       title="Edit Setting">
                                        ✏️ Edit
                                    </a>
                                    <?php if($setting->level !== 'global' || $settings->where('level', 'global')->count() > 1): ?>
                                    <form method="POST" action="<?php echo e(route('app.arrival-time-settings.destroy', $setting)); ?>" class="inline" 
                                          onsubmit="return confirm('🗑️ Deactivate this arrival time setting?\\n\\nThis will make the setting inactive but preserve historical data.')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 rounded-md text-sm font-medium transition-colors"
                                                title="Deactivate Setting">
                                            🗑️ Deactivate
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <?php echo e($settings->appends(request()->query())->links()); ?>

            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">⚙️</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Arrival Time Settings Found</h3>
                <p class="text-gray-600 mb-4">Create your first arrival time setting to define early/late tolerances.</p>
                <a href="<?php echo e(route('app.arrival-time-settings.create')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    ➕ Create First Setting
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Auto-submit form when filter values change for better UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const selects = form.querySelectorAll('select');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Add a small delay to prevent rapid submissions
            clearTimeout(window.autoSubmitTimeout);
            window.autoSubmitTimeout = setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/arrival-time-settings/index.blade.php ENDPATH**/ ?>