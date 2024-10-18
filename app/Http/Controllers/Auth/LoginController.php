<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use ThrottlesLogins;
use App\Models\Login;
use App\Mail\WelcomeMail;
use App\Mail\LoggedinMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use App\Providers\RouteServiceProvider;
use Stevebauman\Location\Facades\Location;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = RouteServiceProvider::HOME;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  protected $maxAttempts = 3;
  protected $decayMinutes = 15;
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }

  protected function credentials(Request $request)
  {




    if (is_numeric($request->get('email'))) {
      return ['phone' => $request->get('email'), 'password' => $request->get('password'), 'status' => 'Active'];
    } elseif (filter_var($request->get('email'))) {
      return ['email' => $request->get('email'), 'password' => $request->get('password')];
    }
    return $request->only($this->username(), 'password');
    $email = $request->get('email');
    $data = (['email' => $request->get('email'),
    ]);
    Mail::to($email)->send(new WelcomeMail($data));
    Mail::to($email)->send(new LoggedinMail($request));

    $this->validateLogin($request);

    if ($this->hasTooManyLoginAttempts($request)) {
      $this->fireLockoutEvent($request);

      return $this->sendLockoutResponse($request);
    }
    $credentials = $this->credentials($request);

    $user = User::where('email', $credentials['email'])->first();

    if ($user && $user->status == 'Inactive') {
      // Account is disabled, handle accordingly (e.g., show a custom error message)
      return $this->sendDisabledResponse($request);
    }

    if ($this->attemptLogin($request)) {
      return $this->sendLoginResponse($request);
    }

    $this->incrementLoginAttempts($request);

    return $this->sendFailedLoginResponse($request);
  }


  // protected function sendLockoutResponse(Request $request)
  // {
  //     $minutes = ceil($this->limiter()->availableIn(
  //         $this->throttleKey($request)
  //     ) / 60);

  //     throw ValidationException::withMessages([
  //         $this->username() => [trans('auth.throttle', ['seconds' => $minutes])],
  //     ]);
  // }
  protected function sendLockoutResponse(Request $request)
  {
    $seconds = $this->limiter()->availableIn(
      $this->throttleKey($request)
    );

    // Custom logic to update status to inactive on lockout
    $this->updateUserStatus($request->input('email'));

    throw ValidationException::withMessages([
      $this->username() => [trans('auth.throttle', ['seconds' => $seconds])],
    ])->status(403);
  }

  // Custom method to update user status
  protected function updateUserStatus($email)
  {
    // Assuming you have an 'status' field in your users table
    User::where('email', $email)->update(['status' => 'Inactive']);
  }

  protected function authenticated(Request $request, $user)
  {
    Event::dispatch(new \Illuminate\Auth\Events\Login($user, false, 0));
    $user->update(['last_login_at' => now()]);

    return redirect()->intended($this->redirectPath());


    $this->logUserActivity($user->id, 'Login', $request->url(), $request->userAgent());
    return redirect()->intended($this->redirectPath());
    // Retrieve the last successful login


  }

  private function logUserActivity($userId, $activity, $url, $userAgent)
  {
    try {
      Log::info("LoginController | logUserActivity() | User ID: {$userId} | Activity: {$activity} | URL: {$url} | User Agent: {$userAgent}");
    } catch (\Exception $e) {
      $unique_id = floor(time() - 999999999);
   
      Log::channel('error_log')->error('LoginController | LogUserActivity() Error ' . $unique_id, [
        'message' => $e->getMessage(),
        'stack_trace' => $e->getTraceAsString()
    ]);
  }
  }
  
  protected function sendFailedLoginResponse(Request $request)
  {
    Event::dispatch(new \Illuminate\Auth\Events\Failed($request->only($this->username()), false, 0));
    throw ValidationException::withMessages([
      $this->username() => [trans('auth.throttle')],
    ]);
  }

  protected function sendDisabledResponse(Request $request)
  {
    throw ValidationException::withMessages([
      $this->username() => [trans('auth.disabled')],
    ]);
  }
}
