#!/bin/bash
# Deployment script to fix file permissions for Laravel application
# Run this script after deploying to your cloud server
# Usage: ./deploy-fix-permissions.sh [web-server-user]
# Example: ./deploy-fix-permissions.sh www-data

set -e

# Default web server user (common options: www-data, apache, nginx, httpd)
WEB_USER="${1:-www-data}"

echo "=========================================="
echo "Laravel Deployment - Fix Permissions"
echo "=========================================="
echo "Web Server User: $WEB_USER"
echo ""

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "⚠️  This script should be run as root or with sudo"
    echo "   Usage: sudo ./deploy-fix-permissions.sh $WEB_USER"
    exit 1
fi

# Check if web user exists
if ! id "$WEB_USER" &>/dev/null; then
    echo "❌ Error: Web server user '$WEB_USER' does not exist"
    echo "   Common users: www-data (Debian/Ubuntu), apache (CentOS/RHEL), nginx, httpd"
    echo "   Please specify the correct user: ./deploy-fix-permissions.sh [user]"
    exit 1
fi

echo "✅ Web server user found: $WEB_USER"
echo ""

# Fix ownership - web server user should own all files
echo "📁 Setting ownership to $WEB_USER..."
chown -R "$WEB_USER:$WEB_USER" .

# Set directory permissions (755 = rwxr-xr-x)
echo "📂 Setting directory permissions to 755..."
find . -type d -exec chmod 755 {} \;

# Set file permissions (644 = rw-r--r--)
echo "📄 Setting file permissions to 644..."
find . -type f -exec chmod 644 {} \;

# Make scripts executable
echo "🔧 Making scripts executable..."
find . -type f -name "*.sh" -exec chmod +x {} \;
chmod +x artisan

# Special permissions for storage and cache directories
echo "💾 Setting special permissions for storage and cache..."
if [ -d "storage" ]; then
    chmod -R 775 storage
    chown -R "$WEB_USER:$WEB_USER" storage
    echo "   ✅ storage/"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache
    chown -R "$WEB_USER:$WEB_USER" bootstrap/cache
    echo "   ✅ bootstrap/cache/"
fi

# Ensure public directory is accessible
if [ -d "public" ]; then
    chmod -R 755 public
    chown -R "$WEB_USER:$WEB_USER" public
    echo "   ✅ public/"
fi

# Fix .env file permissions (should be readable by web server, but not world-readable)
if [ -f ".env" ]; then
    chmod 640 .env
    chown "$WEB_USER:$WEB_USER" .env
    echo "   ✅ .env (640)"
fi

echo ""
echo "=========================================="
echo "✅ Permissions fixed successfully!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Clear application cache: php artisan cache:clear"
echo "2. Clear config cache: php artisan config:clear"
echo "3. Clear route cache: php artisan route:clear"
echo "4. Clear view cache: php artisan view:clear"
echo "5. Optimize for production: php artisan optimize"
echo ""
echo "If you still see 403 errors:"
echo "- Check web server configuration (Apache/Nginx)"
echo "- Verify SELinux/AppArmor settings (if applicable)"
echo "- Check web server error logs"
echo ""

