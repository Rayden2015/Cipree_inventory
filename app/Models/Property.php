<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $fillable = [
        'description','location','property_type','status',
        'area','beds','baths','garage','balcony',
        'deck','parking','outdoor_kitchen',
        'tennis_court','sun_room','flat_tv',
        'internet','user_id','cover','price','video','country_id','state_id'
    ];
    public function images(){
        return $this->hasMany(Image::class);
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function country(){
        return $this->belongsTo(Country::class,'country_id');
    }
    public function state(){
        return $this->belongsTo(State::class,'state_id');
    }
}
