<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuckyWheelSpin extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'lucky_wheel_prize_id',
        'prize_name',
        'prize_type',
        'prize_data',
        'spun_at',
        'is_claimed',
        'claimed_at',
    ];

    protected $casts = [
        'prize_data' => 'array',
        'spun_at' => 'datetime',
        'claimed_at' => 'datetime',
        'is_claimed' => 'boolean',
    ];
}
