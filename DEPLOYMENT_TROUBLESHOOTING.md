# Deployment Troubleshooting Guide

## 403 Errors on Static Assets (CSS/JS/Images)

### Symptoms
- Browser console shows: `Failed to load resource: the server responded with a status of 403`
- CSS styles not loading
- JavaScript not working
- Images not displaying

### Root Cause
File permissions on the cloud server prevent the web server from reading static assets.

### Solution

#### Option 1: Use the Deployment Script (Recommended)

```bash
# SSH into your cloud server
ssh user@your-server.com

# Navigate to your project directory
cd /path/to/your/project

# Run the permission fix script
sudo ./deploy-fix-permissions.sh www-data
```

**Common web server users:**
- Debian/Ubuntu: `www-data`
- CentOS/RHEL: `apache` or `httpd`
- Nginx: `nginx` or `www-data`

#### Option 2: Manual Fix

```bash
# 1. Set ownership to web server user
sudo chown -R www-data:www-data /path/to/your/project

# 2. Set directory permissions (755 = readable/executable by all, writable by owner)
sudo find /path/to/your/project -type d -exec chmod 755 {} \;

# 3. Set file permissions (644 = readable by all, writable by owner)
sudo find /path/to/your/project -type f -exec chmod 644 {} \;

# 4. Special permissions for Laravel directories
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# 5. Ensure public directory is accessible
sudo chmod -R 755 public
sudo chown -R www-data:www-data public
```

#### Option 3: Quick Fix for Public Directory Only

If you only need to fix assets in the public directory:

```bash
sudo chmod -R 755 public
sudo chown -R www-data:www-data public
```

### Verify the Fix

1. **Check file permissions:**
   ```bash
   ls -la public/assets/plugins/fontawesome-free/css/
   # Should show: -rw-r--r-- (644) or -rwxr-xr-x (755)
   ```

2. **Check directory permissions:**
   ```bash
   ls -ld public/assets/
   # Should show: drwxr-xr-x (755)
   ```

3. **Test in browser:**
   - Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
   - Check browser console for errors
   - Verify CSS/JS files load correctly

### Additional Checks

#### Apache Configuration

If using Apache, ensure your virtual host allows access:

```apache
<Directory /path/to/your/project/public>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

#### Nginx Configuration

If using Nginx, ensure proper permissions:

```nginx
server {
    root /path/to/your/project/public;
    index index.php;
    
    location ~ \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
```

#### SELinux (CentOS/RHEL)

If SELinux is enabled, you may need to set the correct context:

```bash
# Check SELinux status
getenforce

# If enabled, set proper context
sudo chcon -R -t httpd_sys_content_t /path/to/your/project
sudo chcon -R -t httpd_sys_rw_content_t /path/to/your/project/storage
sudo chcon -R -t httpd_sys_rw_content_t /path/to/your/project/bootstrap/cache
```

#### AppArmor (Ubuntu)

If AppArmor is blocking access, you may need to configure it:

```bash
# Check AppArmor status
sudo aa-status

# Edit web server profile if needed
sudo nano /etc/apparmor.d/usr.sbin.apache2
# or
sudo nano /etc/apparmor.d/usr.sbin.nginx
```

### Still Having Issues?

1. **Check web server error logs:**
   ```bash
   # Apache
   sudo tail -f /var/log/apache2/error.log
   
   # Nginx
   sudo tail -f /var/log/nginx/error.log
   ```

2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify .htaccess is working:**
   ```bash
   # Test if mod_rewrite is enabled (Apache)
   apache2ctl -M | grep rewrite
   
   # Or check if .htaccess is being read
   # Add a syntax error to .htaccess and see if server complains
   ```

4. **Test file access directly:**
   ```bash
   # Try accessing a file directly via curl
   curl -I https://your-domain.com/assets/plugins/fontawesome-free/css/all.min.css
   
   # Should return 200 OK, not 403 Forbidden
   ```

### Prevention

Add these commands to your deployment script/CI-CD pipeline:

```bash
# After deploying code
sudo chown -R www-data:www-data /path/to/your/project
sudo find /path/to/your/project -type d -exec chmod 755 {} \;
sudo find /path/to/your/project -type f -exec chmod 644 {} \;
sudo chmod -R 775 storage bootstrap/cache
sudo chmod -R 755 public
```

## Other Common Issues

### 500 Internal Server Error

1. Check file permissions (see above)
2. Check `.env` file exists and is configured
3. Check `storage/logs` is writable
4. Run `php artisan config:clear`
5. Check web server error logs

### Assets Loading but Styles Not Applied

1. Clear browser cache
2. Check if CSS files are actually loading (Network tab in DevTools)
3. Verify `APP_URL` in `.env` matches your domain
4. Check for CORS issues if using CDN

### JavaScript Errors

1. Check browser console for specific errors
2. Verify all JS files are loading (Network tab)
3. Check for jQuery conflicts
4. Verify asset paths are correct

