@extends('emails.layouts.ktl-layout', ['subject' => 'Access Granted - KTL Booking System'])

@section('content')
<style>
    .success-box {
        background: #f0f9ff;
        border: 2px solid #3b82f6;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        text-align: center;
    }
    .access-details {
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
        color: #059669;
        font-weight: 500;
    }
    .login-button {
        display: inline-block;
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        text-decoration: none;
        padding: 15px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        text-align: center;
        margin: 30px 0;
    }
    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    .welcome-message {
        font-size: 18px;
        font-weight: 600;
        color: #dc2626;
        margin-bottom: 20px;
    }
    .message {
        font-size: 16px;
        line-height: 1.8;
        margin-bottom: 20px;
    }
    .instructions {
        background: #f0f9ff;
        border-left: 4px solid #3b82f6;
        padding: 15px;
        margin: 20px 0;
        font-size: 14px;
    }
</style>

<div class="success-box">
    <h2 style="color: #3b82f6; margin-top: 0;">🎉 Access Granted!</h2>
    <p style="margin-bottom: 0; font-size: 18px;">Your KTL Booking System access has been approved</p>
</div>

<div class="welcome-message">Welcome {{ $user->name }}!</div>

<div class="message">
    Great news! An administrator has reviewed and approved your account. You now have access to the KTL Booking System with the following permissions:
</div>

<div class="access-details">
    <h3 style="color: #374151; margin-top: 0;">Your Access Details</h3>
    <div class="detail-row">
        <span class="detail-label">Account Status:</span>
        <span class="detail-value">✅ Active</span>
    </div>
    <div class="detail-row">
        <span class="detail-label">Email Address:</span>
        <span class="detail-value">{{ $user->email }}</span>
    </div>
    @if($roles && count($roles) > 0)
    <div class="detail-row">
        <span class="detail-label">Assigned Roles:</span>
        <span class="detail-value">{{ implode(', ', $roles) }}</span>
    </div>
    @endif
    @if($depots && count($depots) > 0)
    <div class="detail-row">
        <span class="detail-label">Depot Access:</span>
        <span class="detail-value">{{ implode(', ', $depots) }}</span>
    </div>
    @endif
</div>

<div style="text-align: center;">
    <a href="{{ $loginUrl }}" class="login-button">
        🚀 Access KTL Booking System
    </a>
</div>

<div class="instructions">
    <h4 style="margin-top: 0; color: #1e40af;">Getting Started:</h4>
    <ol style="margin: 10px 0;">
        <li>Click the "Access KTL Booking System" button above</li>
        <li>Log in using your registered email address and password</li>
        <li>You'll be taken to your personalized dashboard</li>
        <li>Explore the system features based on your assigned role</li>
    </ol>
    <p style="margin-bottom: 0;"><strong>Need help?</strong> Contact your system administrator or IT support for training and assistance.</p>
</div>

<p style="color: #6b7280; font-size: 14px;">
    If you have any questions about using the system, please contact your administrator who granted you access.
</p>
@endsection