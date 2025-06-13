<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
    use HasFactory;
    public $timestamps = false; // Bật timestamps

    protected $fillable = [
        'order_id', 'food_id', 'combo_id', 'quantity', 'price', 'type'
    ];
    public function toppings()
    {
        return $this->hasMany(Order_topping::class, 'order_detail_id');
    }
    public function foods()
    {
        return $this->belongsTo(Food::class, 'food_id');
    }

}
