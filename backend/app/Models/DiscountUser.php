<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountUser extends Model
{
    protected $table= "discount_user";
    use HasFactory;

    public function discount() { 
    return $this->belongsTo(Discount::class);
    }

}
