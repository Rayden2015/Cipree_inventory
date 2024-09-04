<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;

class SMSController extends Controller
{
    public function sendSms($to, $content)
    {
        try {
            $clientId = env('SMS_CLIENT_ID');
            $clientSecret = env('SMS_CLIENT_SECRET');
            $from = env('SMS_FROM_NUMBER');

            $query = [
                'clientid' => "hjigszay",
                'clientsecret' => "tpfwfgbm",
                'from' => "CIPREE",
                'to' => $to,
                'content' => $content
            ];

            //$url = "https://devp-sms03726-api.hubtel.com/v1/messages/send?" . http_build_query($query);
            $url = "https://smsc.hubtel.com/v1/messages/send?" . http_build_query($query);

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "GET",
            ]);

            $response = curl_exec($curl);
            $error = curl_error($curl);

            curl_close($curl);

            if ($error) {
                Log::channel('error_log')->error('SmsController | sendSms', [
                    'to' => $to,
                    'content' => $content,
                    'error' => $error,
                ]);

                echo "cURL Error #:" . $error;
            } else {
                Log::info('SmsController | sendSms', [
                    'to' => $to,
                    'content' => $content,
                    'response' => $response,
                ]);

                echo $response;
            }
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
                Log::channel('error_log')->error('SmsController | SendSms() Error ' . $unique_id
                ,[
                    'message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString()
                ]);
    
        // Redirect back with the error message
        return redirect()->back()
                         ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
    }
    
    }
}
