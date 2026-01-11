<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    public function read($id)
    {
        try {
            $notification = Notification::where('id', '=', $id)->update(['read_at' => '1']);
            Log::info('NotificationController | read', [
                'user_details' => Auth::user(),
                'notification_id' => $id,
                'message' => 'Notification marked as read successfully.',
            ]);
            return back();
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('NotificationController | Read() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public static function getNotificationCount()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->site) {
                return 0; // Return 0 if user has no site
            }
            
            $site_id = $user->site->id;
            $notificationCount = Notification::where('site_id', '=',$site_id)->where('read_at', '=', '0')->count();
            // Log::info('NotificationController | getNotificationCount', [
            //     'user_details' => Auth::user(),
            //     'notification_count' => $notificationCount,
            //     'message' => 'Notification count retrieved successfully.',
            // ]);
            return $notificationCount;
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('NotificationController | getNotificationCount() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Return 0 instead of redirect in static method (can't redirect from view)
            return 0;
        }
    }

    public static function getNotification()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->site) {
                // Return empty collection if user has no site - use empty query result
                return Notification::where('id', '<', 0)->paginate(8); // Returns empty paginator
            }
            
            $site_id = $user->site->id;
            $notification = Notification::where('read_at', '=', '0')->where('site_id','=', $site_id)->latest()->paginate(8);
            // Log::info('NotificationController | getNotification', [
            //     'user_details' => Auth::user(),
            //     'notification' => $notification,
            //     'message' => 'Notifications retrieved successfully.',
            // ]);
            return $notification;
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::channel('error_log')->error('NotificationController | getNotification() Error ' . $unique_id,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            // Return empty paginator instead of redirect in static method (can't redirect from view)
            return Notification::where('id', '<', 0)->paginate(8); // Returns empty paginator
        }
    }
}
