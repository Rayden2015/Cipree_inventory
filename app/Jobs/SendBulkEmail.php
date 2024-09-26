<?php
namespace App\Jobs;

use App\Mail\BulkEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBulkEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $subject;
    protected $content;

    public function __construct($users, $subject, $content)
    {
        $this->users = $users;
        $this->subject = $subject;
        $this->content = $content;
    }

    public function handle()
    {
        foreach ($this->users as $user) {
            Mail::to($user->email)->send(new BulkEmail($this->subject, $this->content));
        }
    }
}
