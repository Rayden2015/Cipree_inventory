<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalTax extends Model
{
    use HasFactory;
    protected $fillable = [
        'tax_id','sub_total','grand_total','site_id'
    ];

    public function tax(){
        return $this->belongsTo(Tax::class, 'tax_id');
    }
}
