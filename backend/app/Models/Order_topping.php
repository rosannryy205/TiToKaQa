<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_topping extends Model
{
    use HasFactory;
    public $timestamps = false; // Bật timestamps

    protected $fillable = ['order_detail_id', 'food_toppings_id', 'price'];

    public function orderDetail()
    {
        return $this->belongsTo(Order_detail::class, 'order_detail_id');
    }

}
