<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestSmtpConnection extends Command
{
    protected $signature = 'smtp:test';

    protected $description = 'Test SMTP connection';

    public function handle()
    {
        try {
            Mail::raw('Test email from Laravel', function ($message) {
                $message->to('nurundin2010@gmail.com')->subject('SMTP Test');
            });

            $this->info('Test email sent successfully!');
        } catch (\Exception $e) {
    $unique_id = floor(time() - 999999999);
Log::error('An error occurred with id ' . $unique_id);
Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            $this->error('Failed to send test email. Error: ' . $e->getMessage());
        }
    }
}
