<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// Production Safety: Ensure errors are never displayed in production
// This prevents embarrassing error messages from being shown to users
// Use $_ENV or getenv() instead of env() since Laravel hasn't bootstrapped yet
$appEnv = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: 'production';
$appDebug = isset($_ENV['APP_DEBUG']) ? filter_var($_ENV['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) : (filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) ?: false);
$isProduction = ($appEnv === 'production') || !$appDebug;

if ($isProduction) {
    // In production, suppress all error display
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    // Errors are still logged, just not displayed
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    // In development, suppress deprecation warnings from vendor packages
    // PHP 8.4+ has stricter nullable type requirements that cause warnings in older Laravel/vendor code
    if (PHP_VERSION_ID >= 80100) {
        // Temporarily suppress E_DEPRECATED from error reporting for display
        $originalErrorReporting = error_reporting();
        error_reporting($originalErrorReporting & ~E_DEPRECATED);
        
        // Set error handler to catch deprecation warnings from all vendor packages
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            // Suppress deprecation warnings from all vendor packages
            // These are known compatibility issues that don't affect functionality
            if ($errno === E_DEPRECATED && strpos($errfile, '/vendor/') !== false) {
                return true; // Suppress the warning completely
            }
            // Let other errors through to default handler
            return false;
        }, E_DEPRECATED | E_STRICT);
    }
}

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
