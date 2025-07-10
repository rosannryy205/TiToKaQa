<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topping extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "toppings";
    public $timestamps = false;
    protected $fillable = [
        'name',
        'price',
        'category_id'
    ];

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'food_toppings');
    }


    public function category_topping()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
