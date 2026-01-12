<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

trait TenantScope
{
    /**
     * Boot the tenant scope
     */
    protected static function bootTenantScope()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = Auth::user();
            
            // Super Admin can access all tenants (no filtering)
            if ($user && !$user->isSuperAdmin()) {
                $tenantId = session('current_tenant_id') ?? $user->getCurrentTenant()?->id;
                
                if ($tenantId && static::hasTenantColumn()) {
                    $builder->where(static::getTenantColumn(), $tenantId);
                }
            }
        });
    }
    
    /**
     * Check if model has tenant_id column
     */
    protected static function hasTenantColumn(): bool
    {
        try {
            $instance = new static;
            return Schema::hasColumn($instance->getTable(), 'tenant_id');
        } catch (\Exception $e) {
            // If table doesn't exist yet (during migrations), return false
            return false;
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
