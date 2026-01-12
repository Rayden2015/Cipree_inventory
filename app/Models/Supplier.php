<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, TenantScope;
    protected $fillable = [
        'name','address','location','tel','phone','email','items_supplied','contact_person','primary_currency',
        'comp_reg_no','vat_reg_no','item_cat1','item_cat2','item_cat3','site_id','tenant_id','updated_at'
    ];

    /**
     * Get the tenant that owns the supplier
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
