# Migration to Maxmass Tenant - Guide

## Overview
This command migrates all existing pre-multi-tenancy data to a "Maxmass" tenant. It was designed for systems that were originally built for Maxmass before multi-tenancy was introduced.

## Command
```bash
php artisan tenant:migrate-to-maxmass
```

## What It Does

### Step 1: Creates Maxmass Tenant
- Creates a tenant named "Maxmass" with slug "maxmass"
- Status: Active
- If tenant already exists, uses the existing one

### Step 2: Creates Head Office Site
- Creates a "Head Office" site for Maxmass tenant
- Site code: "HO-MAX"
- If site already exists, uses the existing one

### Step 3: Updates All Sites
- Assigns all sites without `tenant_id` to Maxmass tenant

### Step 4: Updates All Users
- **Users with site_id**: 
  - If site has tenant_id → uses that tenant
  - If site doesn't have tenant_id → assigns to Maxmass and updates the site
- **Users without site_id**: 
  - Assigns to Maxmass tenant
  - Assigns to Head Office site
- **Super Admin users**: Skipped (they should have tenant_id = null)

### Step 5: Updates All Data Tables
Updates the following tables to have `tenant_id = Maxmass tenant id`:
- orders, porders, sorders
- inventory_items, inventories, items
- departments, sections, locations
- suppliers, endusers, end_users_categories
- categories, companies, purchases
- order_parts, porder_parts, sorder_parts
- stock_purchase_requests, spr_porders, spr_porder_items
- inventory_item_details, taxes, levies
- notifications, logins, parts, employees, uoms

**Strategy:**
- For tables with `site_id`: Gets tenant_id from the site
- For tables without `site_id`: Assigns directly to Maxmass

## Safety Checks

### Before Migration
The command checks if any data already has `tenant_id` assigned:
- If found → Migration stops (unless `--force` is used)
- Prevents accidental overwriting of existing tenant assignments

### Force Flag
```bash
php artisan tenant:migrate-to-maxmass --force
```
- Bypasses the safety check
- Use with caution - may overwrite existing tenant assignments

## Example Output

```
🚀 Starting migration to Maxmass tenant...

📦 Step 1: Creating Maxmass tenant...
   ✅ Tenant created/found: Maxmass (ID: 1)

🏢 Step 2: Creating Head Office site...
   ✅ Site created/found: Head Office (ID: 1)

📍 Step 3: Updating sites...
   ✅ Updated 5 sites

👥 Step 4: Updating users...
   ✅ Updated 25 users

📊 Step 5: Updating data tables...
   ✅ All data tables updated

✅ Migration completed successfully!

Summary:
   - Tenant: Maxmass (ID: 1)
   - Head Office Site: Head Office (ID: 1)
   - Sites updated: 5
   - Users updated: 25
```

## Before Running

### 1. Backup Database
```bash
# Always backup before running migration
mysqldump -u username -p database_name > backup_before_migration.sql
```

### 2. Check Current State
```sql
-- Check users without tenant_id
SELECT COUNT(*) FROM users WHERE tenant_id IS NULL;

-- Check sites without tenant_id
SELECT COUNT(*) FROM sites WHERE tenant_id IS NULL;

-- Check data tables
SELECT COUNT(*) FROM orders WHERE tenant_id IS NULL;
SELECT COUNT(*) FROM items WHERE tenant_id IS NULL;
```

### 3. Test in Development First
- Run on a development/staging environment first
- Verify all data is correctly assigned
- Test login with migrated users

## After Migration

### 1. Verify Migration
```sql
-- All users should have tenant_id (except Super Admin)
SELECT id, email, tenant_id, site_id FROM users WHERE tenant_id IS NULL AND email != 'superadmin@gmail.com';

-- All sites should have tenant_id
SELECT id, name, tenant_id FROM sites WHERE tenant_id IS NULL;

-- Sample data check
SELECT COUNT(*) FROM orders WHERE tenant_id IS NULL;
SELECT COUNT(*) FROM items WHERE tenant_id IS NULL;
```

### 2. Test User Login
- Try logging in with `nurundin2010@gmail.com`
- Should now work without "not assigned to a site" error
- User should see their assigned site

### 3. Verify Data Access
- Users should only see data from their tenant
- Super Admin should see all tenants
- Tenant Admin should see all sites in their tenant

## Troubleshooting

### Issue: "Some data already has tenant_id"
**Solution:**
- Review which data has tenant_id
- If intentional, use `--force` flag
- If not, investigate why data has tenant_id

### Issue: User still gets "not assigned to a site"
**Solution:**
```sql
-- Check user's site assignment
SELECT u.id, u.email, u.tenant_id, u.site_id, s.name as site_name
FROM users u
LEFT JOIN sites s ON u.site_id = s.id
WHERE u.email = 'nurundin2010@gmail.com';

-- If site_id is NULL, manually assign:
UPDATE users 
SET site_id = (SELECT id FROM sites WHERE tenant_id = (SELECT tenant_id FROM users WHERE email = 'nurundin2010@gmail.com') LIMIT 1)
WHERE email = 'nurundin2010@gmail.com';
```

### Issue: Data not showing after migration
**Solution:**
- Check if queries filter by tenant_id
- Verify tenant_id is set correctly
- Check session has current_tenant_id
- Review HomeController and other controllers for tenant filtering

## Rollback (If Needed)

If you need to rollback the migration:

```sql
-- Remove tenant_id from all tables (use with caution!)
UPDATE users SET tenant_id = NULL WHERE tenant_id = 1;
UPDATE sites SET tenant_id = NULL WHERE tenant_id = 1;
UPDATE orders SET tenant_id = NULL WHERE tenant_id = 1;
-- ... repeat for all tables
```

**Note:** This will remove all tenant assignments. Only use if you have a backup.

## Notes

- The migration runs in a transaction - if it fails, all changes are rolled back
- Super Admin users are not migrated (they should have tenant_id = null)
- The command logs all operations for audit purposes
- Progress is shown for data table updates
- All operations are logged to Laravel logs
