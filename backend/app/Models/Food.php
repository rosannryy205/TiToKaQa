<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Food extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "foods";
    protected $fillable = [
        'name',
        'price',
        'sale_price',
        'stock',
        'description',
        'image',
        'status',
        'category_id',
        'flash_sale_price',
        'flash_sale_quantity',
        'flash_sale_sold',
        'flash_sale_start',
        'flash_sale_end',
    ];
    protected $casts = [
        'flash_sale_end' => 'datetime',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // public function toppings(){
    //     return $this->belongsToMany(Topping::class, 'food_toppings','food_id','topping_id');
    // }


    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'food_toppings')
            ->using(Food_topping::class)
            ->withPivot('id', 'price');
    }

    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'combo_details', 'food_id', 'combo_id')
            ->withPivot('quantity');
    }
}
