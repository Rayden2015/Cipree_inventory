<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentAuthoriserController extends Controller
{
    public function all_requests()
    {
        $site_id = Auth::user()->site->id;
        $all_requests = Order::where('status', '=', 'Requested')->where('site_id', '=', $site_id)->latest()->paginate(15);
        return view('department_authorser.all_requests', compact('all_requests'));
    }

}
