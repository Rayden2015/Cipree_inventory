<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Uom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'symbol',
        'conversion_factor',
        'measurement_type',
        'description',
        'is_default',
        'base_unit',
    ];
}
