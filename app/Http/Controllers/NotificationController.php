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
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
            Log::error('NotificationController | Read() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return redirect()->back();
    }
    }
    public static function getNotificationCount()
    {
        try {
            $site_id = Auth::user()->site->id;
            $notificationCount = Notification::where('site_id', '=',$site_id)->where('read_at', '=', '0')->count();
            Log::info('NotificationController | getNotificationCount', [
                'user_details' => Auth::user(),
                'notification_count' => $notificationCount,
                'message' => 'Notification count retrieved successfully.',
            ]);
            return $notificationCount;
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
            Log::error('NotificationController | getNotification() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
        
            return 0;
            return redirect()->back();
        }
    }

    public static function getNotification()
    {
        try {
            $site_id = Auth::user()->site->id;
            $notification = Notification::where('read_at', '=', '0')->where('site_id','=', $site_id)->latest()->paginate(8);
            Log::info('NotificationController | getNotification', [
                'user_details' => Auth::user(),
                'notification' => $notification,
                'message' => 'Notifications retrieved successfully.',
            ]);
            return $notification;
        } catch (\Throwable $th) {
            $unique_id = floor(time() - 999999999);
            Log::error('NotificationController | getNotification() Error ' . $unique_id);
            Toastr::error('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the Feedback Button', 'Error');
            return collect();
            return redirect()->back();
           
        }
    }
}
