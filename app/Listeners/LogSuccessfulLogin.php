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
        try {
            // Get the authenticated user from the event
            $user = $event->user;
            
            if ($user) {
                // Create a new Login record for successful login
                Login::create([
                    'user_id' => $user->id,
                    'attempt' => 1, // 1 for successful login
                    'site_id' => $user->site_id ?? null,
                ]);
                
                \Log::info('LogSuccessfulLogin | Successful login recorded', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'login_time' => now()
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('LogSuccessfulLogin | Error logging successful login', [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
        }
    }
    
}
