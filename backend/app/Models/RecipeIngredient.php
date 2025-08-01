<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    use HasFactory;
    protected $table = 'recipe_ingredients';

    protected $fillable = [
        'food_id',
        'ingredient_id',
        'quantity_required',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
