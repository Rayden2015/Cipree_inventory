<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EndUsersCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','description','site_id','updated_at'
    ];

}
