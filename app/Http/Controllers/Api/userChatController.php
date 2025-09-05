<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class userChatController extends Controller
{
      public function fetchMessages($adminId)
    {
        $userId = Auth::id(); // logged-in user ID

        $messages = Message::where(function($q) use ($userId, $adminId) {
                $q->where('sender_id', $userId)->where('receiver_id', $adminId);
            })
            ->orWhere(function($q) use ($userId, $adminId) {
                $q->where('sender_id', $adminId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'messages' => $messages
        ]);
    }

    // Send message from user to admin
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:admins,id', // receiver must be admin
            'message' => 'required|string|max:1000',
        ]);

        $msg = Message::create([
            'sender_id'   => Auth::id(),   // logged-in user
            'receiver_id' => $request->receiver_id,
            'message'     => $request->message,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => $msg
        ]);
    }
}
