<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

     protected $fillable = [
        'name',
        'unit',
        'quantity_in_stock',
        
    ];

        public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

        public function supplierIngredients()
    {
        return $this->hasMany(SupplierIngredient::class);
    }
}
