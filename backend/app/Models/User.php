<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'address',
        'fullname',
        'verify_code',
        'verify_expiry',
        'role_id',
        'phone',
        'provider_id',
        'provider_name',
        'provider_token',
        'provider_refresh_token',
        'status',
        'rank'

    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'provider_token',
        'provider_refresh_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function discounts()
{
    return $this->belongsToMany(Discount::class, 'discount_user', 'user_id', 'discount_id')
        ->withPivot(['point_used', 'exchanged_at', 'expiry_at'])
        ->withTimestamps();
}





}
