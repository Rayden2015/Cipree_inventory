<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## PHP 8.4 Compatibility

This project runs on **PHP 8.4** (matching production). PHP 8.4 has stricter nullable type requirements that may cause deprecation warnings from Laravel 10 and vendor packages. These warnings are harmless and don't affect functionality.

### Suppressing Deprecation Warnings

To suppress these warnings, you have three options:

1. **Use the wrapper script** (recommended):
   ```bash
   ./artisan-wrapper.sh serve
   ./artisan-wrapper.sh migrate
   ```

2. **Use php.ini directly**:
   ```bash
   php -c php.ini artisan serve
   ```

3. **Create a shell alias** (add to `~/.zshrc`):
   ```bash
   alias artisan='php -c php.ini artisan'
   ```

The `php.ini` file in the project root suppresses deprecation warnings from vendor packages.

## Documentation

### Banner Configuration

See `docs/BANNER_CONFIGURATION.md` for complete banner configuration guide. The banner system is configurable via `.env` settings:
- `BANNER_ENABLED` - Enable/disable banner
- `BANNER_DISABLE_DATE` - Service disable date (format: Y-m-d)
- `BANNER_DISMISSIBLE` - Whether users can dismiss the banner

### Common Issues & Solutions

#### 403 Errors on Static Assets (CSS/JS/Images)

**Most Common Cause:** Document root not pointing to `public/` directory.

**Quick Fix:**
- **cPanel (no sudo):** Run `./fix-permissions-cpanel.sh` or set permissions via File Manager (755 for dirs, 644 for files)
- **VPS/Server:** Run `sudo ./deploy-fix-permissions.sh www-data`
- **Verify:** Ensure document root points to `public/` directory (not project root)
- **Check:** `public/.htaccess` exists and has correct permissions

**Files to check:** `public/.htaccess`, web server configuration, file permissions on `public/` directory

#### "Page Expired" (419) Error After Login

**Cause:** CSRF token mismatch due to session/cookie configuration in production.

**Quick Fix:**
1. Update `.env`:
   ```env
   APP_URL=https://your-domain.com
   SESSION_SECURE_COOKIE=true
   SESSION_DOMAIN=null
   ```
2. Fix `app/Http/Middleware/TrustProxies.php` - set `protected $proxies = '*';`
3. Clear config cache: `php artisan config:clear && php artisan config:cache`

**Files involved:** `.env`, `app/Http/Middleware/TrustProxies.php`, `config/session.php`

#### Deployment Checklist

**Before deploying to production:**
- Set `APP_ENV=production` and `APP_DEBUG=false` in `.env`
- Verify `APP_URL` matches your domain exactly
- Set correct file permissions (755 for dirs, 644 for files)
- Ensure document root points to `public/` directory
- Test error handling (errors should be logged, not displayed)
- Clear and cache config: `php artisan config:cache`

**Permission scripts available:**
- `deploy-fix-permissions.sh` - For VPS/servers with sudo
- `fix-permissions-cpanel.sh` - For cPanel (no sudo required)

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
