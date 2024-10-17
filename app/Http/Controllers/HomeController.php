<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $total_properties = Property::count();
        $sale_properties = Property::where('status', '=','Sale')->count();
        $rent_properties = Property::where('status', '=','Rent')->count();
        return view('home', compact('total_properties','sale_properties','rent_properties'));
    }
}
