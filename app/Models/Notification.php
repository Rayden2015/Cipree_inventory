<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'title','user_id','read_at','site_id','updated_at'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
