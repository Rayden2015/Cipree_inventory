<?php
/**
 * Smart script to update error logging in a specific controller
 * Detects method names and replaces error blocks correctly
 */

if ($argc < 2) {
    die("Usage: php update-controller-errors.php <ControllerName>\n");
}

$controllerName = $argv[1];
$file = "app/Http/Controllers/{$controllerName}.php";

if (!file_exists($file)) {
    die("❌ File not found: $file\n");
}

echo "═══════════════════════════════════════════════════════════════\n";
echo "🔧 Updating {$controllerName}\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$content = file_get_contents($file);
$lines = explode("\n", $content);
$fixedCount = 0;
$currentMethod = null;

// Process each line
for ($i = 0; $i < count($lines); $i++) {
    // Track current method name
    if (preg_match('/public\s+(?:static\s+)?function\s+(\w+)\s*\(/', $lines[$i], $matches)) {
        $currentMethod = $matches[1];
    }
    
    // Find old error logging pattern
    if (strpos($lines[$i], 'unique_id = floor(time() - 999999999)') !== false) {
        // This is the start of an old error block
        // Find the end of the error block (the closing })
        $startLine = $i;
        $endLine = $i;
        $bracketCount = 0;
        
        // Look ahead to find the complete error block
        for ($j = $i; $j < min($i + 30, count($lines)); $j++) {
            if (strpos($lines[$j], 'redirect()->back()') !== false || 
                strpos($lines[$j], 'return redirect()->back()') !== false) {
                // Found the end
                for ($k = $j; $k < min($j + 5, count($lines)); $k++) {
                    if (strpos($lines[$k], ';') !== false && 
                        strpos($lines[$k], 'withError') !== false) {
                        $endLine = $k;
                        break 2;
                    }
                }
            }
        }
        
        if ($endLine > $startLine && $currentMethod) {
            // Replace the entire error block
            $indentation = str_repeat(' ', strlen($lines[$startLine]) - strlen(ltrim($lines[$startLine])));
            $newBlock = $indentation . "return \$this->handleError(\$e, '{$currentMethod}()');";
            
            // Remove old lines and insert new one
            array_splice($lines, $startLine, $endLine - $startLine + 1, [$newBlock]);
            
            $fixedCount++;
            echo "✅ Fixed error block in {$currentMethod}() (line ~" . ($startLine + 1) . ")\n";
            
            // Reset to avoid skipping lines after splice
            $i = $startLine;
        }
    }
}

// Write back
$newContent = implode("\n", $lines);
file_put_contents($file, $newContent);

echo "\n═══════════════════════════════════════════════════════════════\n";
echo "✅ Updated {$controllerName}: {$fixedCount} error blocks replaced\n";
echo "═══════════════════════════════════════════════════════════════\n";


