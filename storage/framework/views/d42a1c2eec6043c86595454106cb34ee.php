<?php $__env->startSection('content'); ?>
<style>
    .alert-box {
        background: #fef2f2;
        border: 2px solid #dc2626;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    .user-details {
        background: #f8fafc;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600;
        color: #374151;
    }
    .detail-value {
        color: #6b7280;
    }
    .action-button {
        display: inline-block;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        text-decoration: none;
        padding: 15px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        text-align: center;
        margin: 20px 0;
    }
    .action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    .instructions {
        background: #f0f9ff;
        border-left: 4px solid #3b82f6;
        padding: 15px;
        margin: 20px 0;
        font-size: 14px;
    }
</style>

<div class="alert-box">
    <h2 style="color: #dc2626; margin-top: 0;">⚠️ New User Registration</h2>
    <p style="margin-bottom: 0;">A new user has registered for the KTL Booking System and requires administrator approval to access the system.</p>
</div>

<div class="user-details">
    <h3 style="color: #374151; margin-top: 0;">User Details</h3>
    <div class="detail-row">
        <span class="detail-label">Full Name:</span>
        <span class="detail-value"><?php echo e($newUser->name); ?></span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Email Address:</span>
        <span class="detail-value"><?php echo e($newUser->email); ?></span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Registration Date:</span>
        <span class="detail-value"><?php echo e($newUser->created_at->format('d M Y H:i')); ?></span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Account Status:</span>
        <span class="detail-value" style="color: #d97706;">⏳ Pending Approval</span>
    </div>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="<?php echo e($adminUrl); ?>" class="action-button">
        👥 Manage User Access
    </a>
</div>

<div class="instructions">
    <h4 style="margin-top: 0; color: #1e40af;">Next Steps:</h4>
    <ol style="margin: 10px 0;">
        <li>Review the user's registration details above</li>
        <li>Click the "Manage User Access" button to go to the admin dashboard</li>
        <li>Navigate to user management to assign appropriate roles</li>
        <li>Assign depot access based on the user's requirements</li>
        <li>The user will be automatically notified when access is granted</li>
    </ol>
    <p style="margin-bottom: 0;"><strong>Note:</strong> Until roles and depot access are assigned, the user will see a "pending access" page when they log in.</p>
</div>

<p style="color: #6b7280; font-size: 14px;">
    This notification was sent because your email address is configured to receive new user registration alerts. 
    To modify these settings, visit the Admin Settings Dashboard.
</p>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('emails.layouts.ktl-layout', ['subject' => 'New User Registration Approval Required'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/emails/new-user-registration.blade.php ENDPATH**/ ?>