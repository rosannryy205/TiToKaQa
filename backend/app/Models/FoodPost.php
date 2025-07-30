<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'food_id',
        'title',
        'content',
        'image',
        'published_at',
    ];

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
