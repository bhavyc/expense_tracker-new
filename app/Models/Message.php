<?php

// namespace App\Models;

// use Illuminate\Database\Eloquent\Model;

// class Message extends Model
// {
//      protected $fillable = ['sender_id','receiver_id','message'];

       

//     // Receiver user
//     public function receiver() {
//         return $this->belongsTo(User::class, 'receiver_id');
//     }

//     public function sender()
// {
//     if ($this->sender_type == 'admin') {
//         return $this->belongsTo(Admin::class, 'sender_id');
//     }
//     return $this->belongsTo(User::class, 'sender_id');
// }
// }
 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'message'];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
