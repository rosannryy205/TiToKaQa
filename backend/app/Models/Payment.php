<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table= "payments";
    public $timestamps = false;

    protected $fillable = [
        'amount_paid',
        'payment_method',
        'payment_status',
        'payment_time',
        'payment_type',
        'order_id',
    ];
}
