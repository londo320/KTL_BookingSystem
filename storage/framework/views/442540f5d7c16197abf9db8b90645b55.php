<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Details</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">Booking Details #<?php echo e($booking->id); ?></h2>
        
        <?php if(isset($customMessage) && $customMessage): ?>
            <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #2563eb; margin-bottom: 20px;">
                <p><strong>Message:</strong></p>
                <p><?php echo e($customMessage); ?></p>
            </div>
        <?php endif; ?>
        
        <p>Please find attached the detailed booking information for:</p>
        
        <ul style="background: #f8f9fa; padding: 15px; border-radius: 5px;">
            <li><strong>Booking ID:</strong> #<?php echo e($booking->id); ?></li>
            <li><strong>Customer:</strong> <?php echo e($booking->customer->name ?? 'Not assigned'); ?></li>
            <li><strong>Depot:</strong> <?php echo e($booking->slot->depot->name); ?></li>
            <li><strong>Date & Time:</strong> <?php echo e($booking->slot->start_at->format('l, d F Y - H:i')); ?> - <?php echo e($booking->slot->end_at->format('H:i')); ?></li>
            <li><strong>Status:</strong> 
                <?php if($booking->arrived_at): ?>
                    <?php if($booking->departed_at): ?>
                        Completed
                    <?php else: ?>
                        On-site
                    <?php endif; ?>
                <?php else: ?>
                    <?php if($booking->slot->locked_at && $booking->slot->locked_at->isPast()): ?>
                        Locked
                    <?php else: ?>
                        Active
                    <?php endif; ?>
                <?php endif; ?>
            </li>
        </ul>
        
        <p>The attached PDF contains complete booking details including load information, transportation details, and arrival status.</p>
        
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        
        <p style="font-size: 12px; color: #666;">
            This email was generated automatically from the booking management system.<br>
            Generated on <?php echo e(now()->format('d M Y, H:i')); ?>

        </p>
    </div>
</body>
</html><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/email.blade.php ENDPATH**/ ?>