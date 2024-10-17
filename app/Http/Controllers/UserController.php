<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'=>'email|unique:users,email,except,id',
            'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
        ]);
        $user = new User();
        $user->name = $request->name;
        $user->notes = $request->notes;
        $user->address = $request->address;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->mobile = $request->mobile;
        $user->skype = $request->skype;
        $user->facebook_url = $request->facebook_url;
        $user->instagram_url = $request->instagram_url;
        $user->snapchat_url = $request->snapchat_url;
        $user->twitter_url = $request->twitter_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->role_id = $request->role_id;
        $user->save();

        if ($request->picture) {
            $imageName = UploadHelper::upload($request->picture, 'user-' . $user->id, 'images/users');
            $user->picture = $imageName;
            $user->save();
        }
        return redirect()->route('users.index');

    }

    /**
     * Display the specified resource
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
        $user = User::find($id);
        $roles = Role::all();
        return view('users.edit',compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $id,

            'picture' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
        ]);
        $user->name = $request->name;
        $user->address = $request->address;
        $user->notes = $request->notes;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->phone = $request->phone;
        $user->mobile = $request->mobile;
        $user->skype = $request->skype;
        $user->facebook_url = $request->facebook_url;
        $user->instagram_url = $request->instagram_url;
        $user->snapchat_url = $request->snapchat_url;
        $user->twitter_url = $request->twitter_url;
        $user->linkedin_url = $request->linkedin_url;
        $user->role_id = $request->role_id;
        if ($request->picture) {
            // Delete the previous image stored & Upload this image
            $imageName = UploadHelper::update($request->picture, 'user-' . $user->id, 'images/users', $user->picture);

            $user->picture = $imageName;
            $user->save();
        }


        $user->update();
        return redirect()->route('users.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user =  User::find($id);
        // unlink("images/users/" . $user->image);

        User::where("id", $user->id)->delete();

        return redirect()->route('users.index');
    }
}
