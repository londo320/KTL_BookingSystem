<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email}';

    protected $description = 'Test email configuration';

    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('This is a test email from the booking system.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email from Booking System');
            });

            $this->info('Test email sent successfully to '.$email);

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: '.$e->getMessage());

            return 1;
        }
    }
}
