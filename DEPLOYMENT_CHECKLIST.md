# Production Deployment Checklist

## Critical: Error Display Settings

### Before Deploying to Production

1. **Verify `.env` file has these settings:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Never commit `.env` file with `APP_DEBUG=true`**

3. **Verify error suppression is working:**
   - Errors should be logged to `storage/logs/laravel.log`
   - Errors should NOT be displayed on the page
   - Users should see generic error pages, not stack traces

### Security Settings

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` is set to your production domain
- [ ] Database credentials are correct
- [ ] All sensitive keys are set (APP_KEY, etc.)

### Error Handling

The application is configured to:
- ✅ Suppress all error display in production (`display_errors=0`)
- ✅ Log all errors to `storage/logs/laravel.log`
- ✅ Show generic error pages to users (500, 404, etc.)
- ✅ Suppress Carbon deprecation warnings
- ✅ Never expose stack traces or sensitive information

### Testing Error Suppression

After deployment, test that errors are properly hidden:
1. Intentionally cause an error (e.g., access a non-existent route)
2. Verify you see a generic error page, not a stack trace
3. Check that the error is logged in `storage/logs/laravel.log`

### Important Notes

- **NEVER** set `APP_DEBUG=true` in production
- **NEVER** commit `.env` file to version control
- Always test error handling in a staging environment first
- Monitor error logs regularly for issues

