# Deployment Fix: Department Authoriser Pending Request Approvals

## Issue Fixed
Department Authorisers were seeing all site-wide pending request approvals instead of only their department's requests.

## Files to Deploy

### Required Files:
1. **app/Http/Controllers/HomeController.php**
   - Fixed: `$pending_request_approvals` now filters by department for Department Authorisers
   - Location: Lines 179-194

2. **app/Providers/AuthServiceProvider.php**
   - Added: Gate definitions for dashboard access
   - Location: Lines 28-35

## Post-Deployment Steps

1. Clear application cache:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

2. (Optional) Cache config for production:
   ```bash
   php artisan config:cache
   ```

## Verification

After deployment, verify:
- Department Authorisers see only their department's pending requests
- Super Authorisers see all pending requests
- Counts are correct on the dashboard

## Test Accounts (if created)
- Department Authoriser: dept.authoriser@test.com / password
- Super Authoriser: super.authoriser@test.com / password
