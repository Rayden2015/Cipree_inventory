<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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

Log::channel('error_log')->error('TestSmtpController | Handle() Error ' . $unique_id, [
    'message' => $e->getMessage(),
    'stack_trace' => $e->getTraceAsString()
]);
    }
    }
}