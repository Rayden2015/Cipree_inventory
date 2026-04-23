<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Auth\ThrottlesLogins;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Foundation\Auth\ThrottlesLogins as ThrottlesLoginsTrait;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, ThrottlesLoginsTrait, Authorizable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'department_id',
        'section_id',
        'password',
        'phone',
        'address',
        'location',
        'image',
        'role_id',
        'status',
        'staff_id',
        'tenant_id',
        'site_id',
        'add_admin',
        'add_site_admin',
        'add_requester',
        'add_finance_officer',
        'add_store_officer',
        'add_purchasing_officer',
        'add_authoriser',
        'add_store_assistant',
        'add_procurement_assistant',
        'last_successful_login',
        'last_failed_login',
        'failed_login_attempts',
        'banner_dismissed_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function enduser()
    {
        return $this->belongsTo(Enduser::class, 'enduser_id');
    }
    public function request_by()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    /**
     * Get the tenant that owns the user
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the current tenant for the user (from tenant_id or site->tenant_id)
     */
    public function getCurrentTenant()
    {
        // First, check direct tenant_id
        if ($this->tenant_id) {
            return $this->tenant ?: Tenant::find($this->tenant_id);
        }
        
        // If no direct tenant_id, try to get from site
        if ($this->site_id) {
            // Load site relationship if not already loaded
            // Use withoutTenantScope to avoid circular dependency when Site model has TenantScope
            // Check if relation is loaded to avoid triggering it (which would trigger scope)
            if ($this->relationLoaded('site')) {
                $site = $this->site;
            } else {
                $site = Site::withoutTenantScope()->find($this->site_id);
            }
            if ($site && $site->tenant_id) {
                return $site->tenant ?? Tenant::find($site->tenant_id);
            }
        }
        
        return null;
    }

    /**
     * Check if user is a Super Admin (can access all tenants)
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is a Tenant Admin
     */
    public function isTenantAdmin()
    {
        return $this->hasRole('Tenant Admin');
    }

    /**
     * Limit user listings to the authenticated viewer's tenant (and site unless Tenant Admin).
     * Intentionally NOT a global scope: applying TenantScope to User interacts badly with auth resolution.
     */
    public function scopeVisibleToAuth(Builder $query): Builder
    {
        $user = auth()->user();
        if (! $user) {
            return $query->whereRaw('1 = 0');
        }
        if ($user->hasRole('Super Admin')) {
            return $query;
        }

        $tenantId = session('current_tenant_id') ?? $user->getCurrentTenant()?->id;
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($user->site_id && ! $user->hasRole('Tenant Admin')) {
            $query->where('site_id', $user->site_id);
        }

        return $query;
    }
}
