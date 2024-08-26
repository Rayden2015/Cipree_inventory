<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogin
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
    public function handle(object $event)
    {
        $event = Auth::id();
        Login::create([
            'user_id'=>$event,
            'attempt'=>1,
        ]);
        $user = User::find($event);
        // User::where('id','=',$event)->update(['last_login_at'=>Carbon::now()]);
        


    }
}
