<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'status',
        'settings',
        'description',
        'contact_email',
        'contact_phone',
        'contact_name',
        'trial_ends_at',
        'logo_path',
        'primary_color',
        'secondary_color',
    ];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tenant) {
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    /**
     * Get all users for this tenant (both direct tenant admins and site users)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all sites for this tenant
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    /**
     * Get tenant admin users (users directly assigned to tenant)
     */
    public function tenantAdmins()
    {
        return $this->users()->whereNull('site_id')->whereHas('roles', function ($query) {
            $query->where('name', 'Tenant Admin');
        });
    }

    /**
     * Check if tenant is active
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Get all orders for this tenant
     */
    public function orders()
    {
        return Order::whereHas('user', function ($query) {
            $query->where('tenant_id', $this->id);
        });
    }
}
