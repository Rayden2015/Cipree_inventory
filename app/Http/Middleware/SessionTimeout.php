<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionTimeout
{
    protected $timeout = 120; // Set the inactivity time in minutes

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            $currentTime = Carbon::now()->timestamp;

            if ($lastActivity && ($currentTime - $lastActivity) > ($this->timeout * 60)) {
                Auth::logout();
                session()->invalidate();
                return redirect()->route('login')->with('message', 'You have been logged out due to inactivity.');
            }

            session(['lastActivityTime' => $currentTime]);
        }

        return $next($request);
    }
}
