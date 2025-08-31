

<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    📊 Customer Behavior Analysis
                </h1>
                <p class="mt-2 text-gray-600">Track customer booking patterns, rebooks, and cancellations</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('app.customer-behavior.flagged')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                    🚩 Flagged Customers
                </a>
                <a href="<?php echo e(route('app.customer-behavior.export', ['days' => $days])); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    📥 Export CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📝 Analysis Filters</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="days" class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                    <select name="days" id="days" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="7" <?php echo e($days == 7 ? 'selected' : ''); ?>>📅 Last 7 days</option>
                        <option value="30" <?php echo e($days == 30 ? 'selected' : ''); ?>>📅 Last 30 days</option>
                        <option value="90" <?php echo e($days == 90 ? 'selected' : ''); ?>>📅 Last 90 days</option>
                        <option value="365" <?php echo e($days == 365 ? 'selected' : ''); ?>>📅 Last year</option>
                    </select>
                </div>
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="rebook_count" <?php echo e($sortBy == 'rebook_count' ? 'selected' : ''); ?>>🔄 Rebooks</option>
                        <option value="cancellation_count" <?php echo e($sortBy == 'cancellation_count' ? 'selected' : ''); ?>>❌ Cancellations</option>
                        <option value="last_minute_count" <?php echo e($sortBy == 'last_minute_count' ? 'selected' : ''); ?>>⏰ Last Minute Actions</option>
                        <option value="risk_score" <?php echo e($sortBy == 'risk_score' ? 'selected' : ''); ?>>⚠️ Risk Score</option>
                        <option value="name" <?php echo e($sortBy == 'name' ? 'selected' : ''); ?>>🏢 Customer Name</option>
                    </select>
                </div>
                <div>
                    <label for="direction" class="block text-sm font-medium text-gray-700 mb-2">Order</label>
                    <select name="direction" id="direction" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="desc" <?php echo e($direction == 'desc' ? 'selected' : ''); ?>>📈 High to Low</option>
                        <option value="asc" <?php echo e($direction == 'asc' ? 'selected' : ''); ?>>📉 Low to High</option>
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

    <!-- Overall Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-8 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo e($overallStats['total_customers']); ?></div>
            <div class="text-sm text-gray-600 mt-1">👥 Customers</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-amber-600"><?php echo e($overallStats['total_rebooks']); ?></div>
            <div class="text-sm text-gray-600 mt-1">🔄 Rebooks</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-cyan-600"><?php echo e($overallStats['total_cancellations']); ?></div>
            <div class="text-sm text-gray-600 mt-1">❌ Cancellations</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-red-600"><?php echo e($overallStats['total_last_minute']); ?></div>
            <div class="text-sm text-gray-600 mt-1">⏰ Last Minute</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-orange-600"><?php echo e($customers->sum('late_arrivals')); ?></div>
            <div class="text-sm text-gray-600 mt-1">🕐 Late Arrivals</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-500"><?php echo e($customers->sum('early_arrivals')); ?></div>
            <div class="text-sm text-gray-600 mt-1">⏪ Early Arrivals</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-green-600"><?php echo e($overallStats['avg_notice_hours']); ?>h</div>
            <div class="text-sm text-gray-600 mt-1">📊 Avg Notice</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-gray-600"><?php echo e($overallStats['rebook_rate']); ?>%</div>
            <div class="text-sm text-gray-600 mt-1">📈 Rebook Rate</div>
        </div>
    </div>

    <!-- Customer Analysis Table -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">🏢 Customer Analysis (<?php echo e($days); ?> days)</h3>
            <p class="text-sm text-gray-600 mt-1">Detailed breakdown of customer booking behavior and risk assessment</p>
        </div>
        <div class="overflow-hidden">
            <?php if($customers->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rebooks</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cancellations</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Last Minute</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Notice</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 <?php echo e($customer->risk_level === 'High Risk' ? 'bg-red-50 border-l-4 border-red-500' : ($customer->risk_level === 'Medium Risk' ? 'bg-amber-50 border-l-4 border-amber-500' : '')); ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($customer->name); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($customer->user_emails ?: '📧 No users assigned'); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    📊 <?php echo e($customer->total_bookings); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    🔄 <?php echo e($customer->rebook_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800">
                                    ❌ <?php echo e($customer->cancellation_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ⏰ <?php echo e($customer->last_minute_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                📊 <?php echo e($customer->avg_hours_notice); ?>h
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php
                                    $riskClasses = [
                                        'High Risk' => 'bg-red-100 text-red-800',
                                        'Medium Risk' => 'bg-amber-100 text-amber-800',
                                        'Low Risk' => 'bg-gray-100 text-gray-800',
                                        'No Risk' => 'bg-green-100 text-green-800'
                                    ];
                                    $riskClass = $riskClasses[$customer->risk_level] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($riskClass); ?>">
                                    <?php echo e($customer->risk_level === 'High Risk' ? '🚨' : ($customer->risk_level === 'Medium Risk' ? '⚠️' : ($customer->risk_level === 'Low Risk' ? '⚡' : '✅'))); ?> 
                                    <?php echo e($customer->risk_level); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('app.customer-behavior.show', $customer->id)); ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-sm font-medium transition-colors"
                                       title="View Details">
                                        👁️ View
                                    </a>
                                    <?php if($customer->risk_level === 'High Risk' || $customer->risk_level === 'Medium Risk'): ?>
                                    <button class="inline-flex items-center px-3 py-1 border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-md text-sm font-medium transition-colors"
                                            title="Flag Customer" onclick="flagCustomer(<?php echo e($customer->id); ?>)">
                                        🚩 Flag
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📊</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Customer Data Available</h3>
                <p class="text-gray-600">No customer behavior data found for the selected <?php echo e($days); ?>-day period.</p>
                <p class="text-sm text-gray-500 mt-2">Try selecting a different time period or check that customers have booking activity.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function flagCustomer(customerId) {
    if (confirm('🚩 Flag this customer for review?\n\nThis will mark the customer for management attention and further review.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '⏳ Flagging...';
        button.disabled = true;
        
        // Simulate API call - replace with actual implementation
        setTimeout(() => {
            alert('✅ Customer has been flagged for review and will be reviewed by management.');
            button.innerHTML = '✅ Flagged';
            button.classList.remove('border-amber-300', 'text-amber-700', 'bg-amber-50', 'hover:bg-amber-100');
            button.classList.add('border-green-300', 'text-green-700', 'bg-green-50');
            
            // Reset after 3 seconds
            setTimeout(() => {
                button.innerHTML = originalText;
                button.disabled = false;
                button.classList.remove('border-green-300', 'text-green-700', 'bg-green-50');
                button.classList.add('border-amber-300', 'text-amber-700', 'bg-amber-50', 'hover:bg-amber-100');
            }, 3000);
        }, 1500);
    }
}

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

// Add smooth scrolling to anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/customer-behavior/index.blade.php ENDPATH**/ ?>