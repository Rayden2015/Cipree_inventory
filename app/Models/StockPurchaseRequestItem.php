<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPurchaseRequestItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id','inventory_id','description','quantity','make','model','serial_number','unit_cost_exc_vat_gh','comments','request_number','remarks','priority','prefix','part_number','purchasing_order_number','sub_total','grand_total','discount','item_id','qty_supplied','site_id'
    ];

    public function item_parts(){
        return $this->belongsTo(InventoryItem::class,'inventory_id');
    }
    public function item_details(){
        return $this->belongsTo(Item::class,'item_id');
    }
    public function enduser(){
        return $this->belongsTo(Enduser::class,'enduser_id');
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class,'inventory_id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }

}
