<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enduser extends Model
{
    use HasFactory;
    protected $fillable = [
        'asset_staff_id','name_description','department','section','model','serial_number','manufacturer', 'type','designation','status','site_id','department_id','section_id','enduser_category_id','updated_at'
    ];

    public function site(){
        return $this->belongsTo(Site::class,'site_id');
    }

    public function departmente(){
        return $this->belongsTo(Department::class,'department_id');
    }

    public function sectione(){
        return $this->belongsTo(Section::class,'section_id');
    }
    public function ed_category(){
        return $this->belongsTo(EndUsersCategory::class,'enduser_category_id');
    }
}
