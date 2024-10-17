<?php

namespace App\Helpers;

use App\Models\ClientPayment;
use App\Models\PaymentRecord;
use Illuminate\Support\Facades\DB;

class Pay
{
    // public static function getYears($st_id)
    // {
    //     return PaymentRecord::where(['student_id' => $st_id])->pluck('year')->unique();
    // }

    public static function genRefCode()
    {
        return date('Y') . mt_rand(10000, 999999);
    }
    public static function genRefCode1()
    {
        return date('H') . mt_rand(10000, 999999);
    }

public static function getOrder($id){

    return  ClientPayment::where('id',$id)->sum('order_id');
}

}
