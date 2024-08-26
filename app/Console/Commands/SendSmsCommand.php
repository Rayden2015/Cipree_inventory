<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SmsController;

class SendSmsCommand extends Command
{
    protected $signature = 'sms:send';

    protected $description = 'Send an SMS message';

    public function handle()
    {
        $to = '0591557389';
        $content = 'Test from CIPREE';

        $smsController = new SmsController();
        $smsController->sendSms($to, $content);

        $this->info('SMS sent successfully!');
    }
}
