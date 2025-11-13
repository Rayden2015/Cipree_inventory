<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Traits\LogsErrors;

class ErrorLoggingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $errorLogDir = storage_path('logs/errors');
        if (! is_dir($errorLogDir)) {
            mkdir($errorLogDir, 0755, true);
        }
    }

    /**
     * Test that LogsErrors trait exists and has required methods
     */
    public function test_logs_errors_trait_exists()
    {
        $this->assertTrue(
            trait_exists('App\Traits\LogsErrors'),
            'LogsErrors trait should exist'
        );
        
        $traitMethods = get_class_methods('App\Traits\LogsErrors');
        
        $this->assertContains('logError', $traitMethods, 'Trait should have logError method');
        $this->assertContains('handleError', $traitMethods, 'Trait should have handleError method');
        $this->assertContains('getUserErrorMessage', $traitMethods, 'Trait should have getUserErrorMessage method');
    }

    /**
     * Test that UserController uses LogsErrors trait
     */
    public function test_user_controller_uses_logs_errors_trait()
    {
        $controller = new \App\Http\Controllers\UserController();
        $traits = class_uses($controller);
        
        $this->assertContains(
            'App\Traits\LogsErrors',
            $traits,
            'UserController should use LogsErrors trait'
        );
    }

    /**
     * Test that EnduserController uses LogsErrors trait
     */
    public function test_enduser_controller_uses_logs_errors_trait()
    {
        $controller = new \App\Http\Controllers\EnduserController();
        $traits = class_uses($controller);
        
        $this->assertContains(
            'App\Traits\LogsErrors',
            $traits,
            'EnduserController should use LogsErrors trait'
        );
    }

    /**
     * Test error logging format
     */
    public function test_error_logging_format()
    {
        $logFile = storage_path('logs/errors/error.log');
        if (file_exists($logFile)) {
            unlink($logFile);
        }

        // Create a test controller that uses the trait
        $controller = new class extends \App\Http\Controllers\Controller {
            use LogsErrors;
            
            public function testMethod()
            {
                try {
                    throw new \Exception('Test exception for logging');
                } catch (\Exception $e) {
                    return $this->logError($e, 'testMethod()', ['test_data' => 'test_value']);
                }
            }
        };

        // Execute the test method and get error ID
        $errorId = $controller->testMethod();
        
        // Verify error ID is a number
        $this->assertIsNumeric($errorId, 'Error ID should be numeric');
        $this->assertGreaterThan(0, $errorId, 'Error ID should be positive');
        
        // Check log file for the error
        $this->assertFileExists($logFile, 'Error log file should exist');
        
        $logContent = file_get_contents($logFile);
        $this->assertStringContainsString((string)$errorId, $logContent, 'Error ID should be in log file');
        $this->assertStringContainsString('[ERROR_ID:' . $errorId . ']', $logContent, 'Error should use new format');
    }

    /**
     * Test getUserErrorMessage format
     */
    public function test_get_user_error_message_format()
    {
        $controller = new class extends \App\Http\Controllers\Controller {
            use LogsErrors;
        };

        $errorId = 123456789;
        $message = $controller->getUserErrorMessage($errorId);
        
        $this->assertStringContainsString((string)$errorId, $message, 'Message should contain error ID');
        $this->assertStringContainsString('Administrator', $message, 'Message should mention Administrator');
        $this->assertStringContainsString('error ID:', $message, 'Message should say "error ID:"');
    }

    /**
     * Test that error_logs table exists
     */
    public function test_error_logs_table_exists()
    {
        $this->assertTrue(
            \Schema::hasTable('error_logs'),
            'error_logs table should exist'
        );
        
        $columns = \Schema::getColumnListing('error_logs');
        
        $expectedColumns = [
            'id', 'message', 'context', 'level', 'level_name', 
            'channel', 'record_datetime', 'extra', 'formatted',
            'remote_addr', 'user_agent', 'created_at', 'updated_at'
        ];
        
        foreach ($expectedColumns as $column) {
            $this->assertContains($column, $columns, "error_logs table should have {$column} column");
        }
    }

    /**
     * Test error log routes exist
     */
    public function test_error_log_routes_exist()
    {
        $this->assertTrue(
            \Route::has('error-logs.index'),
            'error-logs.index route should exist'
        );
        
        $this->assertTrue(
            \Route::has('error-logs.search'),
            'error-logs.search route should exist'
        );
        
        $this->assertTrue(
            \Route::has('error-logs.show'),
            'error-logs.show route should exist'
        );
        
        $this->assertTrue(
            \Route::has('error-logs.search-files'),
            'error-logs.search-files route should exist'
        );
    }
}


