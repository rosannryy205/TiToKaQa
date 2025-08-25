<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoodPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
<<<<<<< HEAD
=======
        'user_id',
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
        'category',
        'title',
        'content',
        'image',
        'published_at',
    ];

    public function foods()
    {
        return $this->belongsTo(Food::class);
    }
<<<<<<< HEAD
=======

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
>>>>>>> ffe2d1ccb4485c049b824f539d121519edaaf06f
}
