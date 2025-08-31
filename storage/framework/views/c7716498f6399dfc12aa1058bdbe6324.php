<?php $__env->startSection('title', 'Flagged Customers'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    🚩 Flagged Customers - High Risk Behavior
                </h1>
                <p class="mt-2 text-gray-600">Customers requiring management attention due to concerning booking patterns</p>
            </div>
            <div class="flex gap-3">
                <a href="<?php echo e(route('app.customer-behavior.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Analysis
                </a>
            </div>
        </div>
    </div>

    <!-- Time Period Filter -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📅 Analysis Period</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="days" class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                    <select name="days" id="days" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                        <option value="7" <?php echo e($days == 7 ? 'selected' : ''); ?>>📅 Last 7 days</option>
                        <option value="30" <?php echo e($days == 30 ? 'selected' : ''); ?>>📅 Last 30 days</option>
                        <option value="60" <?php echo e($days == 60 ? 'selected' : ''); ?>>📅 Last 60 days</option>
                        <option value="90" <?php echo e($days == 90 ? 'selected' : ''); ?>>📅 Last 90 days</option>
                        <option value="365" <?php echo e($days == 365 ? 'selected' : ''); ?>>📅 Last year</option>
                    </select>
                </div>
                <div class="md:col-span-3">
                    <p class="text-sm text-gray-600">
                        ℹ️ Showing customers with 3+ last-minute actions or 5+ rebooks in the selected period
                    </p>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-red-600"><?php echo e($flaggedCustomers->count()); ?></div>
            <div class="text-sm text-gray-600 mt-1">🚩 Flagged Customers</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-amber-600"><?php echo e($flaggedCustomers->where('last_minute_count', '>=', 5)->count()); ?></div>
            <div class="text-sm text-gray-600 mt-1">⏰ High Risk</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo e($flaggedCustomers->sum('rebook_count')); ?></div>
            <div class="text-sm text-gray-600 mt-1">🔄 Total Rebooks</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-cyan-600"><?php echo e($flaggedCustomers->sum('cancellation_count')); ?></div>
            <div class="text-sm text-gray-600 mt-1">❌ Cancellations</div>
        </div>
    </div>

    <!-- Flagged Customers -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">⚠️ High Risk Customers (<?php echo e($flaggedCustomers->count()); ?> found)</h3>
            <p class="text-sm text-gray-600 mt-1">Customers requiring management attention and possible intervention</p>
        </div>
        <div class="overflow-hidden">
            <?php if($flaggedCustomers->count() > 0): ?>
            
            <!-- Summary Alert -->
            <div class="mx-6 mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-amber-800">⚠️ Risk Assessment Summary</h4>
                        <p class="mt-1 text-sm text-amber-700">
                            These customers have exhibited concerning booking patterns that may indicate slot abuse or 
                            operational issues. Consider implementing restrictions or reaching out for feedback.
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Indicators</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Last Minute</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rebooks</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cancellations</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recent Dates</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $flaggedCustomers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 <?php echo e($customer->last_minute_count >= 5 ? 'bg-red-50 border-l-4 border-red-500' : 'bg-amber-50 border-l-4 border-amber-500'); ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($customer->name); ?></div>
                                        <div class="text-sm text-gray-500">📧 Contact via assigned users</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <?php if($customer->last_minute_count >= 5): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ⏰ Excessive Last-Minute
                                        </span>
                                    <?php elseif($customer->last_minute_count >= 3): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            ⏰ Frequent Last-Minute
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if($customer->rebook_count >= 10): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            🔄 Excessive Rebooks
                                        </span>
                                    <?php elseif($customer->rebook_count >= 5): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            🔄 Frequent Rebooks
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if($customer->cancellation_count >= 10): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ❌ High Cancellations
                                        </span>
                                    <?php elseif($customer->cancellation_count >= 5): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            ❌ Frequent Cancellations
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($customer->last_minute_count >= 5 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'); ?>">
                                    ⏰ <?php echo e($customer->last_minute_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($customer->rebook_count >= 10 ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800'); ?>">
                                    🔄 <?php echo e($customer->rebook_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    ❌ <?php echo e($customer->cancellation_count); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if($customer->last_minute_dates): ?>
                                    <div class="flex flex-wrap gap-1">
                                        <?php
                                            $dates = explode(',', $customer->last_minute_dates);
                                            $dates = array_slice($dates, 0, 3); // Show only first 3 dates
                                        ?>
                                        <?php $__currentLoopData = $dates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $date): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($date): ?>
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    <?php echo e(\Carbon\Carbon::parse($date)->format('M j')); ?>

                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(count(explode(',', $customer->last_minute_dates)) > 3): ?>
                                            <span class="text-xs text-gray-500">+more</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-gray-500">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('app.customer-behavior.show', $customer->id)); ?>" 
                                       class="inline-flex items-center px-3 py-1 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-md text-sm font-medium transition-colors"
                                       title="View Details">
                                        👁️ View
                                    </a>
                                    <button type="button" class="inline-flex items-center px-3 py-1 border border-amber-300 text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-md text-sm font-medium transition-colors"
                                            title="Contact Customer" onclick="contactCustomer('<?php echo e($customer->email ?? ''); ?>', '<?php echo e($customer->name); ?>')">
                                        📧 Contact
                                    </button>
                                    <button type="button" class="inline-flex items-center px-3 py-1 border border-red-300 text-red-700 bg-red-50 hover:bg-red-100 rounded-md text-sm font-medium transition-colors"
                                            title="Restrict Customer" onclick="restrictCustomer(<?php echo e($customer->id); ?>, '<?php echo e($customer->name); ?>')">
                                        🚫 Restrict
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Recommended Actions -->
            <div class="mx-6 my-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">💡 Recommended Actions</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-amber-800">📞 Contact Customers</h5>
                                <p class="mt-1 text-sm text-amber-700">
                                    Reach out to understand booking challenges and provide guidance on proper slot usage.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-blue-800">⚙️ Implement Restrictions</h5>
                                <p class="mt-1 text-sm text-blue-700">
                                    Consider temporary booking limits or requiring advance approvals for high-risk customers.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h5 class="text-sm font-medium text-green-800">📊 Monitor Trends</h5>
                                <p class="mt-1 text-sm text-green-700">
                                    Track improvements over time and adjust policies based on customer behavior patterns.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Flagged Customers</h3>
                <p class="text-gray-600">
                    Great! No customers meet the high-risk criteria for the selected time period.
                </p>
                <p class="text-sm text-gray-500 mt-2">All customers are exhibiting normal booking behavior patterns.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<!-- Contact Customer Modal -->
<div id="contactModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">📧 Contact Customer</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeContactModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Send an email to <strong id="customerName" class="text-gray-900"></strong>?
                </p>
                <p class="text-sm text-gray-400 mt-2">
                    This will open your default email client with a pre-filled message about booking behavior patterns.
                </p>
            </div>
            <div class="flex items-center px-4 py-3 gap-3 justify-end">
                <button type="button" onclick="closeContactModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <a href="#" id="emailLink" 
                   class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md hover:bg-blue-700 transition-colors">
                    📧 Send Email
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Restrict Customer Modal -->
<div id="restrictModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">🚫 Restrict Customer</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeRestrictModal()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">
                    Apply booking restrictions to <strong id="restrictCustomerName" class="text-gray-900"></strong>?
                </p>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Restriction Type:</label>
                    <select id="restrictionType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        <option value="approval_required">Require admin approval for all bookings</option>
                        <option value="limit_rebooks">Limit rebooks to 2 per month</option>
                        <option value="no_last_minute">Block last-minute bookings (&lt;24h)</option>
                        <option value="temporary_ban">Temporary booking ban (7 days)</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center px-4 py-3 gap-3 justify-end">
                <button type="button" onclick="closeRestrictModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="applyRestriction()" 
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 transition-colors">
                    🚫 Apply Restriction
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentCustomerId = null;

function contactCustomer(email, name) {
    document.getElementById('customerName').textContent = name;
    
    const subject = encodeURIComponent('Regarding Your Recent Booking Activity');
    const body = encodeURIComponent(`Dear ${name},\n\nWe noticed some frequent changes to your recent bookings. We'd like to understand if there are any challenges you're facing with our booking system and how we can better assist you.\n\nPlease feel free to reach out if you have any questions or concerns.\n\nBest regards,\nYour Booking Team`);
    
    document.getElementById('emailLink').href = `mailto:${email}?subject=${subject}&body=${body}`;
    document.getElementById('contactModal').classList.remove('hidden');
}

function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
}

function restrictCustomer(customerId, name) {
    currentCustomerId = customerId;
    document.getElementById('restrictCustomerName').textContent = name;
    document.getElementById('restrictModal').classList.remove('hidden');
}

function closeRestrictModal() {
    document.getElementById('restrictModal').classList.add('hidden');
}

function applyRestriction() {
    const restrictionType = document.getElementById('restrictionType').value;
    
    // Implementation for applying restriction
    console.log(`Applying ${restrictionType} to customer ${currentCustomerId}`);
    
    // Show success message (could be replaced with actual API call)
    alert('🚫 Restriction applied successfully');
    closeRestrictModal();
    
    // Optional: Update the UI to reflect the restriction
    // This could involve updating the row to show the restriction status
}

// Auto-submit form when filter values change for better UX
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const selects = form?.querySelectorAll('select');
    
    selects?.forEach(select => {
        select.addEventListener('change', function() {
            // Add a small delay to prevent rapid submissions
            clearTimeout(window.autoSubmitTimeout);
            window.autoSubmitTimeout = setTimeout(() => {
                form.submit();
            }, 500);
        });
    });
});

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const contactModal = document.getElementById('contactModal');
    const restrictModal = document.getElementById('restrictModal');
    
    if (event.target === contactModal) {
        closeContactModal();
    }
    if (event.target === restrictModal) {
        closeRestrictModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeContactModal();
        closeRestrictModal();
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customer-behavior/flagged.blade.php ENDPATH**/ ?>