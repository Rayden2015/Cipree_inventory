# Multi-Tenancy Implementation Plan

## Overview
Implementing a comprehensive multi-tenancy system where:
- **Super Admin** can create tenants and tenant admins
- **Tenant Admin** can setup their tenant, create sites, and manage users
- Data isolation by tenant_id at all levels

## Architecture

### Database Structure
1. **tenants** table (new)
   - id, name, slug, domain, status, settings (JSON), created_at, updated_at

2. **users** table (modify)
   - Add: tenant_id (nullable - for tenant admins directly under tenant)
   - Keep: site_id (nullable - for users under a site)

3. **sites** table (modify)
   - Add: tenant_id (required)

4. All other tables (modify)
   - Add: tenant_id to orders, inventory_items, departments, suppliers, etc.

### Roles
- **Super Admin**: Can access all tenants, create tenants, create tenant admins
- **Tenant Admin**: Can manage their tenant, create sites, manage users within tenant
- Existing roles remain but are scoped to tenant

## Implementation Steps

1. ✅ Fix merge conflicts in migration files
2. ⏳ Create tenants table migration
3. ⏳ Create Tenant model with relationships
4. ⏳ Add tenant_id to users table
5. ⏳ Add tenant_id to sites table
6. ⏳ Add tenant_id to all relevant tables
7. ⏳ Create TenantContext middleware
8. ⏳ Update User model to handle tenant context
9. ⏳ Create Super Admin role and permissions
10. ⏳ Create Tenant Admin role and permissions
11. ⏳ Create TenantController for Super Admin
12. ⏳ Create TenantAdminController for tenant management
13. ⏳ Update all controllers to use tenant context
14. ⏳ Create views for tenant management
15. ⏳ Update authentication to handle tenant switching

## Files to Create/Modify

### New Files
- app/Models/Tenant.php
- app/Http/Controllers/TenantController.php
- app/Http/Controllers/TenantAdminController.php
- app/Http/Middleware/TenantContext.php
- database/migrations/XXXX_create_tenants_table.php
- database/migrations/XXXX_add_tenant_id_to_users_table.php
- database/migrations/XXXX_add_tenant_id_to_sites_table.php
- database/migrations/XXXX_add_tenant_id_to_all_tables.php
- resources/views/tenants/*.blade.php
- resources/views/tenant-admin/*.blade.php

### Modified Files
- app/Models/User.php
- app/Models/Site.php
- All models (add tenant relationship)
- All controllers (add tenant filtering)
- app/Http/Kernel.php (register middleware)
- routes/web.php (add tenant routes)
