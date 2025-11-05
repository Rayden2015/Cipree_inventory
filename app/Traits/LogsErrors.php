<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsErrors
{
    /**
     * Log an error with comprehensive context
     *
     * @param \Exception $exception The exception that was thrown
     * @param string $action The action/method being performed (e.g., "Store()", "Update()")
     * @param array $additionalContext Any additional context to log
     * @return int The unique error ID
     */
    protected function logError(\Exception $exception, string $action, array $additionalContext = []): int
    {
        $unique_id = floor(time() - 999999999);
        
        // Get controller name from the calling class
        $controller = class_basename($this);
        
        // Build comprehensive error context
        $context = array_merge([
            'error_id' => $unique_id,
            'controller' => $controller,
            'method' => $action,
            'user_id' => Auth::id() ?? null,
            'user_name' => Auth::user()->name ?? 'guest',
            'user_email' => Auth::user()->email ?? null,
            'url' => request()->fullUrl(),
            'http_method' => request()->method(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'error_message' => $exception->getMessage(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'error_code' => $exception->getCode(),
            'stack_trace' => $exception->getTraceAsString(),
            'request_data' => request()->except(['password', 'password_confirmation', '_token', 'image']),
            'timestamp' => now()->toDateTimeString()
        ], $additionalContext);
        
        // Log with standardized format
        Log::channel('error_log')->error(
            "[ERROR_ID:{$unique_id}] {$controller} | {$action} | {$exception->getMessage()}", 
            $context
        );
        
        return $unique_id;
    }
    
    /**
     * Get standardized error message for users
     *
     * @param int $errorId The unique error ID
     * @return string The user-friendly error message
     */
    protected function getUserErrorMessage(int $errorId): string
    {
        return "An error occurred. Please contact the Administrator with error ID: {$errorId} via the error code and Feedback Button.";
    }
    
    /**
     * Log and return error response
     *
     * @param \Exception $exception The exception that was thrown
     * @param string $action The action/method being performed
     * @param array $additionalContext Any additional context to log
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleError(\Exception $exception, string $action, array $additionalContext = [])
    {
        $errorId = $this->logError($exception, $action, $additionalContext);
        
        return redirect()->back()
            ->withInput()
            ->withError($this->getUserErrorMessage($errorId));
    }
}

