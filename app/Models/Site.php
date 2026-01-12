<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory, TenantScope;
    
    protected $fillable = [
        'name',
        'site_code',
        'tenant_id',
        'updated_at'
    ];

    /**
     * Get the tenant that owns the site
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get all users for this site
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        static::bootTenantScope();
    }
}
