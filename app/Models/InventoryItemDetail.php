<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItemDetail extends Model
{
    use HasFactory;
     protected $fillable = [
        'location_id', 'inventory_id', 'description', 'part_number', 'quantity', 'codes', 'uom', 'category_id',
        'unit_cost_exc_vat_gh', 'unit_cost_exc_vat_usd', 'total_value_gh', 'total_value_usd',
        'srf', 'erf', 'ats', 'drq', 'remarks', 'amount', 'discount', 'enduser_id','stock_code','item_id','before_discount','site_id'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function enduser()
    {
        return $this->belongsTo(Enduser::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }

    public function site()
    {
        return $this->belongsTo(Site::class,'site_id');
    }

    public function suppler()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }

}
