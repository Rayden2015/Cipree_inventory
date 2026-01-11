# Troubleshooting 504 Gateway Timeout Errors for CSS/JS Files

## Problem
Getting 504 Gateway Timeout errors for CSS and JavaScript files when accessing the login page.

## Understanding 504 Errors
A 504 Gateway Timeout means:
- The server/proxy took too long to respond
- There's a timeout in the server configuration
- PHP-FPM might be timing out if files are being routed through PHP

## Important Note
The login page (`resources/views/auth/login.blade.php`) uses **external CDN links**:
- `https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css`
- `https://unpkg.com/bs-brain@2.0.2/components/logins/login-5/assets/css/login-5.css`

These external CDN files should NOT be affected by your server configuration. If you're seeing 504 errors on these, it could indicate:
1. Network connectivity issues
2. Firewall/proxy blocking external requests
3. DNS resolution problems

## Common Causes & Solutions

### 1. Server/Proxy Timeout Configuration
If you're using a proxy or load balancer (Cloudflare, AWS ALB, etc.), check timeout settings:
- Increase proxy_read_timeout (Nginx)
- Increase ProxyTimeout (Apache)
- Check Cloudflare timeout settings (if using Cloudflare)

### 2. PHP-FPM Processing Static Files
Static files should be served directly by the web server, NOT through PHP-FPM.

**For Nginx:**
Ensure static file locations are defined BEFORE the PHP location block:
```nginx
location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
    expires 1y;
    access_log off;
    try_files $uri =404;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
    # ... rest of PHP config
}
```

**For Apache:**
The `.htaccess` file should handle this (already configured correctly).

### 3. Check Server Logs
Check your web server error logs:
- **Nginx:** `/var/log/nginx/error.log`
- **Apache:** `/var/log/apache2/error.log` or `/var/log/httpd/error_log`
- **PHP-FPM:** `/var/log/php-fpm/error.log`

Look for timeout-related errors.

### 4. Check if Static Files Exist
Verify the files exist and are accessible:
```bash
ls -la public/assets/images/icons/test_green.png
```

### 5. Test Direct Access
Try accessing a CSS/JS file directly in your browser:
- `https://your-domain.com/assets/css/bootstrap.min.css`
- Or check the external CDN links directly

### 6. Check Browser Console
Open browser DevTools (F12) → Network tab:
- Check which specific files are showing 504 errors
- Check if they're local files or external CDN files
- Check the response headers

### 7. Clear Browser Cache
Since it works in incognito mode, try:
- Hard refresh: Ctrl+Shift+R (Windows/Linux) or Cmd+Shift+R (Mac)
- Clear browser cache completely
- Check if browser extensions are blocking requests

### 8. Check Server Resources
504 errors can occur if the server is overloaded:
```bash
# Check server load
top
htop

# Check disk space
df -h

# Check memory
free -h
```

### 9. Increase Timeout Values (If Needed)

**For Nginx:**
```nginx
proxy_read_timeout 300s;
proxy_connect_timeout 75s;
fastcgi_read_timeout 300s;
```

**For PHP-FPM:**
In `/etc/php/8.4/fpm/pool.d/www.conf` (adjust path for your PHP version):
```
request_terminate_timeout = 300s
```

### 10. Check Firewall/Security Software
- Check if mod_security or similar security modules are blocking requests
- Check if firewall rules are blocking certain file types
- Check if antivirus/security software is scanning files (causing delays)

## Quick Diagnostic Steps

1. **Check which files are failing:**
   - Open browser DevTools (F12)
   - Go to Network tab
   - Refresh the page
   - Look for files with 504 status
   - Note whether they're local or external (CDN) files

2. **Test external CDN links directly:**
   - Try opening `https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css` directly in your browser
   - If this times out, it's a network/CDN issue, not your server

3. **Check server logs:**
   - Look for timeout errors
   - Look for PHP-FPM errors
   - Look for access denied errors

4. **Test with curl:**
   ```bash
   # Test local file
   curl -I https://your-domain.com/assets/images/icons/test_green.png
   
   # Test external CDN
   curl -I https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css
   ```

## If Using Cloudflare or Similar Proxy

If you're using Cloudflare or a similar proxy service:
1. Check Cloudflare timeout settings
2. Try bypassing Cloudflare temporarily (if possible)
3. Check if Cloudflare is blocking certain file types
4. Verify DNS is pointing correctly

## Next Steps

1. Identify which specific files are showing 504 errors (local vs external)
2. Check server logs for timeout errors
3. Verify server configuration (timeout values)
4. Test file access directly
5. Check server resources (CPU, memory, disk)

If the errors are only on external CDN files, this is likely a network/proxy issue, not a server configuration issue.
