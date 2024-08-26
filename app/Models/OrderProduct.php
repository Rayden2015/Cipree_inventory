<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected  $fillable = ['order_id','product_id','quantity','prec_stage','tailor_id','finisher_id','meas','sowing_for','descs','site_id'];

   
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function products()
    {
    	return $this->belongsToMany('App\Product','order_products')->withPivot('quantity');
    }


}
