<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory, TenantScope;
   
    protected $fillable = [
        'item_description','item_uom','item_category_id','item_stock_code','item_part_number','added_by','modified_by','reorder_level','max_stock_level','lead_time_days','valuation_method','stock_quantity','amount','new_category','site_id','tenant_id','uom_id','updated_at'
    ];

    public function category(){
        return $this->belongsTo(Category::class,'item_category_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'added_by');
    }
    public function modified(){
        return $this->belongsTo(User::class,'modified_by');
    }
    public function location(){
        return $this->belongsTo(Location::class,'location_id');
    }
    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class); // Adjust if necessary
    }

    /**
     * Get the tenant that owns the item
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
