<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'lent_total',
        'owed_total',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function expenses()
{
    return $this->hasMany(Expense::class);
}
// app/Models/User.php

public function groups()
{
    return $this->belongsToMany(Group::class, 'groups_member', 'user_id', 'group_id');
}
 public function updateTotals()
    {
        // Total user lent (diya)
        $this->lent_total = $this->splits()
            ->where('type', 'lent')
            ->sum('amount');

        // Total user owed (liya)
        $this->owed_total = $this->splits()
            ->where('type', 'owned')
            ->sum('amount');

        $this->save();
    }

}
