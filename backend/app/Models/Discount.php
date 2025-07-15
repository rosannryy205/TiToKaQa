<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{   
    protected $table= "discounts";
    use HasFactory;


    public function users()
{
    return $this->belongsToMany(User::class, 'discount_user')
        ->withPivot(['used', 'created_at'])
        ->withTimestamps();
}

}
