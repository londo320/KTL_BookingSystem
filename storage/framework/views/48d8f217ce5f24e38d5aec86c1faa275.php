<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details #<?php echo e($booking->id); ?></title>
    <style>
        body { 
            font-family: 'DejaVu Sans', 'Arial Unicode MS', Arial, sans-serif; 
            margin: 0; 
            padding: 20px; 
            font-size: 12px;
            line-height: 1.4;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .section { 
            margin-bottom: 15px; 
            page-break-inside: avoid;
        }
        .section-title { 
            font-size: 14px; 
            font-weight: bold; 
            margin-bottom: 8px; 
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 2px;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 10px; 
        }
        .info-item { 
            margin-bottom: 5px; 
        }
        .label { 
            font-weight: bold; 
            color: #555; 
        }
        .value { 
            margin-left: 10px; 
        }
        .status-banner {
            background: #f0f9ff;
            border: 1px solid #0ea5e9;
            padding: 10px;
            margin-bottom: 15px;
            text-align: center;
        }
        .arrived .status-banner {
            background: #f0fdf4;
            border-color: #22c55e;
        }
        .locked .status-banner {
            background: #fffbeb;
            border-color: #f59e0b;
        }
        .load-comparison {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .load-comparison .arrow {
            color: #666;
            font-weight: bold;
        }
        .variance {
            font-size: 11px;
            font-weight: bold;
        }
        .variance.positive { color: #059669; }
        .variance.negative { color: #dc2626; }
        
        /* Emoji styling for PDFs */
        .emoji {
            font-family: 'Apple Color Emoji', 'Segoe UI Emoji', 'Noto Color Emoji', 'Segoe UI Symbol', sans-serif;
            font-size: 14px;
            margin-right: 5px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Booking Details #<?php echo e($booking->id); ?></h1>
        <p>Generated on <?php echo e(now()->format('d M Y, H:i')); ?></p>
    </div>

    <?php
        $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
        $hasArrived = $booking->arrived_at;
    ?>

    <?php if($hasArrived): ?>
        <div class="status-banner arrived">
            <strong><span class="emoji">✅</span>Vehicle Arrived</strong><br>
            Arrived: <?php echo e($booking->arrived_at->format('d M Y, H:i')); ?>

            <?php if($booking->departed_at): ?>
                | Departed: <?php echo e($booking->departed_at->format('d M Y, H:i')); ?>

            <?php else: ?>
                | Currently on-site
            <?php endif; ?>
        </div>
    <?php elseif($isLocked): ?>
        <div class="status-banner locked">
            <strong><span class="emoji">🔒</span>Booking Locked</strong><br>
            Cut-off time: <?php echo e($booking->slot->locked_at->format('d M Y, H:i')); ?>

        </div>
    <?php else: ?>
        <div class="status-banner">
            <strong><span class="emoji">📅</span>Booking Active</strong><br>
            This booking is active and can be edited.
        </div>
    <?php endif; ?>

    <div class="info-grid">
        <div class="section">
            <div class="section-title"><span class="emoji">📋</span>Booking Information</div>
            <div class="info-item">
                <span class="label">Booking ID:</span>
                <span class="value">#<?php echo e($booking->id); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Customer:</span>
                <span class="value"><?php echo e($booking->customer->name ?? 'Not assigned'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Created By:</span>
                <span class="value"><?php echo e($booking->user->name ?? 'Unknown'); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Created At:</span>
                <span class="value"><?php echo e($booking->created_at->format('d M Y, H:i')); ?></span>
            </div>
            <?php if($booking->reference): ?>
                <div class="info-item">
                    <span class="label">Reference:</span>
                    <span class="value"><?php echo e($booking->reference); ?></span>
                </div>
            <?php endif; ?>
        </div>

        <div class="section">
            <div class="section-title"><span class="emoji">📍</span>Slot & Location</div>
            <div class="info-item">
                <span class="label">Depot:</span>
                <span class="value"><?php echo e($booking->slot->depot->name); ?></span>
            </div>
            <?php if($booking->slot->depot->location): ?>
                <div class="info-item">
                    <span class="label">Location:</span>
                    <span class="value"><?php echo e($booking->slot->depot->location); ?></span>
                </div>
            <?php endif; ?>
            <div class="info-item">
                <span class="label">Date:</span>
                <span class="value"><?php echo e($booking->slot->start_at->format('l, d F Y')); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Time:</span>
                <span class="value"><?php echo e($booking->slot->start_at->format('H:i')); ?> - <?php echo e($booking->slot->end_at->format('H:i')); ?></span>
            </div>
            <div class="info-item">
                <span class="label">Booking Type:</span>
                <span class="value"><?php echo e($booking->bookingType->name ?? 'Not specified'); ?></span>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title"><span class="emoji">📦</span>PO Numbers & Load Details</div>
        
        <?php if($booking->poNumbers && $booking->poNumbers->count() > 0): ?>
            <?php $__currentLoopData = $booking->poNumbers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $poNumber): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="border: 1px solid #ddd; margin-bottom: 10px; padding: 8px;">
                    <div style="font-weight: bold; margin-bottom: 5px; font-size: 13px;">
                        PO #<?php echo e($index + 1); ?>: <?php echo e($poNumber->po_number); ?>

                        <?php if($poNumber->hasVariance()): ?>
                            <span style="background: #fef2f2; color: #dc2626; padding: 2px 6px; border-radius: 3px; font-size: 10px;">Has Variance</span>
                        <?php elseif($poNumber->isComplete()): ?>
                            <span style="background: #f0fdf4; color: #059669; padding: 2px 6px; border-radius: 3px; font-size: 10px;">Complete</span>
                        <?php endif; ?>
                    </div>
                    
                    
                    <?php if($poNumber->lines->count() > 0): ?>
                        <div style="margin-bottom: 10px;">
                            <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px; color: #666;">
                                Lines (<?php echo e($poNumber->lines->count()); ?>)
                            </div>
                            <?php $__currentLoopData = $poNumber->lines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="border: 1px solid #e5e7eb; margin-bottom: 8px; padding: 6px; background: #fafafa;">
                                    <div style="font-weight: bold; margin-bottom: 3px; font-size: 11px;">
                                        Line <?php echo e($line->line_number); ?>

                                        <?php if($line->hasVariance()): ?>
                                            <span style="background: #fef2f2; color: #dc2626; padding: 1px 4px; border-radius: 2px; font-size: 9px;">Variance</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 10px;">
                                        <div>
                                            <div style="margin-bottom: 2px;">
                                                <span style="font-weight: bold; color: #555;">Cases:</span>
                                                <?php if($line->expected_cases || $line->actual_cases): ?>
                                                    <div>
                                                        <?php if($line->expected_cases): ?>
                                                            Expected: <?php echo e(number_format($line->expected_cases)); ?>

                                                        <?php endif; ?>
                                                        <?php if($line->actual_cases): ?>
                                                            <?php if($line->expected_cases): ?> → <?php endif; ?>
                                                            Actual: <?php echo e(number_format($line->actual_cases)); ?>

                                                            <?php if($line->expected_cases && $line->case_variance != 0): ?>
                                                                <span style="font-weight: bold; color: <?php echo e($line->case_variance > 0 ? '#059669' : '#dc2626'); ?>;">
                                                                    (<?php echo e($line->case_variance > 0 ? '+' : ''); ?><?php echo e($line->case_variance); ?>)
                                                                </span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    Not specified
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div style="margin-bottom: 2px;">
                                                <span style="font-weight: bold; color: #555;">Pallets:</span>
                                                <?php if($line->expected_pallets || $line->actual_pallets): ?>
                                                    <div>
                                                        <?php if($line->expected_pallets): ?>
                                                            Expected: <?php echo e(number_format($line->expected_pallets)); ?>

                                                            <?php if($line->expectedPalletType): ?> (<?php echo e($line->expectedPalletType->name); ?>)<?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php if($line->actual_pallets): ?>
                                                            <?php if($line->expected_pallets): ?> → <?php endif; ?>
                                                            Actual: <?php echo e(number_format($line->actual_pallets)); ?>

                                                            <?php if($line->actualPalletType): ?> (<?php echo e($line->actualPalletType->name); ?>)<?php endif; ?>
                                                            <?php if($line->expected_pallets && $line->pallet_variance != 0): ?>
                                                                <span style="font-weight: bold; color: <?php echo e($line->pallet_variance > 0 ? '#059669' : '#dc2626'); ?>;">
                                                                    (<?php echo e($line->pallet_variance > 0 ? '+' : ''); ?><?php echo e($line->pallet_variance); ?>)
                                                                </span>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                        <?php if($line->pallet_type_variance): ?>
                                                            <div style="color: #dc2626; font-size: 9px; margin-top: 1px;">
                                                                Type Variance: <?php echo e($line->pallet_type_variance); ?>

                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    Not specified
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 10px; color: #666; font-style: italic; font-size: 11px;">
                            No lines defined for this PO
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <?php if($booking->poNumbers->count() > 1): ?>
                <div style="border-top: 2px solid #333; padding-top: 8px; margin-top: 15px;">
                    <div style="font-weight: bold; margin-bottom: 5px;">Summary Totals</div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">Total Cases:</span>
                            <div class="load-comparison" style="margin-left: 0;">
                                <?php if($booking->total_expected_cases > 0): ?>
                                    <span>Expected: <?php echo e(number_format($booking->total_expected_cases)); ?></span>
                                <?php endif; ?>
                                <?php if($booking->total_actual_cases > 0): ?>
                                    <?php if($booking->total_expected_cases > 0): ?> → <?php endif; ?>
                                    <span>Actual: <?php echo e(number_format($booking->total_actual_cases)); ?></span>
                                    <?php if($booking->total_expected_cases > 0 && $booking->total_case_variance != 0): ?>
                                        <span class="variance <?php echo e($booking->total_case_variance > 0 ? 'positive' : 'negative'); ?>">
                                            (<?php echo e($booking->total_case_variance > 0 ? '+' : ''); ?><?php echo e($booking->total_case_variance); ?>)
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <span class="label">Total Pallets:</span>
                            <div class="load-comparison" style="margin-left: 0;">
                                <?php if($booking->total_expected_pallets > 0): ?>
                                    <span>Expected: <?php echo e(number_format($booking->total_expected_pallets)); ?></span>
                                <?php endif; ?>
                                <?php if($booking->total_actual_pallets > 0): ?>
                                    <?php if($booking->total_expected_pallets > 0): ?> → <?php endif; ?>
                                    <span>Actual: <?php echo e(number_format($booking->total_actual_pallets)); ?></span>
                                    <?php if($booking->total_expected_pallets > 0 && $booking->total_pallet_variance != 0): ?>
                                        <span class="variance <?php echo e($booking->total_pallet_variance > 0 ? 'positive' : 'negative'); ?>">
                                            (<?php echo e($booking->total_pallet_variance > 0 ? '+' : ''); ?><?php echo e($booking->total_pallet_variance); ?>)
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 20px; color: #666;">
                No PO numbers recorded for this booking
            </div>
        <?php endif; ?>
        
        <?php if($booking->container_size): ?>
            <div class="info-item">
                <span class="label">Container Size:</span>
                <span class="value"><?php echo e(number_format($booking->container_size)); ?> kg</span>
            </div>
        <?php endif; ?>
        
        <?php if($booking->load_type): ?>
            <div class="info-item">
                <span class="label">Load Type:</span>
                <span class="value"><?php echo e($booking->load_type); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($booking->hazmat): ?>
            <div class="info-item">
                <span class="label">Special Requirements:</span>
                <span class="value" style="color: #dc2626; font-weight: bold;"><span class="emoji">⚠️</span>Hazardous Materials (HAZMAT)</span>
            </div>
        <?php endif; ?>
        
        <?php if($booking->temperature_requirements): ?>
            <div class="info-item">
                <span class="label">Temperature:</span>
                <span class="value"><?php echo e($booking->temperature_requirements); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if($booking->vehicle_registration || $booking->container_number || $booking->carrier_company || $booking->trailerType): ?>
        <div class="section">
            <div class="section-title"><span class="emoji">🚛</span>Transportation & Vehicle Details</div>
            
            <div class="info-grid">
                <div>
                    <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Vehicle Information</div>
                    
                    <?php if($booking->vehicle_registration): ?>
                        <div class="info-item">
                            <span class="label">Vehicle Registration:</span>
                            <span class="value" style="font-family: monospace; background: #f3f4f6; padding: 1px 4px;"><?php echo e($booking->vehicle_registration); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($booking->carrier_company): ?>
                        <div class="info-item">
                            <span class="label">Carrier Company:</span>
                            <span class="value"><?php echo e($booking->carrier_company); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($booking->carrier_contact): ?>
                        <div class="info-item">
                            <span class="label">Carrier Contact:</span>
                            <span class="value"><?php echo e($booking->carrier_contact); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div>
                    <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Container/Trailer Details</div>
                    
                    <?php if($booking->container_number): ?>
                        <div class="info-item">
                            <span class="label">Container/Trailer Number:</span>
                            <span class="value" style="font-family: monospace; background: #f3f4f6; padding: 1px 4px;"><?php echo e($booking->container_number); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($booking->trailerType): ?>
                        <div class="info-item">
                            <span class="label">Trailer Type:</span>
                            <span class="value"><?php echo e($booking->trailerType->name); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($booking->container_size): ?>
                        <div class="info-item">
                            <span class="label">Container Size:</span>
                            <span class="value"><?php echo e($booking->container_size); ?>ft</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if($booking->gate_number || $booking->manifest_number || $booking->estimated_arrival): ?>
                <div style="border-top: 1px solid #ccc; margin-top: 10px; padding-top: 10px;">
                    <div style="font-weight: bold; margin-bottom: 5px; color: #333;">Additional Transportation Info</div>
                    
                    <?php if($booking->gate_number): ?>
                        <div class="info-item">
                            <span class="label">Gate Number:</span>
                            <span class="value"><?php echo e($booking->gate_number); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($booking->manifest_number): ?>
                        <div class="info-item">
                            <span class="label">Manifest Number:</span>
                            <span class="value" style="font-family: monospace;"><?php echo e($booking->manifest_number); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($booking->estimated_arrival): ?>
                        <div class="info-item">
                            <span class="label">Estimated Arrival:</span>
                            <span class="value"><?php echo e($booking->estimated_arrival->format('d M Y, H:i')); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($booking->special_instructions || $booking->notes): ?>
        <div class="section">
            <div class="section-title"><span class="emoji">📝</span>Additional Information</div>
            
            <?php if($booking->special_instructions): ?>
                <div class="info-item">
                    <span class="label">Special Instructions:</span>
                    <div class="value"><?php echo e($booking->special_instructions); ?></div>
                </div>
            <?php endif; ?>
            
            <?php if($booking->notes): ?>
                <div class="info-item">
                    <span class="label">Notes:</span>
                    <div class="value"><?php echo e($booking->notes); ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($hasArrived): ?>
        <div class="section">
            <div class="section-title"><span class="emoji">✅</span>Arrival Information</div>
            
            <div class="info-item">
                <span class="label">Arrived At:</span>
                <span class="value"><?php echo e($booking->arrived_at->format('l, d F Y - H:i')); ?></span>
            </div>
            
            <?php if($booking->departed_at): ?>
                <div class="info-item">
                    <span class="label">Departed At:</span>
                    <span class="value"><?php echo e($booking->departed_at->format('l, d F Y - H:i')); ?></span>
                </div>
                
                <div class="info-item">
                    <span class="label">Time On-Site:</span>
                    <span class="value"><?php echo e($booking->arrived_at->diffForHumans($booking->departed_at, true)); ?></span>
                </div>
            <?php else: ?>
                <div class="info-item">
                    <span class="label">Status:</span>
                    <span class="value" style="color: #2563eb; font-weight: bold;"><span class="emoji">🚛</span>Currently on-site</span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/pdf.blade.php ENDPATH**/ ?>