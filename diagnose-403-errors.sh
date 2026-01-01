#!/bin/bash
# Diagnostic script to identify 403 error causes
# Run this on your cloud server to diagnose asset access issues

set -e

echo "=========================================="
echo "403 Error Diagnostic Tool"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get project directory
if [ -z "$1" ]; then
    PROJECT_DIR=$(pwd)
else
    PROJECT_DIR="$1"
fi

cd "$PROJECT_DIR"

echo "Project Directory: $PROJECT_DIR"
echo ""

# Check if public directory exists
if [ ! -d "public" ]; then
    echo -e "${RED}❌ ERROR: public/ directory not found!${NC}"
    exit 1
fi

echo -e "${GREEN}✅ public/ directory exists${NC}"
echo ""

# Check web server type
echo "=== Web Server Detection ==="
if command -v apache2 &> /dev/null || command -v httpd &> /dev/null; then
    echo -e "${GREEN}✅ Apache detected${NC}"
    WEB_SERVER="apache"
    if command -v apache2 &> /dev/null; then
        APACHE_USER=$(ps aux | grep '[a]pache2' | head -1 | awk '{print $1}' || echo "www-data")
    else
        APACHE_USER=$(ps aux | grep '[h]ttpd' | head -1 | awk '{print $1}' || echo "apache")
    fi
    echo "   Web server user: $APACHE_USER"
elif command -v nginx &> /dev/null; then
    echo -e "${GREEN}✅ Nginx detected${NC}"
    WEB_SERVER="nginx"
    NGINX_USER=$(ps aux | grep '[n]ginx' | head -1 | awk '{print $1}' || echo "www-data")
    echo "   Web server user: $NGINX_USER"
else
    echo -e "${YELLOW}⚠️  Could not detect web server${NC}"
    WEB_SERVER="unknown"
fi
echo ""

# Check file permissions
echo "=== File Permissions Check ==="
echo "Checking public/assets/plugins/fontawesome-free/css/all.min.css..."

TEST_FILE="public/assets/plugins/fontawesome-free/css/all.min.css"
if [ -f "$TEST_FILE" ]; then
    PERMS=$(stat -c "%a" "$TEST_FILE" 2>/dev/null || stat -f "%OLp" "$TEST_FILE" 2>/dev/null)
    OWNER=$(stat -c "%U:%G" "$TEST_FILE" 2>/dev/null || stat -f "%Su:%Sg" "$TEST_FILE" 2>/dev/null)
    
    echo "   File: $TEST_FILE"
    echo "   Permissions: $PERMS"
    echo "   Owner: $OWNER"
    
    if [ "$PERMS" -lt 644 ]; then
        echo -e "   ${RED}❌ Permissions too restrictive (should be 644 or 755)${NC}"
    else
        echo -e "   ${GREEN}✅ Permissions OK${NC}"
    fi
else
    echo -e "   ${RED}❌ Test file not found: $TEST_FILE${NC}"
fi
echo ""

# Check directory permissions
echo "Checking public/assets/ directory..."
if [ -d "public/assets" ]; then
    DIR_PERMS=$(stat -c "%a" "public/assets" 2>/dev/null || stat -f "%OLp" "public/assets" 2>/dev/null)
    DIR_OWNER=$(stat -c "%U:%G" "public/assets" 2>/dev/null || stat -f "%Su:%Sg" "public/assets" 2>/dev/null)
    
    echo "   Permissions: $DIR_PERMS"
    echo "   Owner: $DIR_OWNER"
    
    if [ "$DIR_PERMS" -lt 755 ]; then
        echo -e "   ${RED}❌ Directory permissions too restrictive (should be 755)${NC}"
    else
        echo -e "   ${GREEN}✅ Directory permissions OK${NC}"
    fi
else
    echo -e "   ${RED}❌ public/assets/ directory not found!${NC}"
fi
echo ""

# Check .htaccess
echo "=== .htaccess Check ==="
if [ -f "public/.htaccess" ]; then
    echo -e "${GREEN}✅ public/.htaccess exists${NC}"
    
    if grep -q "Require all granted" "public/.htaccess" || grep -q "Allow from all" "public/.htaccess"; then
        echo -e "   ${GREEN}✅ Contains access rules${NC}"
    else
        echo -e "   ${YELLOW}⚠️  May be missing access rules${NC}"
    fi
else
    echo -e "${RED}❌ public/.htaccess not found!${NC}"
fi
echo ""

# Check document root
echo "=== Document Root Check ==="
if [ "$WEB_SERVER" = "apache" ]; then
    echo "Checking Apache configuration..."
    if [ -f "/etc/apache2/sites-enabled/"* ] || [ -f "/etc/httpd/conf.d/"* ]; then
        echo "   Apache config files found"
        echo "   ${YELLOW}⚠️  Please verify DocumentRoot points to: $PROJECT_DIR/public${NC}"
    fi
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "Checking Nginx configuration..."
    if [ -f "/etc/nginx/sites-enabled/"* ]; then
        echo "   Nginx config files found"
        echo "   ${YELLOW}⚠️  Please verify root directive points to: $PROJECT_DIR/public${NC}"
    fi
fi
echo ""

# Check SELinux (if applicable)
echo "=== SELinux Check ==="
if command -v getenforce &> /dev/null; then
    SELINUX_STATUS=$(getenforce 2>/dev/null || echo "Disabled")
    echo "   SELinux Status: $SELINUX_STATUS"
    if [ "$SELINUX_STATUS" != "Disabled" ]; then
        echo -e "   ${YELLOW}⚠️  SELinux is enabled - may need to set context${NC}"
        echo "   Run: sudo chcon -R -t httpd_sys_content_t $PROJECT_DIR"
    fi
else
    echo "   SELinux not detected"
fi
echo ""

# Test file access
echo "=== File Access Test ==="
if [ -f "$TEST_FILE" ]; then
    if [ -r "$TEST_FILE" ]; then
        echo -e "   ${GREEN}✅ File is readable${NC}"
    else
        echo -e "   ${RED}❌ File is NOT readable${NC}"
    fi
else
    echo -e "   ${RED}❌ Test file does not exist${NC}"
fi
echo ""

# Recommendations
echo "=== Recommendations ==="
echo ""
echo "1. Fix file permissions:"
echo "   sudo chown -R $APACHE_USER:$APACHE_USER $PROJECT_DIR"
echo "   sudo find $PROJECT_DIR -type d -exec chmod 755 {} \\;"
echo "   sudo find $PROJECT_DIR -type f -exec chmod 644 {} \\;"
echo "   sudo chmod -R 755 $PROJECT_DIR/public"
echo ""
echo "2. Verify document root in web server config:"
if [ "$WEB_SERVER" = "apache" ]; then
    echo "   DocumentRoot should be: $PROJECT_DIR/public"
    echo "   Check: /etc/apache2/sites-enabled/* or /etc/httpd/conf.d/*"
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "   root directive should be: $PROJECT_DIR/public"
    echo "   Check: /etc/nginx/sites-enabled/*"
fi
echo ""
echo "3. Check web server error logs:"
if [ "$WEB_SERVER" = "apache" ]; then
    echo "   sudo tail -f /var/log/apache2/error.log"
elif [ "$WEB_SERVER" = "nginx" ]; then
    echo "   sudo tail -f /var/log/nginx/error.log"
fi
echo ""
echo "4. Test direct file access:"
echo "   curl -I http://your-domain.com/assets/plugins/fontawesome-free/css/all.min.css"
echo ""

