 <?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('chat.{userId}', function ($user, $userId) {
    // sirf wahi user access kar sakta hai jiska ID match kare
    return (int) $user->id === (int) $userId;
});
