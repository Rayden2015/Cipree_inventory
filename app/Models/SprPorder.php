<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SprPorder extends Model
{
    use HasFactory;
    use HasFactory;
    protected $fillable = [
        'part_id', 'description', 'quantity','make',
        'model','serial_number','tax','tax2','tax3',
        'unit_price','currency','supplier_id','comments',
        'type_of_purchase','enduser_id','status','intended_recipient','user_id','image','order_id','purchasing_order_number','delivery_reference_number',
        'invoice_number','work_order_ref','suppliers_reference','po_number','date_created','site_id','created_by','is_draft','notes'
    ];


    public function part(){
        return $this->belongsTo(Part::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function enduser(){
        return $this->belongsTo(Enduser::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function site(){
        return $this->belongsTo(Site::class);
    }

}
