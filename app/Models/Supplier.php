<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name','address','location','tel','phone','email','items_supplied','contact_person','primary_currency',
        'comp_reg_no','vat_reg_no','item_cat1','item_cat2','item_cat3','site_id','updated_at'
    ];
}
