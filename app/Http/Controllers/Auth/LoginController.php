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
    // Note: Status is already checked before this method is called in login()
    // So we don't need to include it in credentials array
    $credentials = [];
    
    if (is_numeric($request->get('email'))) {
      // Phone login
      $credentials = [
        'phone' => $request->get('email'), 
        'password' => $request->get('password')
      ];
    } elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
      // Email login
      $credentials = [
        'email' => $request->get('email'), 
        'password' => $request->get('password')
      ];
    } else {
      // Fallback
      $credentials = $request->only($this->username(), 'password');
    }
    
    Log::info('LoginController | credentials() | Credentials prepared', [
      'login_method' => is_numeric($request->get('email')) ? 'phone' : 'email',
      'identifier' => $request->get('email')
    ]);
    
    return $credentials;
  }

  /**
   * Override the login method to add status checking
   */
  public function login(Request $request)
  {
    $this->validateLogin($request);

    // Check for too many login attempts
    if ($this->hasTooManyLoginAttempts($request)) {
      $this->fireLockoutEvent($request);
      return $this->sendLockoutResponse($request);
    }

    // Pre-check if user exists and is active BEFORE attempting login
    $identifier = $request->get('email');
    $user = null;
    
    if (is_numeric($identifier)) {
      $user = User::where('phone', $identifier)->first();
    } else {
      $user = User::where('email', $identifier)->first();
    }

    // Check if user exists
    if (!$user) {
      $this->incrementLoginAttempts($request);
      Log::warning('LoginController | login() | User not found', [
        'identifier' => $identifier
      ]);
      return $this->sendFailedLoginResponse($request);
    }

    // Check if user status is inactive
    if ($user->status !== 'Active') {
      Log::warning('LoginController | login() | Inactive user attempted login', [
        'user_id' => $user->id,
        'user_email' => $user->email,
        'user_status' => $user->status
      ]);
      return $this->sendDisabledResponse($request);
    }

    // Attempt login with credentials that include status check
    if ($this->attemptLogin($request)) {
      return $this->sendLoginResponse($request);
    }

    // Login failed
    $this->incrementLoginAttempts($request);
    
    Log::warning('LoginController | login() | Login failed', [
      'identifier' => $identifier
    ]);
    
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
    // Double-check status after authentication (defense in depth)
    if ($user->status !== 'Active') {
      Log::critical('LoginController | authenticated() | Inactive user bypassed login check', [
        'user_id' => $user->id,
        'user_email' => $user->email,
        'status' => $user->status
      ]);
      
      // Force logout
      $this->guard()->logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();
      
      return redirect()->route('login')
        ->withErrors(['email' => 'Your account has been deactivated. Please contact the administrator.']);
    }

    Event::dispatch(new \Illuminate\Auth\Events\Login($user, false, 0));
    $user->update(['last_login_at' => now()]);

    Log::info('LoginController | authenticated() | User logged in successfully', [
      'user_id' => $user->id,
      'user_email' => $user->email,
      'login_time' => now()
    ]);
    
    $this->logUserActivity($user->id, 'Login', $request->url(), $request->userAgent());
    
    // Set flag to show banner on first page load after login (only if banner is enabled)
    if (config('banner.enabled', true)) {
        $request->session()->put('show_banner_on_login', true);
        $request->session()->forget('banner_dismissed');
    }
    
    return redirect()->intended($this->redirectPath());
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
      $this->username() => ['These credentials do not match our records.'],
    ]);
  }

  protected function sendDisabledResponse(Request $request)
  {
    Log::warning('LoginController | sendDisabledResponse() | Account disabled message sent', [
      'identifier' => $request->get('email')
    ]);
    
    throw ValidationException::withMessages([
      $this->username() => ['Your account has been deactivated. Please contact the administrator.'],
    ]);
  }
}

