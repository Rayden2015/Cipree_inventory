# Quick Start: Running Multi-Tenancy Tests

## 🚀 Quick Start (5 minutes)

### Step 1: Run All Tests
```bash
# Run all tests
php artisan test

# Or run just multi-tenancy tests
bash tests/RunMultiTenancyTests.sh
```

### Step 2: Run Critical Data Isolation Test
```bash
# This is THE most important test - ensures tenants can't see each other's data
php artisan test tests/Feature/TenantDataIsolationTest.php
```

### Step 3: Run Specific Test Suite
```bash
# Tenant management (Super Admin)
php artisan test tests/Feature/TenantManagementTest.php

# Tenant Admin functionality
php artisan test tests/Feature/TenantAdminTest.php

# End-to-end flow
php artisan test tests/Feature/TenantEndToEndTest.php

# Middleware
php artisan test tests/Feature/TenantMiddlewareTest.php
```

## 📊 Test Files Created

✅ **5 Feature Test Classes:**
- `TenantManagementTest.php` - Super Admin tenant management
- `TenantAdminTest.php` - Tenant Admin operations
- `TenantDataIsolationTest.php` ⚠️ **CRITICAL** - Data isolation
- `TenantMiddlewareTest.php` - Middleware tests
- `TenantEndToEndTest.php` - Complete user journeys

✅ **2 Unit Test Classes:**
- `TenantModelTest.php` - Tenant model tests
- `UserModelTest.php` - User model tests

✅ **8 Factory Files:**
- `TenantFactory.php`
- `SiteFactory.php`
- `OrderFactory.php`
- `ItemFactory.php`
- `InventoryItemFactory.php`
- `DepartmentFactory.php`
- `InventoryFactory.php`
- Updated `UserFactory.php`

## ✅ What's Tested

### 1. Tenant Management ✅
- Super Admin can create tenants
- Super Admin can view tenants
- Super Admin can update tenants
- Super Admin can create tenant admins
- Non-Super Admin cannot access tenant management

### 2. Tenant Admin Operations ✅
- Tenant Admin can access dashboard
- Tenant Admin can create sites
- Tenant Admin can create users
- Tenant Admin can update settings
- Tenant Admin cannot access other tenant's data

### 3. Data Isolation ⚠️ **CRITICAL**
- Users cannot see other tenant's orders
- Users cannot see other tenant's items
- Users cannot see other tenant's inventory
- Users cannot see other tenant's departments
- Users cannot see other tenant's sites
- Super Admin can see all tenant data

### 4. Middleware ✅
- Tenant context is set correctly
- Super Admin can access all tenants
- Users without tenants are logged out
- Tenant context from user's tenant_id
- Tenant context from user's site

### 5. End-to-End Flows ✅
- Complete tenant lifecycle
- Super Admin tenant switching
- Dashboard data filtering

### 6. Models ✅
- Tenant relationships
- User relationships
- Helper methods

## 🎯 Most Important Test

**Data Isolation Test** - This ensures tenants cannot see each other's data:

```bash
php artisan test tests/Feature/TenantDataIsolationTest.php
```

**If this test fails, you have a serious security issue!**

## 📝 Expected Results

All tests should pass. If any fail:

1. **Check error messages** - Read the output carefully
2. **Fix factories** - Ensure factories create correct relationships
3. **Fix models** - Ensure models have correct relationships
4. **Fix middleware** - Ensure TenantContext works correctly

## 🔍 Test Coverage Summary

- **Total Tests:** 50+ test cases
- **Feature Tests:** 5 test classes
- **Unit Tests:** 2 test classes
- **Factories:** 8 factories
- **Coverage:** Tenant management, data isolation, middleware, models, end-to-end flows

## ⚠️ Known Issues & Fixes

### Issue: Factory creates circular dependencies
**Fix:** Factories use relationships correctly - Laravel will handle the creation order.

### Issue: Tests fail with "tenant_id" not found
**Fix:** Ensure migrations have been run:
```bash
php artisan migrate:fresh
```

### Issue: Permission errors
**Fix:** Clear permission cache:
```bash
php artisan permission:cache-reset
```

## 📚 Full Documentation

See `TESTING_GUIDE.md` for comprehensive testing documentation.

## ✅ Success Criteria

Tests are successful when:
- ✅ All tests pass
- ✅ Data isolation tests pass (critical!)
- ✅ No permission errors
- ✅ No factory errors
- ✅ Middleware works correctly

**Run the tests now to verify everything works!**

```bash
php artisan test
```
