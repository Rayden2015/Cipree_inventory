<?php
/**
 * Automatically fix REPLACE_METHOD() placeholders by detecting actual method names
 */

$files = [
    'app/Http/Controllers/StockPurchaseRequestController.php',
    'app/Http/Controllers/AuthoriserController.php',
    'app/Http/Controllers/CategoryController.php',
    'app/Http/Controllers/StoreRequestController.php',
    'app/Http/Controllers/SupplierController.php',
];

$totalFixed = 0;

foreach ($files as $file) {
    if (!file_exists($file)) {
        echo "❌ File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    $fixed = 0;
    
    // Find each REPLACE_METHOD() occurrence and look backwards for the method name
    for ($i = 0; $i < count($lines); $i++) {
        if (strpos($lines[$i], "REPLACE_METHOD()") !== false) {
            // Look backwards for the method definition
            for ($j = $i; $j >= max(0, $i - 50); $j--) {
                if (preg_match('/public\s+function\s+(\w+)\s*\(/', $lines[$j], $matches)) {
                    $methodName = $matches[1];
                    $lines[$i] = str_replace("REPLACE_METHOD()", $methodName . "()", $lines[$i]);
                    $fixed++;
                    echo "✅ Fixed in $file: REPLACE_METHOD() -> $methodName()\n";
                    break;
                }
            }
        }
    }
    
    if ($fixed > 0) {
        file_put_contents($file, implode("\n", $lines));
        echo "   ➜ Saved $file with $fixed fixes\n\n";
        $totalFixed += $fixed;
    }
}

echo "═══════════════════════════════════════\n";
echo "✅ Total fixes applied: $totalFixed\n";
echo "═══════════════════════════════════════\n";

