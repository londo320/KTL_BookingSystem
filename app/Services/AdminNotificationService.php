<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Notifications\NewUserRegistrationNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AdminNotificationService
{
    /**
     * Send new user registration notification to configured admin emails
     */
    public static function sendNewUserRegistration(User $user)
    {
        $adminEmails = Setting::get('admin_approval_emails', '');
        
        if (empty($adminEmails)) {
            return; // No admin emails configured
        }
        
        // Parse email addresses
        $emailArray = array_map('trim', explode(',', $adminEmails));
        $validEmails = array_filter($emailArray, function($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });
        
        if (empty($validEmails)) {
            return; // No valid emails found
        }
        
        // Send notification to each admin email
        Notification::route('mail', $validEmails)
            ->notify(new NewUserRegistrationNotification($user));
    }
}