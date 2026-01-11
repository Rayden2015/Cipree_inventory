# Creating Super Admin User on Production/Pre-Prod

## Prerequisites
1. SSH into the server: `ssh pensmqhz@business107 dev.cipree.com`
2. Navigate to the project directory
3. Ensure migrations have been run: `php artisan migrate`

## Step 1: Create Roles (if not already created)
```bash
php artisan tenant:create-roles
```

This creates:
- Super Admin role
- Tenant Admin role

## Step 2: Create Super Admin User

### Option A: With password in command (Quick)
```bash
php artisan tenant:create-super-admin superadmin@example.com "Super Admin" --password=YourSecurePassword123!
```

### Option B: Interactive (More Secure)
```bash
php artisan tenant:create-super-admin superadmin@example.com "Super Admin"
```
Then enter password when prompted (will be hidden).

## Step 3: Verify User Created
```bash
php artisan tinker
```
Then in tinker:
```php
$user = \App\Models\User::where('email', 'superadmin@example.com')->first();
$user->roles; // Should show "Super Admin" role
$user->isSuperAdmin(); // Should return true
exit
```

## Step 4: Login
1. Navigate to: `https://dev.cipree.com/login`
2. Email: `superadmin@example.com`
3. Password: The password you set

## Important Notes:
- Super Admin users have `tenant_id = null` and `site_id = null`
- Super Admin can access all tenants and manage them
- Super Admin bypasses domain checks in production
- Make sure to use a strong password (min 8 characters)

## Troubleshooting:
- If command fails: Check that migrations have been run
- If login fails: Verify user status is "Active" in database
- If role not assigned: Run `php artisan tenant:create-roles` first
