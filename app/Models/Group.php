<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description','budget','permanent','created_by','category','carry_forward_model'];

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense');
    }

    public function users()
    {
         return $this->belongsToMany('App\Models\User', 'groups_member', 'group_id', 'user_id');
    }
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
public function members()
{
    return $this->hasMany(GroupMember::class);
}
}
