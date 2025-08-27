<?php
// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Message;
// use Illuminate\Support\Facades\Auth;

// class ChatController extends Controller
// {
//     // Fetch Messages between Admin and User
//    public function fetchMessages($userId)
// {
//     $myId = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : Auth::id();

//     $messages = Message::where(function ($q) use ($userId, $myId) {
//             $q->where('sender_id', $myId)
//               ->where('receiver_id', $userId);
//         })
//         ->orWhere(function ($q) use ($userId, $myId) {
//             $q->where('sender_id', $userId)
//               ->where('receiver_id', $myId);
//         })
//         ->orderBy('created_at', 'asc')
//         ->get();

//     return response()->json($messages);
// }

//     // Send Message
//     public function sendMessage(Request $request)
// {
//     // Determine sender ID based on guard
//     $senderId = Auth::guard('admin')->check() ? Auth::guard('admin')->id() : Auth::id();

//     $msg = Message::create([
//         'sender_id'   => $senderId,
//         'receiver_id' => $request->receiver_id,
//         'message'     => $request->message,
//     ]);

//     return response()->json($msg);
// }

// }  


 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // Fetch Messages between Admin and User
    public function fetchUserMessages($adminId)
{
    $userId = Auth::id(); // logged-in user
    $messages = Message::where(function($q) use ($userId, $adminId) {
        $q->where('sender_id', $userId)->where('receiver_id', $adminId);
    })->orWhere(function($q) use ($userId, $adminId) {
        $q->where('sender_id', $adminId)->where('receiver_id', $userId);
    })->orderBy('created_at', 'asc')->get();

    return response()->json($messages);
}

public function sendUserMessage(Request $request)
{
    $msg = Message::create([
        'sender_id'   => Auth::id(),        // always user ID
        'receiver_id' => $request->receiver_id,
        'message'     => $request->message,
    ]);

    return response()->json($msg);
}



public function fetchAdminMessages($userId)
{
    $adminId = Auth::guard('admin')->id(); // logged-in admin
    $messages = Message::where(function($q) use ($userId, $adminId) {
        $q->where('sender_id', $adminId)->where('receiver_id', $userId);
    })->orWhere(function($q) use ($userId, $adminId) {
        $q->where('sender_id', $userId)->where('receiver_id', $adminId);
    })->orderBy('created_at', 'asc')->get();

    return response()->json($messages);
}

public function sendAdminMessage(Request $request)
{
    $msg = Message::create([
        'sender_id'   => Auth::guard('admin')->id(), // always admin ID
        'receiver_id' => $request->receiver_id,
        'message'     => $request->message,
    ]);

    return response()->json($msg);
}

}
