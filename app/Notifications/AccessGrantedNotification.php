<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessGrantedNotification extends Notification
{
    use Queueable;

    public $roles;
    public $depots;

    /**
     * Create a new notification instance.
     */
    public function __construct($roles = null, $depots = null)
    {
        $this->roles = $roles;
        $this->depots = $depots;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = config('app.url') . '/login';
        
        return (new MailMessage)
            ->subject('Access Granted - KTL Booking System')
            ->view('emails.access-granted', [
                'user' => $notifiable,
                'loginUrl' => $loginUrl,
                'roles' => $this->roles,
                'depots' => $this->depots
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'roles' => $this->roles,
            'depots' => $this->depots,
            'granted_at' => now(),
        ];
    }
}
