<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait TenantScope
{
    /**
     * Cache column existence checks to avoid repeated schema introspection
     * (which can be expensive and blow up memory under heavy query volumes).
     *
     * @var array<string, bool>
     */
    protected static array $__tenantScopeHasTenantColumnCache = [];

    /**
     * @var array<string, bool>
     */
    protected static array $__tenantScopeHasSiteColumnCache = [];

    /**
     * Boot the tenant scope
     */
    protected static function bootTenantScope()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            // CRITICAL: Avoid calling Auth::user() when the guard is not yet authenticated.
            // During login, Laravel queries the User provider; if User is tenant-scoped and we call
            // Auth::user() here, it can recurse and exhaust memory.
            if (! Auth::check()) {
                return;
            }

            $ctx = static::resolveTenantScopeAuthContext();
            if (! $ctx) {
                return;
            }

            // Super Admin can access all tenants (no filtering)
            if ($ctx['is_super_admin']) {
                return;
            }

            if ($ctx['tenant_id'] && static::hasTenantColumn()) {
                $builder->where(static::getTenantColumn(), $ctx['tenant_id']);
            }

            /**
             * Site scoping (within a tenant)
             * - Applies only if the model has a site_id column
             * - Bypassed by Super Admin (handled above), Tenant Admin, and any roles configured to bypass
             * - Uses session('current_site_id') when present, otherwise the user's assigned site_id
             */
            if (static::hasSiteColumn() && ! $ctx['can_bypass_site'] && $ctx['site_id']) {
                $builder->where('site_id', $ctx['site_id']);
            }
        });
    }

    /**
     * Resolve tenant/site scope flags once per HTTP request.
     * Calling Spatie role checks inside every global scope application can recurse / explode memory
     * on pages that issue many queries (dashboards).
     */
    protected static function resolveTenantScopeAuthContext(): ?array
    {
        if (! Auth::check()) {
            return null;
        }

        $request = function_exists('request') ? request() : null;
        // Key by authenticated user id: in tests the same Request instance can be reused across
        // actingAs() switches; a single global cache key would leak the first user's scope flags.
        $cacheKey = '__tenant_scope_auth_ctx_' . (Auth::id() ?? 'guest');

        if ($request && $request->attributes->has($cacheKey)) {
            return $request->attributes->get($cacheKey);
        }

        $user = Auth::user();

        $bypassRoles = config('scoping.site_bypass_roles', ['Super Admin', 'Tenant Admin']);
        $isSuperAdmin = $user->hasRole('Super Admin');
        $isTenantAdmin = $user->hasRole('Tenant Admin');
        $canBypassSiteScope = $isTenantAdmin || (method_exists($user, 'hasAnyRole') && $user->hasAnyRole($bypassRoles));

        $tenantId = session('current_tenant_id');
        if (! $tenantId && ! $isSuperAdmin) {
            $tenantId = $user->getCurrentTenant()?->id;
        }

        $siteId = session('current_site_id') ?? $user->site_id;

        $ctx = [
            'is_super_admin' => $isSuperAdmin,
            'can_bypass_site' => $canBypassSiteScope,
            'tenant_id' => $tenantId,
            'site_id' => $siteId,
        ];

        if ($request) {
            $request->attributes->set($cacheKey, $ctx);
        }

        return $ctx;
    }
    
    /**
     * Check if model has tenant_id column
     */
    protected static function hasTenantColumn(): bool
    {
        $key = static::class;
        if (array_key_exists($key, static::$__tenantScopeHasTenantColumnCache)) {
            return static::$__tenantScopeHasTenantColumnCache[$key];
        }

        try {
            $instance = new static;
            return static::$__tenantScopeHasTenantColumnCache[$key] = Schema::hasColumn($instance->getTable(), 'tenant_id');
        } catch (\Exception $e) {
            // If table doesn't exist yet (during migrations), return false
            return static::$__tenantScopeHasTenantColumnCache[$key] = false;
        }
    }

    /**
     * Check if model has site_id column
     */
    protected static function hasSiteColumn(): bool
    {
        $key = static::class;
        if (array_key_exists($key, static::$__tenantScopeHasSiteColumnCache)) {
            return static::$__tenantScopeHasSiteColumnCache[$key];
        }

        try {
            $instance = new static;
            return static::$__tenantScopeHasSiteColumnCache[$key] = Schema::hasColumn($instance->getTable(), 'site_id');
        } catch (\Exception $e) {
            return static::$__tenantScopeHasSiteColumnCache[$key] = false;
        }
    }
    
    /**
     * Get the tenant column name
     */
    protected static function getTenantColumn(): string
    {
        return 'tenant_id';
    }
    
    /**
     * Query without tenant scope (for Super Admin)
     */
    public static function withoutTenantScope()
    {
        return static::withoutGlobalScope('tenant');
    }
    
    /**
     * Query all tenants (for Super Admin) - alias for withoutTenantScope
     */
    public static function allTenants()
    {
        return static::withoutGlobalScope('tenant');
    }
    
    /**
     * Query for specific tenant (bypasses scope and filters by tenant_id)
     */
    public static function forTenant($tenantId)
    {
        return static::withoutGlobalScope('tenant')->where('tenant_id', $tenantId);
    }
}
