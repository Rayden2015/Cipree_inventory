#!/bin/bash
# Permission fix script for cPanel (no sudo required)
# Run this script from your project directory via SSH

set -e

echo "=========================================="
echo "Laravel Permission Fix for cPanel"
echo "=========================================="
echo ""

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Get current user (cPanel username)
CURRENT_USER=$(whoami)
echo "Current user: $CURRENT_USER"
echo "Project directory: $SCRIPT_DIR"
echo ""

# Check if running in cPanel environment
if [ -d "/usr/local/cpanel" ] || [ -n "$CPANEL" ]; then
    echo "✅ cPanel environment detected"
else
    echo "⚠️  This script is designed for cPanel, but cPanel not detected"
    echo "   Continuing anyway..."
fi
echo ""

# In cPanel, the web server user is usually the same as the cPanel user
# or it might be 'nobody' or the username
WEB_USER="$CURRENT_USER"

# Check if public directory exists
if [ ! -d "public" ]; then
    echo "❌ ERROR: public/ directory not found!"
    exit 1
fi

echo "📁 Setting file permissions (no sudo required)..."
echo ""

# Set directory permissions (755 = rwxr-xr-x)
echo "📂 Setting directory permissions to 755..."
find . -type d -exec chmod 755 {} \; 2>/dev/null || {
    echo "⚠️  Some directories couldn't be changed (this is normal)"
}

# Set file permissions (644 = rw-r--r--)
echo "📄 Setting file permissions to 644..."
find . -type f -exec chmod 644 {} \; 2>/dev/null || {
    echo "⚠️  Some files couldn't be changed (this is normal)"
}

# Make scripts executable
echo "🔧 Making scripts executable..."
find . -type f -name "*.sh" -exec chmod +x {} \; 2>/dev/null || true
chmod +x artisan 2>/dev/null || true

# Special permissions for storage and cache directories
echo "💾 Setting special permissions for storage and cache..."
if [ -d "storage" ]; then
    chmod -R 775 storage 2>/dev/null || chmod -R 755 storage 2>/dev/null || true
    echo "   ✅ storage/"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache 2>/dev/null || chmod -R 755 bootstrap/cache 2>/dev/null || true
    echo "   ✅ bootstrap/cache/"
fi

# Ensure public directory is accessible
if [ -d "public" ]; then
    chmod -R 755 public 2>/dev/null || true
    echo "   ✅ public/"
fi

# Fix .env file permissions (should be readable, but not world-readable)
if [ -f ".env" ]; then
    chmod 640 .env 2>/dev/null || chmod 644 .env 2>/dev/null || true
    echo "   ✅ .env"
fi

echo ""
echo "=========================================="
echo "✅ Permissions updated!"
echo "=========================================="
echo ""
echo "Note: In cPanel, you may need to:"
echo "1. Set permissions via cPanel File Manager (755 for dirs, 644 for files)"
echo "2. Ensure public/ directory is accessible"
echo "3. Check that .htaccess file exists in public/"
echo ""
echo "If issues persist, check:"
echo "- cPanel File Manager → Select public/ → Change Permissions"
echo "- cPanel Error Logs"
echo "- Laravel logs: tail -f storage/logs/laravel.log"
echo ""

