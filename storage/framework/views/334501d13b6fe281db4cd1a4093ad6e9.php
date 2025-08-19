<?php $__env->startSection('title', 'Customer Analysis - ' . $customer->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    📊 Customer Analysis: <?php echo e($customer->name); ?>

                </h1>
                <p class="mt-2 text-gray-600">
                    <?php if($customer->users && $customer->users->count() > 0): ?>
                        👤 Users: <?php echo e($customer->users->pluck('email')->join(', ')); ?>

                    <?php else: ?>
                        👤 No users assigned to this customer
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <a href="<?php echo e(route('admin.customer-behavior.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Analysis
                </a>
            </div>
        </div>
    </div>

    <!-- Time Period and Behavior Filters -->
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📅 Analysis Period & Behavior Filters</h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="days" class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                    <select name="days" id="days" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                        <option value="30" <?php echo e($days == 30 ? 'selected' : ''); ?>>📅 Last 30 days</option>
                        <option value="60" <?php echo e($days == 60 ? 'selected' : ''); ?>>📅 Last 60 days</option>
                        <option value="90" <?php echo e($days == 90 ? 'selected' : ''); ?>>📅 Last 90 days</option>
                        <option value="180" <?php echo e($days == 180 ? 'selected' : ''); ?>>📅 Last 6 months</option>
                        <option value="365" <?php echo e($days == 365 ? 'selected' : ''); ?>>📅 Last year</option>
                    </select>
                </div>
                <div>
                    <label for="filter" class="block text-sm font-medium text-gray-700 mb-2">Behavior Filter</label>
                    <select name="filter" id="filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                        <option value="all" <?php echo e($filter == 'all' ? 'selected' : ''); ?>>📊 All Actions</option>
                        <option value="bad" <?php echo e($filter == 'bad' ? 'selected' : ''); ?>>🚨 Bad Behavior</option>
                        <option value="good" <?php echo e($filter == 'good' ? 'selected' : ''); ?>>✅ Good Behavior</option>
                        <option value="late" <?php echo e($filter == 'late' ? 'selected' : ''); ?>>⏰ Late Arrivals</option>
                        <option value="early" <?php echo e($filter == 'early' ? 'selected' : ''); ?>>⏪ Early Arrivals</option>
                        <option value="on_time" <?php echo e($filter == 'on_time' ? 'selected' : ''); ?>>🎯 On-Time Arrivals</option>
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

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-8 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-600"><?php echo e($customerStats['bookings_created']); ?></div>
            <div class="text-sm text-gray-600 mt-1">📅 Created</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-amber-600"><?php echo e($customerStats['total_rebooks']); ?></div>
            <div class="text-sm text-gray-600 mt-1">🔄 Rebooks</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-cyan-600"><?php echo e($customerStats['total_cancellations']); ?></div>
            <div class="text-sm text-gray-600 mt-1">❌ Cancellations</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-red-600"><?php echo e($customerStats['last_minute_actions']); ?></div>
            <div class="text-sm text-gray-600 mt-1">⏰ Last Minute</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-orange-600"><?php echo e($customerStats['late_arrivals']); ?></div>
            <div class="text-sm text-gray-600 mt-1">🕐 Late</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-blue-500"><?php echo e($customerStats['early_arrivals']); ?></div>
            <div class="text-sm text-gray-600 mt-1">⏪ Early</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-green-600"><?php echo e($customerStats['on_time_arrivals']); ?></div>
            <div class="text-sm text-gray-600 mt-1">🎯 On-Time</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-6 text-center">
            <div class="text-3xl font-bold text-purple-600"><?php echo e($customerStats['late_arrival_rate']); ?>%</div>
            <div class="text-sm text-gray-600 mt-1">📊 Late Rate</div>
        </div>
    </div>

    <!-- Detailed Analysis -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Notice Time Analysis -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">⏰ Notice Time Analysis</h3>
                <p class="text-sm text-gray-600 mt-1">How much advance notice does this customer typically provide</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo e($customerStats['max_hours_notice']); ?>h</div>
                        <div class="text-sm text-gray-600">📈 Maximum Notice</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-amber-600"><?php echo e($customerStats['avg_hours_notice']); ?>h</div>
                        <div class="text-sm text-gray-600">📊 Average Notice</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600"><?php echo e($customerStats['min_hours_notice']); ?>h</div>
                        <div class="text-sm text-gray-600">📉 Minimum Notice</div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <?php
                        $lastMinutePercentage = $customerStats['total_actions'] > 0 ? 
                            ($customerStats['last_minute_actions'] / $customerStats['total_actions']) * 100 : 0;
                    ?>
                    <div class="bg-gray-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-red-500 h-full flex items-center justify-center text-white text-sm font-medium transition-all duration-500" 
                             style="width: <?php echo e($lastMinutePercentage); ?>%">
                            <?php echo e(round($lastMinutePercentage, 1)); ?>% Last Minute
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">⚠️ Percentage of last-minute actions (&lt;24h notice)</p>
                </div>
            </div>
        </div>
        
        <!-- Action Breakdown Chart -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">📊 Action Breakdown</h3>
                <p class="text-sm text-gray-600 mt-1">Distribution of booking activities for this customer</p>
            </div>
            <div class="p-6">
                <div class="h-64 flex items-center justify-center">
                    <canvas id="actionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Patterns (if available) -->
    <?php if(count($patterns['weekly']) > 0): ?>
    <div class="bg-white rounded-lg shadow-sm border mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">📅 Weekly Activity Pattern</h3>
            <p class="text-sm text-gray-600 mt-1">Booking activity trends over the past weeks</p>
        </div>
        <div class="p-6">
            <div class="h-64">
                <canvas id="weeklyChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Activity History -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">📋 Customer Behavior History (<?php echo e($days); ?> days)</h3>
            <div class="flex items-center justify-between mt-1">
                <p class="text-sm text-gray-600">Customer-initiated booking actions (excludes internal operational movements)</p>
                <div class="flex items-center gap-2">
                    <?php
                        $filterLabels = [
                            'all' => ['label' => 'All Actions', 'class' => 'bg-blue-100 text-blue-800', 'emoji' => '📊'],
                            'bad' => ['label' => 'Bad Behavior', 'class' => 'bg-red-100 text-red-800', 'emoji' => '🚨'],
                            'good' => ['label' => 'Good Behavior', 'class' => 'bg-green-100 text-green-800', 'emoji' => '✅'],
                            'late' => ['label' => 'Late Arrivals', 'class' => 'bg-orange-100 text-orange-800', 'emoji' => '⏰'],
                            'early' => ['label' => 'Early Arrivals', 'class' => 'bg-blue-100 text-blue-800', 'emoji' => '⏪'],
                            'on_time' => ['label' => 'On-Time Arrivals', 'class' => 'bg-green-100 text-green-800', 'emoji' => '🎯']
                        ];
                        $currentFilter = $filterLabels[$filter] ?? $filterLabels['all'];
                    ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo e($currentFilter['class']); ?>">
                        <?php echo e($currentFilter['emoji']); ?> <?php echo e($currentFilter['label']); ?>

                    </span>
                </div>
            </div>
        </div>
        <div class="overflow-hidden">
            <?php if($recentHistory->count() > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Ref</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slot Details</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Notice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $recentHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isBadBehavior = in_array($history->action, ['rebooked', 'cancelled', 'late_arrival', 'early_arrival']) || $history->is_last_minute;
                            $rowClass = '';
                            if ($isBadBehavior) {
                                if ($history->action === 'late_arrival') {
                                    $rowClass = 'bg-orange-50 border-l-4 border-orange-500';
                                } elseif ($history->action === 'early_arrival') {
                                    $rowClass = 'bg-blue-50 border-l-4 border-blue-500';
                                } elseif ($history->is_last_minute) {
                                    $rowClass = 'bg-red-50 border-l-4 border-red-500';
                                } else {
                                    $rowClass = 'bg-amber-50 border-l-4 border-amber-500';
                                }
                            } elseif ($history->action === 'on_time_arrival') {
                                $rowClass = 'bg-green-50 border-l-4 border-green-500';
                            }
                        ?>
                        <tr class="hover:bg-gray-50 <?php echo e($rowClass); ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900"><?php echo e($history->created_at->format('M j, Y')); ?></div>
                                <div class="text-sm text-gray-500"><?php echo e($history->created_at->format('H:i')); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php
                                    $actionClasses = [
                                        'created' => 'bg-green-100 text-green-800',
                                        'rebooked' => 'bg-amber-100 text-amber-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'late_arrival' => 'bg-orange-100 text-orange-800',
                                        'early_arrival' => 'bg-blue-100 text-blue-800',
                                        'on_time_arrival' => 'bg-green-100 text-green-800',
                                        'updated' => 'bg-blue-100 text-blue-800'
                                    ];
                                    $actionClass = $actionClasses[$history->action] ?? 'bg-gray-100 text-gray-800';
                                    
                                    $actionEmojis = [
                                        'created' => '📅',
                                        'rebooked' => '🔄',
                                        'cancelled' => '❌',
                                        'completed' => '✅',
                                        'late_arrival' => '🕐',
                                        'early_arrival' => '⏪',
                                        'on_time_arrival' => '🎯',
                                        'updated' => '📝'
                                    ];
                                    $emoji = $actionEmojis[$history->action] ?? '📋';
                                ?>
                                <div class="flex flex-col items-center gap-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($actionClass); ?>">
                                        <?php echo e($emoji); ?> <?php echo e(ucfirst($history->action)); ?>

                                    </span>
                                    <?php if($history->is_last_minute): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800" title="Last minute action">
                                            ⚠️ Last Min
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if($history->booking): ?>
                                    <a href="<?php echo e(route('admin.bookings.show', $history->booking)); ?>" 
                                       class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                        📋 <?php echo e($history->booking->booking_reference); ?>

                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <?php if($history->originalSlot): ?>
                                        <div class="text-gray-600 mb-1">
                                            <strong>From:</strong> <?php echo e($history->originalSlot->start_at->format('M j H:i')); ?>

                                            <br>
                                            🏢 <?php echo e($history->originalSlot->depot->name ?? ''); ?>

                                        </div>
                                    <?php endif; ?>
                                    <?php if($history->newSlot): ?>
                                        <div class="text-green-600">
                                            <strong>To:</strong> <?php echo e($history->newSlot->start_at->format('M j H:i')); ?>

                                            <br>
                                            🏢 <?php echo e($history->newSlot->depot->name ?? ''); ?>

                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <?php if($history->hours_before_slot !== null): ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($history->is_last_minute ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'); ?>">
                                        ⏱️ <?php echo e(abs($history->hours_before_slot)); ?>h
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?php echo e($history->reason ?? '-'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-900">👤 <?php echo e($history->user->name ?? 'System'); ?></div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <?php echo e($recentHistory->appends(request()->query())->links()); ?>

            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📋</div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Customer Behavior Activity</h3>
                <p class="text-gray-600">No customer-initiated booking actions found for the selected <?php echo e($days); ?>-day period.</p>
                <p class="text-sm text-gray-500 mt-2">This customer has not made rebooks, cancellations, or other behavior-relevant actions in this timeframe.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Modern chart styling with better colors
Chart.defaults.font.family = '"Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.color = '#6B7280';

// Action Breakdown Chart with modern styling
const actionCtx = document.getElementById('actionChart').getContext('2d');
new Chart(actionCtx, {
    type: 'doughnut',
    data: {
        labels: ['📅 Bookings Created', '🔄 Rebooks', '❌ Cancellations', '✅ Completed', '🕐 Late Arrivals'],
        datasets: [{
            data: [
                <?php echo e($customerStats['bookings_created']); ?>,
                <?php echo e($customerStats['total_rebooks']); ?>,
                <?php echo e($customerStats['total_cancellations']); ?>,
                <?php echo e($customerStats['completed_bookings']); ?>,
                <?php echo e($customerStats['late_arrivals']); ?>

            ],
            backgroundColor: [
                '#2563EB', // Blue
                '#F59E0B', // Amber
                '#06B6D4', // Cyan
                '#10B981', // Green
                '#EA580C'  // Orange
            ],
            borderColor: '#ffffff',
            borderWidth: 3,
            hoverBorderWidth: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 12,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: '#1F2937',
                titleColor: '#F9FAFB',
                bodyColor: '#F9FAFB',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '60%',
        animation: {
            animateRotate: true,
            animateScale: true,
            duration: 1000,
            easing: 'easeOutCubic'
        }
    }
});

<?php if(count($patterns['weekly']) > 0): ?>
// Weekly Pattern Chart with modern styling
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'line',
    data: {
        labels: [
            <?php $__currentLoopData = $patterns['weekly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                'Week <?php echo e($week->week_number); ?>',
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ],
        datasets: [{
            label: '📊 Total Actions',
            data: [
                <?php $__currentLoopData = $patterns['weekly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($week->actions); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            borderColor: '#2563EB',
            backgroundColor: 'rgba(37, 99, 235, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#2563EB',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }, {
            label: '🔄 Changes (Rebooks/Cancels)',
            data: [
                <?php $__currentLoopData = $patterns['weekly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($week->changes); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            borderColor: '#F59E0B',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#F59E0B',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }, {
            label: '⚠️ Last Minute',
            data: [
                <?php $__currentLoopData = $patterns['weekly']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($week->last_minute); ?>,
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            ],
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#EF4444',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        plugins: {
            legend: {
                position: 'top',
                align: 'start',
                labels: {
                    padding: 20,
                    usePointStyle: true,
                    font: {
                        size: 12,
                        weight: '500'
                    }
                }
            },
            tooltip: {
                backgroundColor: '#1F2937',
                titleColor: '#F9FAFB',
                bodyColor: '#F9FAFB',
                borderColor: '#374151',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true
            }
        },
        scales: {
            x: {
                grid: {
                    color: '#E5E7EB',
                    drawTicks: false
                },
                ticks: {
                    color: '#6B7280',
                    font: {
                        size: 11,
                        weight: '500'
                    },
                    padding: 10
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: '#E5E7EB',
                    drawTicks: false
                },
                ticks: {
                    color: '#6B7280',
                    font: {
                        size: 11,
                        weight: '500'
                    },
                    padding: 10
                }
            }
        },
        animation: {
            duration: 1000,
            easing: 'easeOutCubic'
        }
    }
});
<?php endif; ?>

// Smooth scroll animation for any anchor links
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

// Add loading animation for form submissions
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const select = form?.querySelector('select');
    
    if (select) {
        select.addEventListener('change', function() {
            // Add loading state to the select
            const originalHTML = this.innerHTML;
            this.disabled = true;
            
            // Show loading message
            const loadingOption = document.createElement('option');
            loadingOption.textContent = '🔄 Loading...';
            loadingOption.selected = true;
            this.innerHTML = '';
            this.appendChild(loadingOption);
            
            // Submit form after brief delay to show loading state
            setTimeout(() => {
                this.form.submit();
            }, 200);
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customer-behavior/show.blade.php ENDPATH**/ ?>