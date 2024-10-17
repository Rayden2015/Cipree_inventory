<?php

namespace App\Http\Controllers;

use App\Models\vc;
use App\Models\Image;
use App\Models\State;
use App\Models\Country;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\RedirectResponse;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::latest()->paginate(6);
        return view('property-grid', compact('properties'));
    }

    public function allproperties()
    {
        $properties = Property::latest()->paginate(15);
        return view('properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countries = Country::all();
        return view('properties.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'video' => 'sometimes|nullable|image|mimes:mp4,3gp,mpeg',
        ]);
        if ($request->hasFile("cover")) {
            $file = $request->file("cover");
            $imageName = time() . '_' . $file->getClientOriginalName();
            $file->move(\public_path("cover/"), $imageName);

            $auth_id = Auth::id();
            $property = new Property([
                "description" => $request->description,
                "price" => $request->price,
                "location" => $request->location,
                "property_type" => $request->property_type,
                "status" => $request->status,
                "area" => $request->area,
                "beds" => $request->beds,
                "baths" => $request->baths,
                "garage" => $request->garage,
                "balcony" => $request->balcony,
                "deck" => $request->deck,
                "parking" => $request->parking,
                "outdoor_kitchen" => $request->outdoor_kitchen,
                "tennis_court" => $request->tennis_court,
                "sun_room" => $request->sun_room,
                "flat_tv" => $request->flat_tv,
                "internet" => $request->internet,
                "country_id"=>$request->country_id,
                "state_id"=>$request->state_id,
                "user_id" => $auth_id,
                "cover" => $imageName,
                
            ]);
            $property->save();
        }
        if ($request->video) {
            $videoName = UploadHelper::upload($request->video, 'property-' . $property->id, 'video/properties');
            $property->video = $videoName;
            $property->save();
        }
        if ($request->hasFile("images")) {
            $files = $request->file("images");
            foreach ($files as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $request['property_id'] = $property->id;
                $request['image'] = $imageName;
                $file->move(\public_path("/images/properties"), $imageName);
                Image::create($request->all());
            }
        }
        return redirect()->route('property.allproperties');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $properties = Property::find($id);
        $images = Property::findOrFail($id)->with('images');
        return view('property-single', compact('properties', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $properties = Property::find($id);
        $countries = Country::all();
        $states = State::all();
        return view('properties.edit', compact('properties','states','countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        if ($request->hasFile("cover")) {
            if (File::exists("cover/" . $property->cover)) {
                File::delete("cover/" . $property->cover);
            }
            $file = $request->file("cover");
            $property->cover = time() . "_" . $file->getClientOriginalName();
            $file->move(\public_path("/cover"), $property->cover);
            $request['cover'] = $property->cover;
        }
        $auth_id = Auth::id();
        $property->update([
            "description" => $request->description,
            "price" => $request->price,
            "location" => $request->location,
            "property_type" => $request->property_type,
            "status" => $request->status,
            "area" => $request->area,
            "beds" => $request->beds,
            "baths" => $request->baths,
            "garage" => $request->garage,
            "balcony" => $request->balcony,
            "deck" => $request->deck,
            "parking" => $request->parking,
            "outdoor_kitchen" => $request->outdoor_kitchen,
            "tennis_court" => $request->tennis_court,
            "sun_room" => $request->sun_room,
            "flat_tv" => $request->flat_tv,
            "internet" => $request->internet,
            "country_id"=>$request->country_id,
            "state_id"=>$request->state_id,
            "user_id" => $auth_id,
            "cover" => $property->cover,
        ]);
        if ($request->video) {
            // Delete the previous image stored & Upload this image
            $videoName = UploadHelper::update($request->video, 'property-' . $property->id, 'video/properties', $property->video);

            $property->image = $videoName;
            $property->save();
        }
        if ($request->hasFile("images")) {
            $files = $request->file("images");
            foreach ($files as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $request["property_id"] = $id;
                $request["image"] = $imageName;
                $file->move(\public_path("/images/properties"), $imageName);
                Image::create($request->all());
            }
        }
        
        return redirect()->route('property.allproperties');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $property = Property::findOrFail($id);

        if (File::exists("cover/" . $property->cover)) {
            File::delete("cover/" . $property->cover);
        }
        $images = Image::where("property_id", $property->id)->get();
        foreach ($images as $image) {
            if (File::exists("images/" . $image->image)) {
                File::delete("images/" . $image->image);
            }
        }
        $property->delete();
        return back();
    }

    public function deleteimage($id)
    {
        $images = Image::findOrFail($id);
        if (File::exists("images/" . $images->image)) {
            File::delete("images/" . $images->image);
        }

        Image::find($id)->delete();
        return back();
    }

    public function deletecover($id)
    {
        $cover = Property::findOrFail($id)->cover;
        if (File::exists("cover/" . $cover)) {
            File::delete("cover/" . $cover);
        }
        return back();
    }
}
