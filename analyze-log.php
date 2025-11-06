<?php
/**
 * Production Log Analyzer
 * Analyzes Laravel production logs to identify issues
 */

$logFile = '/Users/nurudin/Downloads/laravel-2025-11-06.log';

if (!file_exists($logFile)) {
    die("Log file not found: $logFile\n");
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "📊 PRODUCTION LOG ANALYSIS - " . date('Y-m-d') . "\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$content = file_get_contents($logFile);
$lines = explode("\n", $content);

$errors = [];
$warnings = [];
$users = [];
$controllers = [];
$errorTypes = [];

foreach ($lines as $line) {
    // Parse timestamp and level
    if (preg_match('/\[([\d\-\s:]+)\]\s+production\.(ERROR|WARNING|INFO):(.*)/', $line, $matches)) {
        $timestamp = $matches[1];
        $level = $matches[2];
        $message = $matches[3];
        
        if ($level === 'ERROR') {
            // Extract error type
            if (preg_match('/Missing required parameter for \[Route: ([^\]]+)\]/', $message, $routeMatch)) {
                $errorType = "Missing Route Parameter: " . $routeMatch[1];
                $errorTypes[$errorType] = ($errorTypes[$errorType] ?? 0) + 1;
                
                // Extract view info
                if (preg_match('/"view":"([^"]+)"/', $message, $viewMatch)) {
                    $errors[] = [
                        'time' => $timestamp,
                        'type' => $errorType,
                        'view' => basename($viewMatch[1]),
                        'message' => $routeMatch[0]
                    ];
                }
            } elseif (preg_match('/SQLSTATE\[(\w+)\]:(.{0,100})/', $message, $sqlMatch)) {
                $errorType = "Database Error: SQLSTATE[" . $sqlMatch[1] . "]";
                $errorTypes[$errorType] = ($errorTypes[$errorType] ?? 0) + 1;
                $errors[] = [
                    'time' => $timestamp,
                    'type' => $errorType,
                    'message' => trim($sqlMatch[2])
                ];
            } elseif (preg_match('/(Call to undefined|Class.*not found|Method.*does not exist)/', $message, $codeMatch)) {
                $errorType = "Code Error: " . $codeMatch[1];
                $errorTypes[$errorType] = ($errorTypes[$errorType] ?? 0) + 1;
                $errors[] = [
                    'time' => $timestamp,
                    'type' => $errorType,
                    'message' => substr($message, 0, 150)
                ];
            } else {
                $errorTypes['Other Error'] = ($errorTypes['Other Error'] ?? 0) + 1;
                $errors[] = [
                    'time' => $timestamp,
                    'type' => 'Other Error',
                    'message' => substr($message, 0, 150)
                ];
            }
        } elseif ($level === 'WARNING') {
            // Track warnings
            if (preg_match('/(Inactive user|Account disabled|login)/', $message, $warnMatch)) {
                if (preg_match('/"user_email":"([^"]+)"/', $message, $emailMatch)) {
                    $email = $emailMatch[1];
                    $warnings[] = [
                        'time' => $timestamp,
                        'type' => 'Inactive User Login Attempt',
                        'user' => $email
                    ];
                    $users[$email] = ($users[$email] ?? 0) + 1;
                }
            }
        } elseif ($level === 'INFO') {
            // Track controller usage
            if (preg_match('/(\w+Controller)\s+\|/', $message, $ctrlMatch)) {
                $controllers[$ctrlMatch[1]] = ($controllers[$ctrlMatch[1]] ?? 0) + 1;
            }
        }
    }
}

// Display error summary
echo "🔴 ERRORS FOUND:\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total ERROR entries: " . count($errors) . "\n\n";

if (count($errorTypes) > 0) {
    echo "Error Types:\n";
    arsort($errorTypes);
    foreach ($errorTypes as $type => $count) {
        echo "  • $type: $count occurrence(s)\n";
    }
    echo "\n";
}

// Display detailed errors
if (count($errors) > 0) {
    echo "Detailed Error List:\n";
    $displayedErrors = [];
    foreach ($errors as $error) {
        $key = $error['type'] . ($error['view'] ?? $error['message'] ?? '');
        if (!isset($displayedErrors[$key])) {
            $displayedErrors[$key] = true;
            echo "\n  [" . $error['time'] . "]\n";
            echo "  Type: " . $error['type'] . "\n";
            if (isset($error['view'])) {
                echo "  View: " . $error['view'] . "\n";
            }
            if (isset($error['message'])) {
                echo "  Message: " . $error['message'] . "\n";
            }
        }
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "⚠️  WARNINGS FOUND:\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "Total WARNING entries: " . count($warnings) . "\n\n";

if (count($users) > 0) {
    echo "Inactive User Login Attempts:\n";
    arsort($users);
    foreach ($users as $email => $count) {
        echo "  • $email: $count attempt(s)\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "📈 CONTROLLER ACTIVITY:\n";
echo "═══════════════════════════════════════════════════════════════\n";

if (count($controllers) > 0) {
    arsort($controllers);
    $top10 = array_slice($controllers, 0, 10);
    echo "Top 10 Most Active Controllers:\n";
    foreach ($top10 as $controller => $count) {
        echo "  • $controller: $count requests\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "✅ ANALYSIS COMPLETE\n";
echo "═══════════════════════════════════════════════════════════════\n";

