<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory, TenantScope;

    protected $fillable = [
        'work_order_number',
        'title',
        'description',
        'status',
        'priority',
        'asset_state',
        'requested_date',
        'due_date',
        'completed_date',
        'work_done_details',
        'asset_enduser_id',
        'responsible_enduser_id',
        'site_id',
        'tenant_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'requested_date' => 'datetime',
        'due_date' => 'datetime',
        'completed_date' => 'datetime',
        'asset_down_since' => 'datetime',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function asset()
    {
        return $this->belongsTo(Enduser::class, 'asset_enduser_id');
    }

    public function responsiblePerson()
    {
        return $this->belongsTo(Enduser::class, 'responsible_enduser_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function storeRequests()
    {
        return $this->hasMany(Sorder::class, 'work_order_number', 'work_order_number');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        static::bootTenantScope();
    }
}

