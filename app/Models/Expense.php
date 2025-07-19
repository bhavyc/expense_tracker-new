<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    // ✅ Mass assignable fields (jo hum form se fill karte hain)
    protected $fillable = [
        'user_id',
        'group_id',
        'description',
        'amount',
        'expense_date',
        'category',
        'status',
        'notes',
    ];

    // ✅ Relationship: Expense belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ✅ Relationship: Expense may belong to a Group (or NULL if personal)
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // // ✅ Scope: Filter by status (optional, useful for admin)
    // public function scopePending($query)
    // {
    //     return $query->where('status', 'pending');
    // }

    // public function scopeApproved($query)
    // {
    //     return $query->where('status', 'approved');
    // }
    public function splits()
{
    return $this->hasMany(Split::class);
}
public function category()
{
    return $this->belongsTo(Category::class);
}


}
