<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;

    protected $table= "toppings";

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'food_toppings');
    }

}
