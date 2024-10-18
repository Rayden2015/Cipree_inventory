<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $casts = [
        'reviewed' => 'boolean',
        'screenshot'=>'string',
        'user_info' => 'array'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id'); 
    }
}