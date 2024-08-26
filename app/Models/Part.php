<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id','name','description','site_id','location_id',
        'quantity','site_id'
    ];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function site(){
        return $this->belongsTo(Site::class);
    }
    public function location(){
        return $this->belongsTo(Location::class);
    }
}
