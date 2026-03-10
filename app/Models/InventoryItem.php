<?php

namespace App\Models;

use App\Models\Concerns\TenantScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory, TenantScope;

    protected $casts = [
        'last_updated_at' => 'datetime',
    ];

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_VOIDED = 'Voided';
    public const STATUS_ADJUSTMENT = 'Adjustment';

    protected $fillable = [
        'location_id', 'inventory_id', 'description', 'part_number', 'quantity', 'codes', 'uom', 'category_id',
        'unit_cost_exc_vat_gh', 'unit_cost_exc_vat_usd', 'total_value_gh', 'total_value_usd',
        'srf', 'erf', 'ats', 'drq', 'remarks', 'amount', 'discount', 'enduser_id','stock_code','item_id','before_discount','tenant_id','site_id','last_updated_by','last_updated_at','updated_at',
        'status', 'source_inventory_item_id', 'current_net_balance',
    ];

    /**
     * Requester view: only Active and Adjustment (clean aggregated view).
     */
    public function scopeActiveForRequester($query)
    {
        return $query->whereIn('status', [self::STATUS_ACTIVE, self::STATUS_ADJUSTMENT]);
    }

    /**
     * Only Active lines (e.g. for GRN picking with balance > 0).
     */
    public function scopeActiveOnly($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Auditor/Supervisor: all statuses (full forensic trail).
     */
    public function scopeAuditorAll($query)
    {
        return $query; // no status filter
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function enduser()
    {
        return $this->belongsTo(Enduser::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id');
    }

    public function suppler()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function item(){
        return $this->belongsTo(Item::class,'item_id');
    }
    public function item_view(){
        return $this->belongsTo(Item::class,'id');
    }
    public function site()
    {
        return $this->belongsTo(Site::class,'site_id');
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function sourceInventoryItem()
    {
        return $this->belongsTo(InventoryItem::class, 'source_inventory_item_id');
    }

    public function correctionRequests()
    {
        return $this->hasMany(InventoryCorrectionRequest::class);
    }

    /**
     * Issued quantity from this line (sorder_parts.inventory_id = inventory_items.id).
     */
    public function getIssuedQuantityAttribute(): int
    {
        return (int) \DB::table('sorder_parts')->where('inventory_id', $this->id)->sum('qty_supplied');
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        static::bootTenantScope();
    }
}
