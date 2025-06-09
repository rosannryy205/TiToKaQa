<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Combo_detail extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table= "combo_details";
    protected $fillable = ['combo_id', 'food_id', 'quantity'];

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }
}
