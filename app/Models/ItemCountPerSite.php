<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemCountPerSite extends Model
{
    // Since the view is not managed by Laravel migrations, we disable timestamps
    public $timestamps = false;

    // Set the table name to your view name
    protected $table = 'item_count_per_site';

    // Optionally, if your view doesn't have a primary key, you can disable it
    protected $primaryKey = null;
    public $incrementing = false;
}
