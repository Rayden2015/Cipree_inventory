<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
   
    public function index()
    {
        try {
            $feedbacks = Feedback::all();
            $images = Storage::disk('local')->allFiles('screenshots/');

            Log::info('ReviewController | index', [
                'user_details' => auth()->user(),
                'message' => 'Feedbacks and images retrieved successfully.',
            ]);

            return view('feedback.index', compact('feedbacks', 'images'));
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('ReviewController | Index() Error ' . $unique_id  ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function indexx()
    {
        try {
            $types = config('kustomer.feedbacks');

            $feedbacks = Feedback::latest()->get()->map(function ($feedback) use ($types) {
                $feedback->icon = isset($types[$feedback->type]) ? $types[$feedback->type]['icon'] : '';
                return $feedback;
            });

            Log::info('ReviewController | indexx', [
                'user_details' => auth()->user(),
                'message' => 'Feedbacks retrieved successfully.',
            ]);

            return $feedbacks;
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('ReviewController | Indexx() Error ' . $unique_id  ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function show(Feedback $feedback, $id)
    {
        try {
            $feedback1 = Feedback::find($id);
            // $collection = Feedback::where('id','=',$id)->pluck(['user_info']);
            $fd = ['user_id' => $feedback1->user_info['user_id']];
            $user = User::where('id', '=', $fd)->value('name');
            //  $id =   return [
            //         'user_id' => $feedback1->user_info['user_id'],

            //     ];
            $feedback = array($feedback1);

            Log::info('ReviewController | show', [
                'user_details' => auth()->user(),
                'message' => 'Feedback details retrieved successfully.',
                'feedback_id' => $id,
            ]);

            return view('feedback.show', compact('feedback1', 'user'), ['feedback' => $feedback]);
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('ReviewController | Show() Error ' . $unique_id  ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }

    public function markAsReviewed(Feedback $feedback, $id)
    {
        try {
            $feedback = Feedback::find($id);
            $feedback->reviewed = 1;
            $feedback->save();

            Log::info('ReviewController | markAsReviewed', [
                'user_details' => auth()->user(),
                'message' => 'Feedback marked as reviewed successfully.',
                'feedback_id' => $id,
            ]);

           
            return redirect()->back()->withSuccess('Successfully Updated');
        } catch (\Throwable $e) {
            $unique_id = floor(time() - 999999999);
            Log::error('ReviewController | MarkAsReviewed() Error ' . $unique_id  ,[
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);

    // Redirect back with the error message
    return redirect()->back()
                     ->withError('An error occurred. Contact Administrator with error ID: ' . $unique_id . ' via the error code and Feedback Button');
}

    }
    public function destroy($id)
    {
        $feedback = Feedback::find($id);
        $feedback->delete();
       
        return redirect()->back()->withSuccess('Successfully deleted');
    }
    // ... other methods ...
}
