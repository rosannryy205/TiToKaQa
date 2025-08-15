<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table= "discounts";
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'discount_value', 'discount_method', 'discount_type',
        'max_discount_amount', 'min_order_value', 'category_id', 'start_date', 'end_date',
        'status', 'usage_limit', 'source', 'user_level', 'cost', 'condition', 'custom_condition_note'
    ];
    
    public function users()
{
    return $this->belongsToMany(User::class, 'discount_user')
        ->withPivot(['used', 'created_at'])
        ->withTimestamps();
}

}
