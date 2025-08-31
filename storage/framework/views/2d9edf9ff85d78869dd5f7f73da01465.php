<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Summary Report</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 10px;
            margin: 20px;
            color: #333;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0; 
            font-size: 18px; 
            color: #2563eb;
        }
        .header p { 
            margin: 5px 0; 
            color: #666; 
        }
        
        .summary-section {
            margin-bottom: 25px;
        }
        .summary-section h2 {
            font-size: 14px;
            color: #1f2937;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .summary-row {
            display: table-row;
        }
        .summary-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .summary-cell:first-child {
            background-color: #f9fafb;
            font-weight: bold;
            width: 25%;
        }
        
        .bookings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        .bookings-table th,
        .bookings-table td {
            border: 1px solid #e5e7eb;
            padding: 6px;
            text-align: left;
        }
        .bookings-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .bookings-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .depot-header {
            background-color: #2563eb !important;
            color: white !important;
            text-align: center;
            font-weight: bold;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-top: 20px;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        .stats-number {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
        }
        .stats-label {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
        
        .status-arrived { color: #10b981; font-weight: bold; }
        .status-pending { color: #f59e0b; font-weight: bold; }
        .status-late { color: #ef4444; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Booking Summary Report</h1>
        <p><strong>Generated:</strong> <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
        <p><strong>Report Period:</strong> <?php echo e($filterDescription); ?></p>
        <p><strong>Total Records:</strong> <?php echo e(number_format($bookings->count())); ?></p>
    </div>

    
    <div class="summary-section">
        <h2>📈 Executive Summary</h2>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">Total Bookings</div>
                <div class="summary-cell"><?php echo e(number_format($summaryData['totalBookings'])); ?></div>
                <div class="summary-cell">Expected Cases</div>
                <div class="summary-cell"><?php echo e(number_format($summaryData['totalExpectedCases'])); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">Arrived</div>
                <div class="summary-cell"><?php echo e(number_format($summaryData['arrivedCount'])); ?> (<?php echo e(number_format($summaryData['arrivedPercentage'], 1)); ?>%)</div>
                <div class="summary-cell">Actual Cases</div>
                <div class="summary-cell"><?php echo e(number_format($summaryData['totalActualCases'])); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">Late Arrivals</div>
                <div class="summary-cell"><?php echo e(number_format($summaryData['lateCount'])); ?> (<?php echo e(number_format($summaryData['latePercentage'], 1)); ?>%)</div>
                <div class="summary-cell">Case Variance</div>
                <div class="summary-cell"><?php echo e($summaryData['totalCaseVariance'] >= 0 ? '+' : ''); ?><?php echo e(number_format($summaryData['totalCaseVariance'])); ?></div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">Outstanding</div>
                <div class="summary-cell"><?php echo e(number_format($summaryData['outstandingCount'])); ?> (<?php echo e(number_format($summaryData['outstandingPercentage'], 1)); ?>%)</div>
                <div class="summary-cell">Pallet Variance</div>
                <div class="summary-cell"><?php echo e($summaryData['totalPalletVariance'] >= 0 ? '+' : ''); ?><?php echo e(number_format($summaryData['totalPalletVariance'])); ?></div>
            </div>
        </div>
    </div>

    
    <?php if(count($summaryData['depotBreakdown']) > 1): ?>
    <div class="summary-section">
        <h2>🏭 Depot Breakdown</h2>
        <div class="bookings-table">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th>Depot</th>
                        <th>Total</th>
                        <th>Arrived</th>
                        <th>Late</th>
                        <th>Outstanding</th>
                        <th>Exp Cases</th>
                        <th>Act Cases</th>
                        <th>Case Δ</th>
                        <th>Pallet Δ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $summaryData['depotBreakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><strong><?php echo e($depot); ?></strong></td>
                        <td><?php echo e(number_format($data['total'])); ?></td>
                        <td class="status-arrived"><?php echo e(number_format($data['arrived'])); ?></td>
                        <td class="status-late"><?php echo e(number_format($data['late'])); ?></td>
                        <td class="status-pending"><?php echo e(number_format($data['outstanding'])); ?></td>
                        <td><?php echo e(number_format($data['expectedCases'])); ?></td>
                        <td><?php echo e(number_format($data['actualCases'])); ?></td>
                        <td><?php echo e($data['caseVariance'] >= 0 ? '+' : ''); ?><?php echo e(number_format($data['caseVariance'])); ?></td>
                        <td><?php echo e($data['palletVariance'] >= 0 ? '+' : ''); ?><?php echo e(number_format($data['palletVariance'])); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    
    <div class="summary-section">
        <h2>📋 Detailed Booking List</h2>
        <table class="bookings-table">
            <thead>
                <tr>
                    <th width="8%">ID</th>
                    <th width="12%">Reference</th>
                    <th width="12%">Customer</th>
                    <th width="12%">Scheduled</th>
                    <th width="10%">Status</th>
                    <th width="10%">Tipping</th>
                    <th width="16%">PO Numbers</th>
                    <th width="10%">Expected</th>
                    <th width="10%">Actual</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $bookings->groupBy(fn($b) => $b->slot->depot->name); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td colspan="9" class="depot-header">🏭 <?php echo e($depotName); ?></td>
                </tr>
                <?php $__currentLoopData = $group->sortBy(fn($b) => $b->slot->start_at); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($booking->id); ?></td>
                    <td><?php echo e($booking->booking_reference ?? 'N/A'); ?></td>
                    <td><?php echo e($booking->customer->name ?? 'N/A'); ?></td>
                    <td><?php echo e($booking->slot->start_at->format('d/m/Y H:i')); ?></td>
                    <td>
                        <?php if($booking->cancelled_at): ?>
                            <span class="status-late">❌ Cancelled</span>
                        <?php elseif($booking->arrived_at): ?>
                            <?php if($booking->departed_at): ?>
                                <span class="status-arrived">✅ Completed</span>
                            <?php else: ?>
                                <span class="status-arrived">🏢 On-site</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="status-pending">⏳ Scheduled</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            $tippingStatus = $booking->getCurrentMovementStatus();
                            $statusEmoji = match ($tippingStatus) {
                                'scheduled' => '⏳',
                                'en_route' => '🚛',
                                'arrived' => '📍',
                                'in_waiting' => '⏸️',
                                'trailer_dropped' => '📍',
                                'at_bay' => '🚛',
                                'unloading' => '⚡',
                                'empty' => '✅',
                                'loading' => '⚡',
                                'loaded' => '📦',
                                'ready_to_depart' => '🚀',
                                'departed' => '🏁',
                                'trailer_collected' => '🔄',
                                default => '❓'
                            };
                        ?>
                        <?php echo e($statusEmoji); ?> <?php echo e(ucwords(str_replace('_', ' ', $tippingStatus))); ?>

                    </td>
                    <td>
                        <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                            <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="margin-bottom: 2px;">
                                    <strong><?php echo e($po->po_number); ?></strong>
                                    <?php if($po->lines->count() > 0): ?>
                                        <br><small>(<?php echo e($po->lines->count()); ?> lines, <?php echo e($po->total_expected_units); ?>u, <?php echo e($po->total_expected_pallets); ?>p)</small>
                                    <?php endif; ?>
                                    <?php if($po->hasVariance()): ?>
                                        <span style="color: #ef4444;">⚠️</span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <span style="color: #666;">❌ No PO Numbers</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                            <?php echo e($booking->total_expected_cases ?? 0); ?>u<br>
                            <?php echo e($booking->total_expected_pallets ?? 0); ?>p
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                            <?php
                                $unitVariance = ($booking->total_actual_cases ?? 0) - ($booking->total_expected_cases ?? 0);
                                $palletVariance = ($booking->total_actual_pallets ?? 0) - ($booking->total_expected_pallets ?? 0);
                                $unitColor = $unitVariance == 0 ? '#10b981' : ($unitVariance > 0 ? '#3b82f6' : '#ef4444');
                                $palletColor = $palletVariance == 0 ? '#10b981' : ($palletVariance > 0 ? '#3b82f6' : '#ef4444');
                            ?>
                            <span style="color: <?php echo e($unitColor); ?>"><?php echo e($booking->total_actual_cases ?? '-'); ?>u</span>
                            <?php if($unitVariance != 0): ?>
                                <small style="color: <?php echo e($unitColor); ?>">(<?php echo e($unitVariance > 0 ? '+' : ''); ?><?php echo e($unitVariance); ?>)</small>
                            <?php endif; ?>
                            <br>
                            <span style="color: <?php echo e($palletColor); ?>"><?php echo e($booking->total_actual_pallets ?? '-'); ?>p</span>
                            <?php if($palletVariance != 0): ?>
                                <small style="color: <?php echo e($palletColor); ?>">(<?php echo e($palletVariance > 0 ? '+' : ''); ?><?php echo e($palletVariance); ?>)</small>
                            <?php endif; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
                    <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $po): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($po->lines->count() > 0): ?>
                            <tr style="background-color: #f8fafc;">
                                <td colspan="2"></td>
                                <td colspan="7" style="font-size: 8px; color: #666;">
                                    <strong><?php echo e($po->po_number); ?> Lines:</strong>
                                    <?php $__currentLoopData = $po->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        Line <?php echo e($line->line_number); ?>: <?php echo e($line->expected_cases); ?> → <?php echo e($line->actual_cases ?? '-'); ?> units, 
                                        <?php echo e($line->expected_pallets); ?> → <?php echo e($line->actual_pallets ?? '-'); ?> <?php echo e($line->expectedPalletType->name ?? 'pallets'); ?>

                                        <?php if($line->hasVariance()): ?>⚠️<?php endif; ?><?php echo e(!$loop->last ? ' | ' : ''); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>

    
    <div class="stats-grid">
        <div class="stats-row">
            <div class="stats-cell">
                <div class="stats-number"><?php echo e(number_format($summaryData['arrivedPercentage'], 1)); ?>%</div>
                <div class="stats-label">Arrival Rate</div>
            </div>
            <div class="stats-cell">
                <div class="stats-number"><?php echo e(number_format($summaryData['latePercentage'], 1)); ?>%</div>
                <div class="stats-label">Late Rate</div>
            </div>
            <div class="stats-cell">
                <div class="stats-number"><?php echo e(number_format($summaryData['totalBookings'])); ?></div>
                <div class="stats-label">Total Slots</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Report generated by Warehouse Management System on <?php echo e(now()->format('d/m/Y H:i:s')); ?></p>
        <p>Data includes all bookings matching the specified filter criteria</p>
    </div>
</body>
</html><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/report-summary.blade.php ENDPATH**/ ?>