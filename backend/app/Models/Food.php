<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $table = "foods";
    protected $fillable = [
        'name',          // Tên món ăn
        'price',         // Giá món ăn
        'sale_price',    // Giá sale
        'stock',         // Số lượng
        'category_id',   // ID danh mục
        'description',   // Mô tả
        'image',         // Hình ảnh
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function toppings(){
    //     return $this->belongsToMany(Topping::class, 'food_toppings','food_id','topping_id');
    // }

    public function toppings()
    {
        return $this->belongsToMany(Topping::class, 'food_toppings')
            ->using(Food_topping::class) // dùng model trung gian
            ->withPivot('id', 'price');
    }
    public function combos()
    {
        return $this->belongsToMany(Combo::class, 'combo_details', 'food_id', 'combo_id');
    }
}
