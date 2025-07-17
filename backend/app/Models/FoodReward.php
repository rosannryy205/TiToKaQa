<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodReward extends Model
{
    use HasFactory;
    protected $fillable = [
        'food_snapshot',
        'user_id',
        'code',
        'name',
        'food_id',
        'expired_at',
        'is_used',
        'used_at',
    ];
    protected $casts = [
        'food_snapshot' => 'array',
        'expired_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
    public function orderDetails()
{
    return $this->hasMany(Order_detail::class, 'reward_id');
}
}
