<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table= "orders";
    public $timestamps = false; // Bật timestamps

    protected $fillable = [
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_count',
        'reservations_time',
        'note',
        'deposit_amount',
        'expiration_time',
        'total_price'

    ];
    public function details() {
        return $this->hasMany(Order_detail::class);
    }

}
