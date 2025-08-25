<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuckyWheelPrize extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type', 'data', 'probability', 'status'];

    protected $casts = [
        'data' => 'array',
    ];
}
