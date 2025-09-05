<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasApiTokens, HasFactory, Notifiable;


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
        'phone_number',
        'personal_budget',
        'personal_carry_forward_balance'
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


      public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        // yahan phone_number add karenge
        return [
            'phone_number' => $this->phone
        ];
    }
}
