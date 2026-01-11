# Multi-Tenancy Testing Guide

## Overview
This guide explains how to test multi-tenancy functionality both locally and in production.

## Production: Domain-Based Tenant Routing

### How It Works
- Each tenant has a `domain` field in the `tenants` table
- When a user accesses the application via a tenant's domain, the system:
  1. Checks the request domain against tenant domains
  2. Verifies the user belongs to that tenant
  3. Sets the tenant context automatically
  4. Super Admin bypasses domain checks

### Setting Up Domain for Tenant
1. When creating/editing a tenant, set the `domain` field (e.g., `tenant1.example.com`)
2. Ensure DNS points to your server
3. Configure web server (Apache/Nginx) to handle the domain

### Domain Matching Logic
The middleware matches domains in this order:
- Exact match: `tenant1.example.com`
- With www: `www.tenant1.example.com`
- Without www: `tenant1.example.com` (strips www if present)

## Local Testing Methods

### Method 1: Using Query Parameters (Easiest)
**Best for:** Quick testing, development

1. **Login as Super Admin**
   ```
   http://localhost:8000/login
   ```

2. **Switch tenant context via query parameter**
   ```
   http://localhost:8000/home?tenant_id=1
   http://localhost:8000/home?tenant_id=2
   ```

3. **Login as Tenant Admin**
   - Login normally - tenant context is set automatically from user's tenant

**Pros:**
- No configuration needed
- Works immediately
- Easy to switch between tenants

**Cons:**
- Requires manual query parameter
- Not realistic to production flow

---

### Method 2: Using Hosts File (Most Realistic)
**Best for:** Testing domain-based routing locally

1. **Edit hosts file**
   ```bash
   # macOS/Linux
   sudo nano /etc/hosts
   
   # Windows
   # C:\Windows\System32\drivers\etc\hosts
   ```

2. **Add entries for each tenant**
   ```
   127.0.0.1 tenant1.local
   127.0.0.1 tenant2.local
   127.0.0.1 tenant3.local
   ```

3. **Update tenant domains in database**
   ```sql
   UPDATE tenants SET domain = 'tenant1.local' WHERE id = 1;
   UPDATE tenants SET domain = 'tenant2.local' WHERE id = 2;
   ```

4. **Access via domain**
   ```
   http://tenant1.local:8000
   http://tenant2.local:8000
   ```

5. **Configure Laravel to accept these domains**
   - Update `.env` if needed
   - Or use `APP_ENV=local` (domain check only runs in production)

**Pros:**
- Realistic to production
- Tests actual domain routing
- No query parameters needed

**Cons:**
- Requires hosts file editing
- Need to update tenant domains

---

### Method 3: Using Subdomains with Valet/Laravel Herd
**Best for:** macOS/Linux with Valet or Laravel Herd

1. **Install Valet** (macOS)
   ```bash
   composer global require laravel/valet
   valet install
   ```

2. **Link your project**
   ```bash
   cd /path/to/inventory-v2
   valet link inventory
   ```

3. **Create subdomains**
   ```bash
   valet link tenant1-inventory
   valet link tenant2-inventory
   ```

4. **Update tenant domains**
   ```sql
   UPDATE tenants SET domain = 'tenant1-inventory.test' WHERE id = 1;
   UPDATE tenants SET domain = 'tenant2-inventory.test' WHERE id = 2;
   ```

5. **Access via subdomain**
   ```
   http://tenant1-inventory.test
   http://tenant2-inventory.test
   ```

**Pros:**
- Professional setup
- Automatic SSL with Valet
- Realistic subdomain routing

**Cons:**
- Requires Valet/Herd installation
- macOS/Linux only

---

### Method 4: Using Environment Variable Override
**Best for:** Testing domain routing without production environment

1. **Temporarily modify middleware** (for testing only)
   ```php
   // In TenantContext.php, change:
   $isProduction = app()->environment('production');
   // To:
   $isProduction = env('ENABLE_DOMAIN_CHECK', false);
   ```

2. **Set in .env**
   ```
   ENABLE_DOMAIN_CHECK=true
   ```

3. **Use hosts file method** (Method 2)

**Pros:**
- Can test domain routing locally
- Easy to toggle on/off

**Cons:**
- Requires code modification
- Not recommended for production code

---

## Testing Scenarios

### Scenario 1: Super Admin Access
1. Login as Super Admin
2. Should be able to access all tenants
3. Can switch tenant via `?tenant_id=X` query parameter
4. Domain check is bypassed

### Scenario 2: Tenant Admin Access
1. Login as Tenant Admin
2. Tenant context set automatically from user's tenant
3. In production: Domain must match tenant's domain
4. Can manage all sites in their tenant

### Scenario 3: Regular User Access
1. Login as regular user
2. Tenant context set from user's tenant
3. In production: Domain must match tenant's domain
4. Can only access their assigned site's data

### Scenario 4: Domain Mismatch (Production)
1. User belongs to Tenant A
2. Access via Tenant B's domain
3. Should be denied access
4. Redirected to login with error message

### Scenario 5: Data Isolation
1. Create data as Tenant A user
2. Switch to Tenant B context
3. Tenant B should not see Tenant A's data
4. Verify all queries filter by `tenant_id`

## Quick Test Checklist

- [ ] Create multiple tenants
- [ ] Create users for each tenant
- [ ] Login as Super Admin - can access all tenants
- [ ] Login as Tenant Admin - sees only their tenant's data
- [ ] Login as regular user - sees only their site's data
- [ ] Test domain routing (production or with hosts file)
- [ ] Test domain mismatch scenario
- [ ] Verify data isolation between tenants
- [ ] Test tenant switching (Super Admin only)
- [ ] Verify audit logs capture tenant context

## Troubleshooting

### Issue: Domain not matching
**Solution:**
- Check tenant's `domain` field in database
- Verify request host matches exactly
- Check for www prefix differences
- Review logs for domain matching details

### Issue: User can't access their tenant
**Solution:**
- Verify user has `tenant_id` set
- Check user's site belongs to correct tenant
- Review `getCurrentTenant()` method
- Check middleware logs

### Issue: Super Admin can't switch tenants
**Solution:**
- Use query parameter: `?tenant_id=X`
- Verify tenant exists
- Check session is working
- Review middleware logic

## Production Deployment Checklist

- [ ] All tenants have `domain` field set
- [ ] DNS records point to server
- [ ] Web server configured for all domains
- [ ] SSL certificates installed for all domains
- [ ] Environment set to `production`
- [ ] Domain-based routing tested
- [ ] Super Admin access verified
- [ ] Data isolation verified
- [ ] Error handling tested

## Notes

- Domain check only runs in `production` environment
- Local development uses user-based tenant assignment
- Super Admin always bypasses domain checks
- Domain matching is case-insensitive
- Supports both `www` and non-`www` domains
