<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table = "payments";
    public $timestamps = false; // Bật timestamps

    protected $fillable = [
        'order_id',
        'vnpay_txn_ref',
        'transaction_id',
        'bank_code',
        'card_type',
        'amount_paid',
        'payment_method',
        'payment_status',
        'payment_time',
        'payment_type',
    ];
}
