<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = "orders";
    public $timestamps = false;

    protected $fillable = [
        'guest_name',
        'guest_phone',
        'order_code',
        'guest_email',
        'guest_count',
        'note',
        'expiration_time',
        'total_price',
        'money_reduce',
        'type_order',
        'tpoint_used',
        'ship_cost',
        'table_fee',
        'user_id',
        'discount_id',
        'discount_user_id',
        'guest_address',
        'order_time',
        'order_status',
        'shippingFee',
        'reservation_code',
        'canceled_at',
        'canceled_reason',

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
    public function payment()
    {
        return $this->hasMany(Payment::class, 'order_id', 'id');
    }
}
