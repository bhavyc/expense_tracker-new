<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'description','created_by'];

    public function expenses()
    {
        return $this->hasMany('App\Models\Expense');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User','groups_member');
    }
    public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
}
