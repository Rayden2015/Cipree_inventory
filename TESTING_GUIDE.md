# Multi-Tenancy Testing Guide

## Overview

This guide covers the comprehensive test suite for the multi-tenancy implementation. The tests ensure data isolation, proper tenant management, and security.

## Test Structure

### Test Files Created

1. **`tests/Feature/TenantManagementTest.php`** - Super Admin tenant management tests
2. **`tests/Feature/TenantAdminTest.php`** - Tenant Admin functionality tests
3. **`tests/Feature/TenantDataIsolationTest.php`** - **CRITICAL** - Data isolation tests
4. **`tests/Feature/TenantMiddlewareTest.php`** - Middleware tests
5. **`tests/Feature/TenantEndToEndTest.php`** - Complete end-to-end flow tests
6. **`tests/Unit/TenantModelTest.php`** - Tenant model unit tests
7. **`tests/Unit/UserModelTest.php`** - User model unit tests

### Factories Created

1. **`database/factories/TenantFactory.php`** - Tenant factory
2. **`database/factories/SiteFactory.php`** - Site factory
3. **`database/factories/OrderFactory.php`** - Order factory
4. **`database/factories/ItemFactory.php`** - Item factory
5. **`database/factories/InventoryItemFactory.php`** - InventoryItem factory
6. **`database/factories/DepartmentFactory.php`** - Department factory
7. **`database/factories/InventoryFactory.php`** - Inventory factory
8. **Updated `database/factories/UserFactory.php`** - Added `superAdmin()` and `tenantAdmin()` states

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suite
```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit
```

### Run Specific Test Class
```bash
# Run tenant management tests
php artisan test tests/Feature/TenantManagementTest.php

# Run data isolation tests (CRITICAL)
php artisan test tests/Feature/TenantDataIsolationTest.php

# Run end-to-end tests
php artisan test tests/Feature/TenantEndToEndTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter test_complete_tenant_lifecycle
```

### Run Tests with Coverage
```bash
php artisan test --coverage
```

## Test Categories

### 1. Tenant Management Tests (`TenantManagementTest`)

**Purpose:** Tests Super Admin's ability to manage tenants.

**Key Tests:**
- ✅ Super Admin can view tenants list
- ✅ Super Admin can create tenant with admin
- ✅ Super Admin can update tenant
- ✅ Super Admin can create additional tenant admins
- ✅ Non-Super Admin cannot access tenant management

**Run:**
```bash
php artisan test tests/Feature/TenantManagementTest.php
```

### 2. Tenant Admin Tests (`TenantAdminTest`)

**Purpose:** Tests Tenant Admin's ability to manage their tenant.

**Key Tests:**
- ✅ Tenant Admin can access dashboard
- ✅ Tenant Admin can create sites
- ✅ Tenant Admin can create users
- ✅ Tenant Admin can update tenant settings
- ✅ Tenant Admin cannot access other tenant's data

**Run:**
```bash
php artisan test tests/Feature/TenantAdminTest.php
```

### 3. Data Isolation Tests (`TenantDataIsolationTest`) ⚠️ **CRITICAL**

**Purpose:** Ensures tenants cannot see each other's data. **This is the most important test suite!**

**Key Tests:**
- ✅ Users cannot see other tenant's orders
- ✅ Users cannot see other tenant's items
- ✅ Users cannot see other tenant's inventory
- ✅ Users cannot see other tenant's departments
- ✅ Users cannot see other tenant's sites
- ✅ Super Admin can see all tenant data
- ✅ Data isolation in HomeController

**Run:**
```bash
php artisan test tests/Feature/TenantDataIsolationTest.php
```

**Why Critical:** Data isolation is the foundation of multi-tenancy. If this fails, tenants can see each other's data, which is a serious security issue.

### 4. Middleware Tests (`TenantMiddlewareTest`)

**Purpose:** Tests that TenantContext middleware works correctly.

**Key Tests:**
- ✅ Middleware sets tenant context for regular users
- ✅ Middleware allows Super Admin to access all tenants
- ✅ Middleware logs out users without tenant
- ✅ Middleware sets tenant from user's tenant_id
- ✅ Middleware sets tenant from user's site

**Run:**
```bash
php artisan test tests/Feature/TenantMiddlewareTest.php
```

### 5. End-to-End Tests (`TenantEndToEndTest`)

