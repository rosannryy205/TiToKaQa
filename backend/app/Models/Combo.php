<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo extends Model
{
    protected $table= "combos";
    use HasFactory;
    
    public function Details(){
        return $this->hasMany(Combo_detail::class);
    }
}
