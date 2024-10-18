<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\UploadHelper;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class MyAccountController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function index()
    {
        try {
            $my = Auth::user();
            Log::info('MyAccountController | index', [
                'user_details' => $my,
                'message' => 'Viewing user account details'
            ]);

            return view('myaccount.index', compact('my'));
        } catch (\Exception $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('MyAccountController | Index() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function update(Request $request, $id)
    {
        try {
            $my = User::find($id);
            $authid = Auth::user()->name;

            Log::info('MyAccountController | update (Before Editing)', [
                'edited_by' => $authid,
                'details_before_edit' => $my,
                'message' => 'Before editing user info'
            ]);

            $this->validate($request, [
                'phone' => 'sometimes|nullable|string|min:6|max:20',
                'email' => 'sometimes|nullable|email|max:100|unique:users,id',
                'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
                'address' => 'nullable',
            ]);

            $my->email = $request->email;
            $my->phone = $request->phone;
            $my->address = $request->address;

            if ($request->image) {
                // Delete the previous image stored & Upload this image
                $imageName = UploadHelper::update($request->image, 'user-' . $my->id, 'images/users', $my->image);
                $my->image = $imageName;
                $my->save();
            }

            $my->update();

            Log::info('MyAccountController | update (After Editing)', [
                'edited_by' => $authid,
                'details_after_edit' => $request->all(),
                'message' => 'After editing user info'
            ]);

           
            return redirect()->route('myaccounts.index')->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('MyAccountController | Update() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function changepassword(Request $request)
    {
        try {
            $this->validate($request, [
                'current_password' => 'required|string',
                'new_password' => 'required|confirmed|min:8|string'
            ]);

            $auth = Auth::user();

            // The passwords matches
            if (!Hash::check($request->get('current_password'), $auth->password)) {
                return back()->with('error', 'Current Password is Invalid');
            }

            // Current password and new password same
            if (strcmp($request->get('current_password'), $request->new_password) == 0) {
                return redirect()->back()->with('error', 'New Password cannot be same as your current password.');
            }

            $user =  User::find($auth->id);
            $authid = Auth::user()->name;

            Log::info('MyAccountController | changepassword (Before Change)', [
                'edited_by' => $authid,
                'details_before_change' => User::find($auth->id),
                'message' => 'Before changing user password'
            ]);

            $user->password =  Hash::make($request->new_password);
            $user->save();

            Log::info('MyAccountController | changepassword (After Change)', [
                'edited_by' => $authid,
                'details_after_change' => $request->all(),
                'message' => 'After changing user password'
            ]);

            
            return back()->withSuccess('Password Changed Successfully');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('MyAccountController | ChangePassword() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
}
