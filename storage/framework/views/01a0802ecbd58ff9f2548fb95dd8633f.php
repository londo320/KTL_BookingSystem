<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details #<?php echo e($booking->id); ?></title>
    <style>
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
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
        
        /* PDF-friendly icons using symbols */
        .status-icon { 
            font-weight: bold; 
            font-size: 14px; 
            margin-right: 5px;
            display: inline-block;
        }
        .arrived { color: #22c55e; }
        .locked { color: #f59e0b; }
        .active { color: #0ea5e9; }
        .hazmat { color: #dc2626; }
        .section-icon {
            font-weight: bold;
            margin-right: 5px;
            color: #666;
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
            <strong><span class="status-icon arrived">[\u2713]</span> Vehicle Arrived</strong><br>
            Arrived: <?php echo e($booking->arrived_at->format('d M Y, H:i')); ?>

            <?php if($booking->departed_at): ?>
                | Departed: <?php echo e($booking->departed_at->format('d M Y, H:i')); ?>

            <?php else: ?>
                | Currently on-site
            <?php endif; ?>
        </div>
    <?php elseif($isLocked): ?>
        <div class="status-banner locked">
            <strong><span class="status-icon locked">[LOCKED]</span> Booking Locked</strong><br>
            Cut-off time: <?php echo e($booking->slot->locked_at->format('d M Y, H:i')); ?>

        </div>
    <?php else: ?>
        <div class="status-banner">
            <strong><span class="status-icon active">[ACTIVE]</span> Booking Active</strong><br>
            This booking is active and can be edited.
        </div>
    <?php endif; ?>

    <div class="info-grid">
        <div class="section">
            <div class="section-title"><span class="section-icon">[INFO]</span> Booking Information</div>
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
            <div class="section-title"><span class="section-icon">[PIN]</span> Slot & Location</div>
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
        <div class="section-title"><span class="section-icon">[LOAD]</span> Load Details</div>
        
        <?php if($booking->expected_cases || $booking->actual_cases): ?>
            <div class="load-comparison">
                <div>
                    <span class="label">Cases Expected:</span>
                    <span class="value"><?php echo e(number_format($booking->expected_cases ?? 0)); ?></span>
                </div>
                <?php if($booking->actual_cases): ?>
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value"><?php echo e(number_format($booking->actual_cases)); ?></span>
                        <?php
                            $caseDiff = $booking->actual_cases - ($booking->expected_cases ?? 0);
                        ?>
                        <?php if($caseDiff != 0): ?>
                            <span class="variance <?php echo e($caseDiff > 0 ? 'positive' : 'negative'); ?>">
                                (<?php echo e($caseDiff > 0 ? '+' : ''); ?><?php echo e(number_format($caseDiff)); ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                <?php elseif($hasArrived): ?>
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value" style="color: #666;">Not recorded</span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if($booking->expected_pallets || $booking->actual_pallets): ?>
            <div class="load-comparison">
                <div>
                    <span class="label">Pallets Expected:</span>
                    <span class="value"><?php echo e(number_format($booking->expected_pallets ?? 0)); ?></span>
                </div>
                <?php if($booking->actual_pallets): ?>
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value"><?php echo e(number_format($booking->actual_pallets)); ?></span>
                        <?php
                            $palletDiff = $booking->actual_pallets - ($booking->expected_pallets ?? 0);
                        ?>
                        <?php if($palletDiff != 0): ?>
                            <span class="variance <?php echo e($palletDiff > 0 ? 'positive' : 'negative'); ?>">
                                (<?php echo e($palletDiff > 0 ? '+' : ''); ?><?php echo e(number_format($palletDiff)); ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                <?php elseif($hasArrived): ?>
                    <span class="arrow">→</span>
                    <div>
                        <span class="label">Actual:</span>
                        <span class="value" style="color: #666;">Not recorded</span>
                    </div>
                <?php endif; ?>
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
                <span class="value" style="color: #dc2626; font-weight: bold;"><span class="hazmat">[!]</span> Hazardous Materials (HAZMAT)</span>
            </div>
        <?php endif; ?>
        
        <?php if($booking->temperature_requirements): ?>
            <div class="info-item">
                <span class="label">Temperature:</span>
                <span class="value"><?php echo e($booking->temperature_requirements); ?></span>
            </div>
        <?php endif; ?>
    </div>

    <?php if($booking->vehicle_registration || $booking->carrier_company): ?>
        <div class="section">
            <div class="section-title"><span class="section-icon">[TRUCK]</span> Transportation</div>
            
            <?php if($booking->vehicle_registration): ?>
                <div class="info-item">
                    <span class="label">Vehicle Registration:</span>
                    <span class="value"><?php echo e($booking->vehicle_registration); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if($booking->container_number): ?>
                <div class="info-item">
                    <span class="label">Container Number:</span>
                    <span class="value"><?php echo e($booking->container_number); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if($booking->carrier_company): ?>
                <div class="info-item">
                    <span class="label">Carrier Company:</span>
                    <span class="value"><?php echo e($booking->carrier_company); ?></span>
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

    <?php if($booking->special_instructions || $booking->notes): ?>
        <div class="section">
            <div class="section-title"><span class="section-icon">[NOTES]</span> Additional Information</div>
            
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
            <div class="section-title"><span class="section-icon arrived">[ARRIVAL]</span> Arrival Information</div>
            
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
                    <span class="value" style="color: #2563eb; font-weight: bold;"><span class="section-icon">[ON-SITE]</span> Currently on-site</span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bookings/pdf-mpdf.blade.php ENDPATH**/ ?>