<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Food_topping extends Pivot
{
    protected $table = 'food_toppings';

    public function toppings()
{
    return $this->belongsTo(Topping::class, 'topping_id');
}

}
