<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - KTL Booking System</title>
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
        .logo-text {
            font-size: 42px;
            font-weight: 900;
            letter-spacing: 4px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            margin-bottom: 5px;
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
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #dc2626;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .reset-button {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button a {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s;
        }
        .reset-button a:hover {
            transform: translateY(-2px);
        }
        .security-notice {
            background: #fef2f2;
            border-left: 4px solid #dc2626;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
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
            <div class="greeting">Hello <?php echo e($user->name); ?>,</div>
            
            <div class="message">
                We received a request to reset the password for your KTL Booking System account. 
                If you made this request, please click the button below to create a new password.
            </div>
            
            <div class="reset-button">
                <a href="<?php echo e($resetUrl); ?>">Reset Your Password</a>
            </div>
            
            <div class="security-notice">
                <strong>Security Notice:</strong> This password reset link will expire in 60 minutes for your security. 
                If you didn't request this password reset, please ignore this email - your account remains secure.
            </div>
            
            <div class="message">
                If you're having trouble clicking the button above, copy and paste the following link into your web browser:
                <br><br>
                <a href="<?php echo e($resetUrl); ?>" style="color: #dc2626; word-break: break-all;"><?php echo e($resetUrl); ?></a>
            </div>
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
</html><?php /**PATH /Users/londo/Herd/test/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>