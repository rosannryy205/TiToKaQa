<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    public $timestamps = false; // Bật timestamps

    protected $fillable = [
        'guest_name',
        'guest_phone',
        'guest_email',
        'guest_count',
        'note',
        'deposit_amount',
        'expiration_time',
        'total_price',
        'money_reduce',
        'user_id',
        'discount_id',
        'guest_address',
        'order_time',
        'order_status',

    ];
    public function details()
    {
        return $this->hasMany(Order_detail::class);
    }

    public function tables()
    {
        return $this->belongsToMany(Table::class, 'reservation_tables')
                    ->withPivot('reserved_from', 'reserved_to');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
