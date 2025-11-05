<?php
/**
 * Comprehensive Error Logging Test
 * Tests all controllers to ensure errors are logged correctly
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "═══════════════════════════════════════════════════════════════\n";
echo "🧪 COMPREHENSIVE ERROR LOGGING TEST\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test results
$results = [
    'total_controllers' => 0,
    'controllers_with_trait' => 0,
    'controllers_with_handle_error' => 0,
    'controllers_with_old_logging' => 0,
    'total_error_blocks' => 0
];

$controllersPath = app_path('Http/Controllers');
$files = File::allFiles($controllersPath);

foreach ($files as $file) {
    if ($file->getExtension() !== 'php') {
        continue;
    }
    
    $results['total_controllers']++;
    $content = File::get($file->getRealPath());
    $filename = $file->getFilename();
    
    // Check if controller uses LogsErrors trait
    if (strpos($content, 'use LogsErrors;') !== false || 
        strpos($content, 'use App\Traits\LogsErrors;') !== false) {
        $results['controllers_with_trait']++;
    }
    
    // Count handleError usages
    $handleErrorCount = substr_count($content, '$this->handleError(');
    if ($handleErrorCount > 0) {
        $results['controllers_with_handle_error']++;
        $results['total_error_blocks'] += $handleErrorCount;
    }
    
    // Count old-style error logging
    $oldStyleCount = substr_count($content, 'floor(time() - 999999999)');
    if ($oldStyleCount > 0) {
        $results['controllers_with_old_logging']++;
        echo "⚠️  {$filename}: {$oldStyleCount} old-style error blocks found\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "📊 TEST RESULTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "Controllers Scanned:\n";
echo "  • Total controllers: " . $results['total_controllers'] . "\n";
echo "  • Using LogsErrors trait: " . $results['controllers_with_trait'] . "\n";
echo "  • Using handleError(): " . $results['controllers_with_handle_error'] . "\n";
echo "  • Still using old logging: " . $results['controllers_with_old_logging'] . "\n";
echo "  • Total error blocks updated: " . $results['total_error_blocks'] . "\n";

$coverage = $results['total_controllers'] > 0 
    ? round(($results['controllers_with_trait'] / $results['total_controllers']) * 100, 1)
    : 0;

echo "\n📈 Coverage: {$coverage}%\n";

if ($coverage >= 80) {
    echo "✅ EXCELLENT! Most controllers updated\n";
} elseif ($coverage >= 50) {
    echo "⚠️  GOOD: Over half updated, continue with remaining controllers\n";
} else {
    echo "❌ NEEDS WORK: Less than half updated\n";
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "🧪 FUNCTIONAL TESTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test 1: Check error_logs table
echo "1. Error Logs Table:\n";
if (Schema::hasTable('error_logs')) {
    echo "   ✅ error_logs table EXISTS\n";
    $count = DB::table('error_logs')->count();
    echo "   📊 Records: {$count}\n";
} else {
    echo "   ❌ error_logs table MISSING\n";
}

// Test 2: Check routes
echo "\n2. Error Log Routes:\n";
$errorRoutes = ['error-logs.index', 'error-logs.search', 'error-logs.show', 'error-logs.search-files'];
foreach ($errorRoutes as $route) {
    if (Route::has($route)) {
        echo "   ✅ {$route}\n";
    } else {
        echo "   ❌ {$route} MISSING\n";
    }
}

// Test 3: Test logging functionality
echo "\n3. Testing Actual Logging:\n";
try {
    $unique_id = floor(time() - 999999999);
    Log::channel('error_log')->error('[ERROR_ID:' . $unique_id . '] Comprehensive test error', [
        'error_id' => $unique_id,
        'test' => 'comprehensive_test',
        'timestamp' => now()->toDateTimeString()
    ]);
    
    echo "   ✅ Test error logged (ID: {$unique_id})\n";
    
    // Check if it's in the file
    $logFile = storage_path('logs/errors/error.log');
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        if (strpos($logContent, (string)$unique_id) !== false) {
            echo "   ✅ Error ID found in log file\n";
        } else {
            echo "   ⚠️  Error ID not yet in file (may take a moment)\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Logging failed: " . $e->getMessage() . "\n";
}

// Test 4: Check critical controllers
echo "\n4. Critical Controllers Status:\n";
$criticalControllers = [
    'UserController' => 'User management',
    'EnduserController' => 'End user management',
    'InventoryController' => 'Inventory operations',
    'OrderController' => 'Order processing',
    'StoreRequestController' => 'Store requests',
    'PurchaseController' => 'Purchase management'
];

foreach ($criticalControllers as $controller => $purpose) {
    $controllerFile = app_path("Http/Controllers/{$controller}.php");
    if (file_exists($controllerFile)) {
        $content = file_get_contents($controllerFile);
        $hasTrait = strpos($content, 'use LogsErrors;') !== false;
        $handleErrorCount = substr_count($content, '$this->handleError(');
        $oldStyleCount = substr_count($content, 'floor(time() - 999999999)');
        
        echo "   {$controller} ({$purpose}):\n";
        if ($hasTrait) {
            echo "      ✅ Uses LogsErrors trait\n";
            if ($handleErrorCount > 0) {
                echo "      ✅ {$handleErrorCount} error blocks using handleError()\n";
            }
            if ($oldStyleCount > 0) {
                echo "      ⚠️  {$oldStyleCount} old-style blocks remaining\n";
            }
        } else {
            echo "      ❌ NOT using LogsErrors trait\n";
            if ($oldStyleCount > 0) {
                echo "      ⚠️  {$oldStyleCount} old-style error blocks\n";
            }
        }
    } else {
        echo "   ❌ {$controller} not found\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "✅ COMPREHENSIVE TEST COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════\n";

