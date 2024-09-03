<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    protected $fillable = [
'location_id','waybill','designation','codes','items','part_number','category_id','uom','quantity','supplier_id',
'dollar_rate','unit_cost_exc_vat_gh','unit_cost_exc_vat_usd',
'total_value_gh','total_value_usd','srf','erf','ats','drq',
'po_number','grn_number','invoice_number','delivered_by','supplier_id','trans_type','remarks','date','billing_currency','enduser_id','user_id','exchange_rate','site_id','manual_remarks','updated_at'
    ];

protected $attributes = [
    'billing_currency'=>'Dollar'
];

    public function location(){
        return $this->belongsTo(Location::class);
    }
    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function delivery(){
        return $this->belongsTo(User::class);
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function enduser(){
        return $this->belongsTo(Enduser::class,'enduser_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function item(){
        return $this->belongsTo(Item::class,'item_id'); 
    }
    public function editedby(){
        return $this->belongsTo(User::class,'edited_by');
    }
    public function site(){
        return $this->belongsTo(Site::class,'site_id');
    }
}
