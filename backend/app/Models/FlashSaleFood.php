<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlashSaleFood extends Model
{   
    protected $table = "flash_sale_foods";
    use HasFactory;
    protected $fillable = [
        'food_id',
        'original_price',
        'sale_price',
        'start_time',
        'end_time',
        'quantity_limit',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
