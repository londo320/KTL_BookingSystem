<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($subject ?? 'KTL Booking System'); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .logo {
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .tagline {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .footer {
            background: #f8fafc;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .company-info {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <img src="<?php echo e($message->embed(public_path('images/ktl_logo_email.png'))); ?>" alt="KTL Logo" style="height: 60px; max-width: 200px;">
            </div>
            <div class="company-name">Booking System</div>
            <div class="tagline">Knowles Logistics</div>
        </div>
        
        <div class="content">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
        
        <div class="footer">
            <div>This email was sent by KTL Booking System</div>
            <div class="company-info">
                Knowles Logistics<br>
                Always Evolving
            </div>
        </div>
    </div>
</body>
</html><?php /**PATH /Users/londo/Herd/test/resources/views/emails/layouts/ktl-layout.blade.php ENDPATH**/ ?>