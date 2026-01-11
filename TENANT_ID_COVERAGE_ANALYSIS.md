# Tenant ID Coverage Analysis - Which Tables Need tenant_id?

## ✅ YES - Tables That SHOULD Have tenant_id

These tables contain **business data that needs to be isolated per tenant**:

### Core Business Tables ✅ (Already Covered)
- ✅ `orders` - Orders/requests
- ✅ `porders` - Purchase orders
- ✅ `sorders` - Store orders
- ✅ `inventory_items` - Inventory items
- ✅ `inventories` - Inventory records
- ✅ `items` - Items/master data
- ✅ `order_parts` - Order line items
- ✅ `porder_parts` - Purchase order line items
- ✅ `sorder_parts` - Store order line items
- ✅ `stock_purchase_requests` - Stock purchase requests
- ✅ `spr_porders` - SPR purchase orders
- ✅ `spr_porder_items` - SPR purchase order items
- ✅ `inventory_item_details` - Inventory item details

### Organizational Tables ✅ (Already Covered)
- ✅ `departments` - Departments (tenant-specific)
- ✅ `sections` - Sections (tenant-specific)
- ✅ `locations` - Locations (tenant-specific)
- ✅ `sites` - Sites (already has tenant_id via separate migration)
- ✅ `users` - Users (already has tenant_id via separate migration)

### Master Data Tables ✅ (Already Covered)
- ✅ `suppliers` - Suppliers (tenant-specific)
- ✅ `endusers` - End users (tenant-specific)
- ✅ `end_users_categories` - End user categories (tenant-specific)
- ✅ `categories` - Item categories (tenant-specific)
- ✅ `companies` - Companies (tenant-specific)
- ✅ `parts` - Parts (tenant-specific)
- ✅ `employees` - Employees (tenant-specific)

### Financial/Config Tables ✅ (Already Covered)
- ✅ `purchases` - Purchase records
- ✅ `taxes` - Tax configurations (tenant-specific)
- ✅ `levies` - Levy configurations (tenant-specific)

### Audit/Activity Tables ✅ (Already Covered - Optional)
- ✅ `notifications` - User notifications (tenant-specific for isolation)
- ✅ `logins` - Login audit trail (tenant-specific for security)

## ❌ NO - Tables That DON'T Need tenant_id

These are **system tables** or **global reference tables**:

### System Tables ❌
- ❌ `migrations` - Laravel migration tracking (system)
- ❌ `password_reset_tokens` / `password_resets` - Password resets (system)
- ❌ `failed_jobs` - Failed job queue (system)
- ❌ `personal_access_tokens` - API tokens (system)
- ❌ `cache` / `cache_locks` - Cache tables (system)
- ❌ `jobs` / `job_batches` - Queue tables (system)
- ❌ `sessions` - Session storage (system)

### Permission/Role Tables ❌ (Global - Managed by Super Admin)
- ❌ `roles` - Roles (global, managed by Super Admin)
- ❌ `permissions` - Permissions (global, managed by Super Admin)
- ❌ `model_has_roles` - Role assignments (global)
- ❌ `model_has_permissions` - Permission assignments (global)
- ❌ `role_has_permissions` - Role-permission mapping (global)

### Core Tenant Table ❌
- ❌ `tenants` - Tenants table itself (doesn't make sense)

## ⚠️ MAYBE - Tables That Might Need tenant_id (Decision Required)

These depend on your business requirements:

### Units of Measure (UOMs) ⚠️
- **Option 1 (Recommended):** Tenant-specific UOMs
  - ✅ Add `tenant_id` to `uoms` table
  - ✅ Each tenant can define their own units
  - ✅ Better isolation and customization
  
- **Option 2:** Global UOMs
  - ❌ No `tenant_id` needed
  - ❌ All tenants share the same units
  - ❌ Less flexible but simpler

**Recommendation:** Make UOMs tenant-specific for better isolation and flexibility.

## 📊 Current Coverage Status

### ✅ Already Covered (29 tables)
Our migration `2026_01_10_224628_add_tenant_id_to_all_tables.php` covers:
1. orders ✅
2. porders ✅
3. sorders ✅
4. inventory_items ✅
5. inventories ✅
6. items ✅
7. departments ✅
8. sections ✅
9. locations ✅
10. suppliers ✅
11. endusers ✅
12. end_users_categories ✅
13. categories ✅
14. companies ✅
15. purchases ✅
16. order_parts ✅
17. porder_parts ✅
18. sorder_parts ✅
19. stock_purchase_requests ✅
20. spr_porders ✅
21. spr_porder_items ✅
22. inventory_item_details ✅
23. taxes ✅
24. levies ✅
25. notifications ✅
26. logins ✅
27. parts ✅
28. employees ✅
29. sites (via separate migration) ✅
30. users (via separate migration) ✅

### ⚠️ Potentially Missing
- `uoms` - **RECOMMENDED to add** if UOMs should be tenant-specific

### ❌ Correctly Excluded
- System tables (migrations, password_resets, etc.)
- Permission tables (roles, permissions, etc.)
- The tenants table itself

## 🔍 Verification Query

To check which tables actually exist and have tenant_id, run:

```sql
-- Check all tables and their columns
SELECT 
    t.table_name,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM information_schema.columns c 
            WHERE c.table_name = t.table_name 
            AND c.column_name = 'tenant_id'
        ) THEN 'YES' 
        ELSE 'NO' 
    END as has_tenant_id,
    CASE 
        WHEN EXISTS (
            SELECT 1 
            FROM information_schema.columns c 
            WHERE c.table_name = t.table_name 
            AND c.column_name = 'site_id'
        ) THEN 'YES' 
        ELSE 'NO' 
    END as has_site_id
FROM information_schema.tables t
WHERE t.table_schema = DATABASE()
AND t.table_type = 'BASE TABLE'
AND t.table_name NOT IN (
    'migrations', 
    'password_reset_tokens', 
    'password_resets',
    'failed_jobs',
    'personal_access_tokens',
    'cache',
    'cache_locks',
    'jobs',
    'job_batches',
    'sessions',
    'tenants'
)
ORDER BY t.table_name;
```

## 🎯 Recommendations

1. **Add `tenant_id` to `uoms` table** if UOMs should be tenant-specific (recommended)
2. **Keep system tables without tenant_id** (correctly excluded)
3. **Keep permission tables global** (correctly excluded)
4. **All business data tables should have tenant_id** (already covered)

## 📝 Action Items

1. ✅ **Verify all business tables have tenant_id** - Already done
2. ⚠️ **Consider adding tenant_id to `uoms`** - Decision needed
3. ✅ **Ensure system tables are excluded** - Already correct
4. ✅ **Test data isolation** - After deployment

## ✅ Conclusion

**Your implementation is CORRECT!** 

Not every table needs `tenant_id`, and you've correctly:
- ✅ Added `tenant_id` to all business data tables
- ✅ Excluded system tables (migrations, cache, etc.)
- ✅ Excluded permission tables (global management)
- ⚠️ **Missing:** Consider `uoms` table if units should be tenant-specific

**The only potential addition is the `uoms` table, but that's a business decision.**
