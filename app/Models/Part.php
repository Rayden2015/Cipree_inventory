<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory, TenantScope;
    
    protected $fillable = [
        'supplier_id','name','description','site_id','location_id',
        'quantity','tenant_id','updated_at'
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function site(){
        return $this->belongsTo(Site::class);
    }
    public function location(){
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the tenant that owns the part
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
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