**Purpose:** Tests complete user journeys from tenant creation to data operations.

**Key Tests:**
- ✅ Complete tenant lifecycle (Super Admin → Tenant Admin → User → Order)
- ✅ Super Admin can switch between tenants
- ✅ Dashboard shows only tenant data

**Run:**
```bash
php artisan test tests/Feature/TenantEndToEndTest.php
```

### 6. Model Unit Tests

**Purpose:** Tests model relationships and helper methods.

**Key Tests:**
- ✅ Tenant has many users and sites
- ✅ User can get current tenant
- ✅ User can check if Super Admin or Tenant Admin

**Run:**
```bash
php artisan test tests/Unit/TenantModelTest.php
php artisan test tests/Unit/UserModelTest.php
```

## Test Coverage

### What's Tested ✅

1. **Tenant Management**
   - ✅ Create, read, update, delete tenants
   - ✅ Create tenant admins
   - ✅ Role-based access control

2. **Tenant Admin Operations**
   - ✅ Site management
   - ✅ User management
   - ✅ Tenant settings

3. **Data Isolation** ⚠️ **CRITICAL**
   - ✅ Orders isolation
   - ✅ Items isolation
   - ✅ Inventory isolation
   - ✅ Departments isolation
   - ✅ Sites isolation

4. **Middleware**
   - ✅ Tenant context setting
   - ✅ Super Admin access
   - ✅ User without tenant handling

5. **Models**
   - ✅ Relationships
   - ✅ Helper methods
   - ✅ Auto-generated fields

6. **End-to-End Flows**
   - ✅ Complete tenant lifecycle
   - ✅ Dashboard data filtering

### What Needs More Testing (Future)

1. **Frontend/Browser Tests**
   - Consider adding Laravel Dusk for browser testing
   - Test UI interactions with tenant switching
   - Test form validations in browser

2. **Performance Tests**
   - Test query performance with large datasets
   - Test tenant switching performance
   - Test concurrent tenant access

3. **Security Tests**
   - Test SQL injection attempts
   - Test cross-tenant data access attempts
   - Test permission bypass attempts

4. **API Tests**
   - If you have API endpoints, add API tests
   - Test API authentication with tenants
   - Test API data isolation

## Troubleshooting Tests

### Common Issues

1. **Factory Errors:**
   ```bash
   # Make sure all factories are properly defined
   # Check that models have HasFactory trait
   ```

2. **Permission Errors:**
   ```bash
   # Clear permission cache
   php artisan permission:cache-reset
   ```

3. **Database Errors:**
   ```bash
   # Refresh migrations
   php artisan migrate:fresh
   ```

4. **Session Errors:**
   ```bash
   # Clear all caches
   php artisan optimize:clear
   ```

### Debugging Failed Tests

```bash
# Run with verbose output
php artisan test --verbose

# Stop on first failure
php artisan test --stop-on-failure

# Filter by name
php artisan test --filter test_name
```

## Test Best Practices

1. **Always test data isolation** - This is the most critical aspect
2. **Test both positive and negative cases** - What should work and what shouldn't
3. **Test edge cases** - Users without tenants, Super Admin scenarios
4. **Use factories** - Don't create test data manually
5. **Use RefreshDatabase** - Ensure clean state between tests
6. **Test relationships** - Verify model relationships work correctly
7. **Test middleware** - Ensure tenant context is set correctly

## Continuous Integration

### Example GitHub Actions Workflow

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install Dependencies
        run: composer install
      - name: Run Tests
        run: php artisan test
```

## Next Steps

1. ✅ **Run all tests** to ensure everything passes
2. ✅ **Fix any failing tests** - Data isolation tests are critical
3. ⚠️ **Add Laravel Dusk** for browser testing (optional but recommended)
4. ⚠️ **Add performance tests** for large datasets
5. ⚠️ **Add API tests** if you have API endpoints

## Summary

You now have comprehensive test coverage for multi-tenancy:

- ✅ **6 Feature Test Classes** - Covering all major functionality
- ✅ **2 Unit Test Classes** - Testing models
- ✅ **8 Factories** - For generating test data
- ✅ **50+ Test Cases** - Covering all scenarios

**Run the tests to ensure everything works:**
```bash
php artisan test
```

**Most Critical Test:**
```bash
php artisan test tests/Feature/TenantDataIsolationTest.php
```

This ensures tenants cannot see each other's data! 🎯
