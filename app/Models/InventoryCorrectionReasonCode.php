<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCorrectionReasonCode extends Model
{
    protected $fillable = ['code', 'type', 'use_case'];

    public function correctionRequests()
    {
        return $this->hasMany(InventoryCorrectionRequest::class, 'reason_code_id');
    }
}
