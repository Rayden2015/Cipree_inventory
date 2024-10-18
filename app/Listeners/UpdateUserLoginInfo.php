<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUserLoginInfo
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Authenticated $event)
    {
       
        
        // $event->user->increment('failed_login_attempts')->update(['last_successful_login' => Carbon::now()]);
    }
}
