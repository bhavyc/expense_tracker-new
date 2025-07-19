<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
     
    protected $table = 'groups_member'; // Specify the table name if it's not the plural of the model name

    protected $fillable = [
        'group_id',
        'user_id',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function  user()
    {
        return $this->belongsTo(User::class);
    }
}
