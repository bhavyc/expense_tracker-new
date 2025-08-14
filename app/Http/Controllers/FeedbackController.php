<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
class FeedbackController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Feedback submitted successfully.');
    }

     
    public function index()
    {
        $feedbacks = Feedback::with('user')->latest()->get();
        return view('admin.feedbacks.index', compact('feedbacks'));
    }

     
    // public function reply(Request $request, $id)
    // {
    //     $request->validate([
    //         'admin_reply' => 'required|string'
    //     ]);

    //     $feedback = Feedback::findOrFail($id);
    //     $feedback->admin_reply = $request->admin_reply;
    //     $feedback->status = 'answered';
    //     $feedback->save();

    //     return back()->with('success', 'Reply sent successfully.');
    // }
}
