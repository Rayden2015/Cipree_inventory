<?php
namespace App\Http\Controllers;


use App\Models\User;
use App\Jobs\SendBulkEmail;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function showForm()
    {
        $users = User::all(); // Fetch all users or apply filters if necessary
        return view('emails.bulk_email_form', compact('users'));
    }

    public function sendBulkEmail(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $users = User::whereIn('id', $request->users)->get();
        $subject = $request->subject;
        $content = $request->content;

        SendBulkEmail::dispatch($users, $subject, $content);

        return redirect()->back()->with('success', 'Bulk email has been dispatched!');
    }
}
