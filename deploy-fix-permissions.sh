#!/bin/bash
# Deployment script to fix file permissions for Laravel application
# Can be used on full servers (with sudo/root) and on shared/cPanel hosting
# Usage (server with sudo/root):   sudo ./deploy-fix-permissions.sh [web-server-user]
# Usage (shared hosting / cPanel): ./deploy-fix-permissions.sh   (runs chmod-only, no chown)

set -e

WEB_USER="${1:-www-data}"    # Ignored on shared hosting / when not root

echo "=========================================="
echo "Laravel Deployment - Fix Permissions"
echo "=========================================="
echo "Web Server User (if applicable): $WEB_USER"
echo ""

# Get the directory where the script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Detect environment: root/server vs shared hosting
IS_ROOT=0
if [ "$EUID" -eq 0 ]; then
    IS_ROOT=1
fi

if [ "$IS_ROOT" -eq 1 ]; then
    # Server mode: validate web user and apply chown
    if ! id "$WEB_USER" &>/dev/null; then
        echo "❌ Error: Web server user '$WEB_USER' does not exist"
        echo "   Common users: www-data (Debian/Ubuntu), apache (CentOS/RHEL), nginx, httpd"
        echo "   Please specify the correct user: ./deploy-fix-permissions.sh [user]"
        exit 1
    fi

    echo "✅ Web server user found: $WEB_USER"
    echo ""

    echo "📁 Setting ownership to $WEB_USER (server mode)..."
    chown -R "$WEB_USER:$WEB_USER" .
else
    echo "ℹ️  Running in shared hosting mode (no sudo/chown)."
    echo "    Ownership will NOT be changed; only chmod will be applied."
    echo ""
fi

# Set directory permissions (755 = rwxr-xr-x)
echo "📂 Setting directory permissions to 755..."
find . -type d -exec chmod 755 {} \;

# Set file permissions (644 = rw-r--r--)
echo "📄 Setting file permissions to 644..."
find . -type f -exec chmod 644 {} \;

# Make scripts executable
echo "🔧 Making scripts executable..."
find . -type f -name "*.sh" -exec chmod +x {} \;
[ -f artisan ] && chmod +x artisan

# Special permissions for storage and cache directories
echo "💾 Setting special permissions for storage and cache..."
if [ -d "storage" ]; then
    chmod -R 775 storage
    if [ "$IS_ROOT" -eq 1 ]; then
        chown -R "$WEB_USER:$WEB_USER" storage
    fi
    echo "   ✅ storage/"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache
    if [ "$IS_ROOT" -eq 1 ]; then
        chown -R "$WEB_USER:$WEB_USER" bootstrap/cache
    fi
    echo "   ✅ bootstrap/cache/"
fi

# Ensure public directory is accessible (when script is run from project root)
if [ -d "public" ]; then
    chmod -R 755 public
    if [ "$IS_ROOT" -eq 1 ]; then
        chown -R "$WEB_USER:$WEB_USER" public
    fi
    echo "   ✅ public/"
fi

# cPanel / shared-hosting friendly web asset permissions.
# If the script is run from the public docroot (e.g. ~/dev.cipree.com/public),
# make sure CSS/JS/image assets are world-readable without requiring sudo.
if [ -d "assets" ]; then
    echo "🌐 Fixing permissions for ./assets (CSS/JS/fonts)..."
    find assets -type d -exec chmod 755 {} \;
    find assets -type f -exec chmod 644 {} \;
    echo "   ✅ assets/"
fi

if [ -d "global_assets" ]; then
    echo "🌐 Fixing permissions for ./global_assets (CSS/JS/fonts)..."
    find global_assets -type d -exec chmod 755 {} \; 2>/dev/null
    find global_assets -type f -exec chmod 644 {} \; 2>/dev/null
    echo "   ✅ global_assets/"
fi

# Fix .env file permissions (should be readable by web server, but not world-readable)
if [ -f ".env" ]; then
    chmod 640 .env
    if [ "$IS_ROOT" -eq 1 ]; then
        chown "$WEB_USER:$WEB_USER" .env
    fi
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

