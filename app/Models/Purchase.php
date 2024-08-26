<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'part_id', 'description', 'quantity','make',
        'model','serial_number','tax','tax2','tax3',
        'unit_price','currency','supplier_id','comments',
        'type_of_purchase','enduser_id','status','intended_recipient','user_id','image','approved_by','approved_on','site_id'
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
    public function requested(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function approvedby(){
        return $this->belongsTo(User::class,'approved_by');
    }
}
