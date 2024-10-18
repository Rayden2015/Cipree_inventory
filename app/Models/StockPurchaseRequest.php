<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockPurchaseRequest extends Model
{
    use HasFactory;


    protected $fillable = [
    'inventory_id', 'description', 'quantity','make','price',
    'model','serial_number','tax','tax2','tax3',
    'unit_price','currency','supplier_id','comments',
    'type_of_purchase','enduser_id','status','intended_recipient','user_id','image','order_id','purchasing_order_number','delivery_reference_number','supplied_on','approved_on','approved_by','requested_by','requested_on',
    'invoice_number','delivered_by','delivered_on','authoriser_remarks','site_id','updated_at'];


    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function request_by(){
        return $this->belongsTo(User::class,'requested_by');
    }

    public function approve_by(){
        return $this->belongsTo(User::class,'approved_by');
    }
    public function user(){
        return $this->belongsTo(User::class,'delivered_by');
    }

    public function enduser(){
        return $this->belongsTo(Enduser::class,'enduser_id');
    }

}

