<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierIngredient extends Model
{
    use HasFactory;

    protected $table = 'supplier_ingredient';
    protected $fillable = [
        'supplier_id',
        'ingredient_id',
        'price',
        'supply_date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
