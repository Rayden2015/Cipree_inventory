<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::latest()->limit(6)->get();
        return view('index', compact('properties'));
        // dd($properties);
    }

    public function about()
    {
        return view('about');
    }
    public function property()
    {
        $properties = Property::latest()->paginate(6);
        return view('properties',compact('properties'));
    }
    public function blogs()
    {
        $blogs = Blog::latest()->paginate(6);
        return view('blog-grid', compact('blogs'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function services(){
        return view('services');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
