<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the authenticated user's account is active.
     * If inactive, the user is logged out and redirected to login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Skip status check for login and logout routes to prevent redirect loops
        $excludedRoutes = ['login', 'logout'];
        if (in_array($request->route()?->getName(), $excludedRoutes) || 
            in_array($request->path(), ['login', 'logout'])) {
            return $next($request);
        }
        
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user status is not Active
            if ($user->status !== 'Active') {
                Log::warning('CheckStatus Middleware | Inactive user detected and logged out', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_status' => $user->status,
                    'route' => $request->path(),
                    'ip_address' => $request->ip()
                ]);
                
                // Logout the user
                Auth::logout();
                
                // Invalidate the session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Redirect to login with error message
                return redirect()->route('login')
                    ->withErrors(['email' => 'Your account has been deactivated. Please contact the administrator.']);
            }
        }
        
        return $next($request);
    }
}
