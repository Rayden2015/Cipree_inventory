#!/bin/bash
# Script to help identify and fix REPLACE_METHOD() placeholders

echo "═══════════════════════════════════════════════════════════════"
echo "🔍 Finding REPLACE_METHOD() placeholders in controllers"
echo "═══════════════════════════════════════════════════════════════"
echo

cd /Users/nurudin/Documents/Projects/inventory-v2

# Find all files with REPLACE_METHOD
FILES=$(grep -rl "REPLACE_METHOD()" app/Http/Controllers/ --include="*.php")

if [ -z "$FILES" ]; then
    echo "✅ No REPLACE_METHOD() placeholders found!"
    exit 0
fi

echo "📋 Files with REPLACE_METHOD():"
echo "$FILES"
echo
echo "═══════════════════════════════════════════════════════════════"
echo "📝 Showing context for each occurrence:"
echo "═══════════════════════════════════════════════════════════════"
echo

# Show context for each occurrence
for file in $FILES; do
    echo "📄 File: $file"
    echo "───────────────────────────────────────────────────────────────"
    grep -B 10 "REPLACE_METHOD()" "$file" | grep -E "public function|REPLACE_METHOD"
    echo
done

echo "═══════════════════════════════════════════════════════════════"
echo "💡 To fix: Search for 'REPLACE_METHOD()' and replace with actual method name"
echo "   Example: index(), store(), update(), destroy(), etc."
echo "═══════════════════════════════════════════════════════════════"

