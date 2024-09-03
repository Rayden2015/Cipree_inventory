<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PorderPart extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id','part_id','description','quantity','make','model','serial_number','unit_price','comments','request_number','remarks','priority','prefix','part_number','purchasing_order_number','sub_total','grand_total','discount','rate','site_id','updated_at'
    ];

    public function parts(){
        return $this->belongsTo(Part::class,'part_id');
    }
}
