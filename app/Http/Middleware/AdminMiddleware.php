<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Traits\HasRoles;

class AdminMiddleware
{
    use HasRoles;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //     return $next($request);
        // }
        // $user = Auth::user();
        if (Auth::user()->role->name !== 'admin') {
            //you can throw a 401 unauthorized error here instead of redirecting back
            return redirect()->route('home'); //this redirects all non-admins back to their previous url's
        }
        return $next($request);
    }
}
