<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
    /**
     * Render an exception into an HTTP response.
     * 
     * IMPORTANT: In production, never display error details to users.
     * All errors are logged but only generic messages are shown.
     */
    public function render($request, Throwable $e)
    {
        // Filter Carbon deprecation warnings from output
        if (PHP_VERSION_ID >= 80100 && $e instanceof \ErrorException) {
            if ($e->getSeverity() === E_DEPRECATED && 
                strpos($e->getFile(), '/vendor/nesbot/carbon/') !== false) {
                // Don't render Carbon deprecation warnings
                return response('', 200);
            }
        }
        
        // Let Laravel handle the rendering - it will respect APP_DEBUG setting
        // In production (APP_DEBUG=false), it will show generic error pages
        // In development (APP_DEBUG=true), it will show detailed errors
        return parent::render($request, $e);
    }
    
}
