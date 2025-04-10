<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo_detail extends Model
{
    use HasFactory;

    public function foods(){
        return $this->belongsToMany(Food::class, 'food_id');
    }
}
