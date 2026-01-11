#!/bin/bash

# Quick script to run all multi-tenancy tests

echo "🧪 Running Multi-Tenancy Test Suite..."
echo "========================================"
echo ""

echo "1️⃣  Running Tenant Management Tests..."
php artisan test tests/Feature/TenantManagementTest.php
echo ""

echo "2️⃣  Running Tenant Admin Tests..."
php artisan test tests/Feature/TenantAdminTest.php
echo ""

echo "3️⃣  Running Data Isolation Tests (CRITICAL) ⚠️..."
php artisan test tests/Feature/TenantDataIsolationTest.php
echo ""

echo "4️⃣  Running Middleware Tests..."
php artisan test tests/Feature/TenantMiddlewareTest.php
echo ""

echo "5️⃣  Running End-to-End Tests..."
php artisan test tests/Feature/TenantEndToEndTest.php
echo ""

echo "6️⃣  Running Model Unit Tests..."
php artisan test tests/Unit/TenantModelTest.php
php artisan test tests/Unit/UserModelTest.php
echo ""

echo "✅ All Multi-Tenancy Tests Complete!"
echo ""
echo "💡 Tip: Run 'php artisan test' to run all tests including existing ones."
