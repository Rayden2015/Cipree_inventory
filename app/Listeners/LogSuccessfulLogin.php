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
        // Get the ID of the authenticated user
        $userId = Auth::id();
    
        // Create a new Login record with the user ID and attempt
        Login::create([
            'user_id' => $userId,
            'attempt' => 1,  // Provide a value for 'attempt'
        ]);
    
        // Find the user by ID if needed for further logic
        $user = User::find($userId);
    }
    
}
