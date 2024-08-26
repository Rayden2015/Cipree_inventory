<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoggedinMail extends Mailable
{
    use Queueable, SerializesModels;

    public $currentUserInfo;
    /**
     * Create a new message instance.
     */
    public function __construct($currentUserInfo)
    {
        $this->currentUserInfo = $currentUserInfo;
    }
  
    public function build()
    {
           $subject = 'Login Alert';
           return $this->view('emails.loggedin')->subject($subject);

    }

}