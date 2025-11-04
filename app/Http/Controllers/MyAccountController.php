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
            $authid = Auth::user()->name;
            $authUserId = Auth::user()->id;

            // Log the update attempt
            Log::info('MyAccountController | update | Attempt started', [
                'user_id' => $id,
                'edited_by' => $authid,
                'request_data' => $request->except(['image'])
            ]);

            // Check if user exists
            $my = User::find($id);
            if (!$my) {
                Log::warning('MyAccountController | update | User not found', [
                    'user_id' => $id,
                    'edited_by' => $authid
                ]);
                return redirect()->back()->withError('Your account was not found. Please contact the administrator.');
            }

            // Verify the user is updating their own account
            if ($authUserId != $id) {
                Log::warning('MyAccountController | update | Unauthorized account update attempt', [
                    'user_id' => $id,
                    'attempted_by' => $authUserId
                ]);
                return redirect()->back()->withError('You can only update your own account information.');
            }

            // Store original user data for logging
            $originalUserData = $my->toArray();

            // Validate with proper uniqueness rules
            try {
                $this->validate($request, [
                    'phone' => 'sometimes|nullable|string|min:6|max:20|unique:users,phone,' . $id,
                    'email' => 'sometimes|nullable|email|max:100|unique:users,email,' . $id,
                    'image' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:10240', // 10MB max
                    'address' => 'nullable|string|max:500',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::warning('MyAccountController | update | Validation failed', [
                    'user_id' => $id,
                    'edited_by' => $authid,
                    'validation_errors' => $e->errors()
                ]);

                // Create user-friendly error messages
                $errors = $e->errors();
                $friendlyMessages = [];

                if (isset($errors['email'])) {
                    $friendlyMessages[] = 'The email address is already in use by another user.';
                }
                if (isset($errors['phone'])) {
                    $friendlyMessages[] = 'The phone number is already in use by another user. Please use a different phone number.';
                }
                if (isset($errors['image'])) {
                    $friendlyMessages[] = 'The uploaded image must be a valid image file (JPEG, PNG, GIF, JPG) and less than 10MB.';
                }
                if (isset($errors['address'])) {
                    $friendlyMessages[] = 'The address cannot exceed 500 characters.';
                }

                // If no specific friendly message, use generic validation error
                if (empty($friendlyMessages)) {
                    $friendlyMessages[] = 'Please check your input and try again.';
                }

                return redirect()->back()
                    ->withInput()
                    ->withError(implode(' ', $friendlyMessages));
            }

            Log::info('MyAccountController | update (Before Editing)', [
                'edited_by' => $authid,
                'details_before_edit' => $originalUserData,
                'message' => 'Before editing user info'
            ]);

            // Update basic information
            $my->email = $request->email;
            $my->phone = $request->phone;
            $my->address = $request->address;

            // Handle the image upload if an image is provided
            if ($request->hasFile('image')) {
                try {
                    // Check if directory exists and is writable
                    $uploadDir = 'images/users';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                        Log::info('MyAccountController | update | Created upload directory', [
                            'directory' => $uploadDir
                        ]);
                    }

                    if (!is_writable($uploadDir)) {
                        Log::error('MyAccountController | update | Upload directory not writable', [
                            'directory' => $uploadDir,
                            'user_id' => $id,
                            'edited_by' => $authid
                        ]);
                        return redirect()->back()
                            ->withError('Image upload failed due to server permissions. Please contact the administrator.');
                    }

                    // Delete the previous image stored & Upload this image
                    $imageName = UploadHelper::update($request->image, 'user-' . $my->id, $uploadDir, $my->image);
                    $my->image = $imageName;

                    Log::info('MyAccountController | update | Image uploaded', [
                        'user_id' => $id,
                        'edited_by' => $authid,
                        'image_name' => $imageName
                    ]);
                } catch (\Exception $e) {
                    Log::error('MyAccountController | update | Image upload failed', [
                        'user_id' => $id,
                        'edited_by' => $authid,
                        'error_message' => $e->getMessage(),
                        'stack_trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->back()
                        ->withError('Image upload failed. Please try again or contact the administrator.');
                }
            }

            // Save the updated user information
            try {
                $my->save();
                Log::info('MyAccountController | update | User data saved', [
                    'user_id' => $id,
                    'edited_by' => $authid
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                Log::error('MyAccountController | update | Database error during save', [
                    'user_id' => $id,
                    'edited_by' => $authid,
                    'error_code' => $e->getCode(),
                    'error_message' => $e->getMessage()
                ]);

                // Check for specific database errors
                if ($e->getCode() == '23000') {
                    return redirect()->back()
                        ->withInput()
                        ->withError('This email or phone number is already in use. Please use different details.');
                }

                throw $e; // Re-throw to be caught by outer catch
            }

            Log::info('MyAccountController | update (After Editing)', [
                'edited_by' => $authid,
                'details_after_edit' => $my->fresh()->toArray(),
                'message' => 'After editing user info'
            ]);

            return redirect()->route('myaccounts.index')->withSuccess('Your account has been updated successfully!');

        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('MyAccountController | Update() Error ' . $unique_id, [
                'user_id' => $id ?? 'unknown',
                'edited_by' => Auth::user()->name ?? 'unknown',
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['image'])
            ]);

            // Redirect back with the error message
            return redirect()->back()
                ->withInput()
                ->withError('An unexpected error occurred while updating your account. Please contact the administrator with error ID: ' . $unique_id);
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
