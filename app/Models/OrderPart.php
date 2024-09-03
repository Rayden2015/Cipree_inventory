<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPart extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id','part_id','description','quantity','make','model','serial_number','unit_price','comments','request_number','remarks','priority','prefix','part_number','uom','site_id','uom_id','updated_at'
    ];
    protected $attributes = [
        'uom' => null,  // Default value for uom
    ];

    public function parts(){
        return $this->belongsTo(Part::class,'part_id');
    }


    public function uoms(){
        return $this->belongsTo(Uom::class,'uom_id');
    }
}
