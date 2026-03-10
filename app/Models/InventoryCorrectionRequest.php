<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCorrectionRequest extends Model
{
    protected $fillable = [
        'inventory_item_id',
        'requested_by',
        'intended_quantity',
        'intended_unit_cost_exc_vat_gh',
        'reason_code_id',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'rejection_reason',
        'tenant_id',
        'site_id',
    ];

    protected $casts = [
        'intended_quantity' => 'decimal:2',
        'intended_unit_cost_exc_vat_gh' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function requestedByUser()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function reasonCode()
    {
        return $this->belongsTo(InventoryCorrectionReasonCode::class, 'reason_code_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }
}
