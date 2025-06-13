<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $table= "combos";
    protected $fillable = ['name', 'price', 'image', 'description'];
    use HasFactory;

    public function foods()
{
    return $this->belongsToMany(Food::class, 'combo_details', 'combo_id', 'food_id')
    ->withPivot('quantity');
}
public function details()
    {
        return $this->hasMany(Combo_detail::class);
    }
}
