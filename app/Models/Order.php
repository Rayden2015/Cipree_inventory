<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'tax', 'tax2', 'tax3', 'currency', 'supplier_id', 'type_of_purchase', 'enduser_id', 'status', 'image', 'user_id', 'request_number', 'request_date', 'approval_status', 'work_order_ref', 'approved_by', 'approved_on', 'site_id','updated_at'

    ];
    public function products()
    {
        return $this->belongsToMany('App\Part', 'order_parts')->withPivot('quantity');
    }

    public function client()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function product()
    {
        return $this->belongsTo(Part::class);
    }

    public function enduser()
    {
        return $this->belongsTo(Enduser::class);
    }

    public function requested()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedby()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

}
