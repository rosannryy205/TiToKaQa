<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    use HasFactory;


    public function food(){
        return $this->belongsToMany(Food::class, 'food_toppings','topping_id','food_id');
    }

}
