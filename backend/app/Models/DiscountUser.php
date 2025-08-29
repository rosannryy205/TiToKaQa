<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountUser extends Model
{
    protected $table= "discount_user";
    use HasFactory;
    protected $fillable = [
        'user_id',
        'discount_id',
        'point_used',
        'exchanged_at',
        'expiry_at',
        'source',
        'spin_id',
        'used_at',
        'spin_id'
    ];
    public function discount() { 
    return $this->belongsTo(Discount::class);
    }

}
