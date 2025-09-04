<?php $__env->startSection('content'); ?>
<style>
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
</style>

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('emails.layouts.ktl-layout', ['subject' => 'Password Reset - KTL Booking System'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/emails/password-reset.blade.php ENDPATH**/ ?>