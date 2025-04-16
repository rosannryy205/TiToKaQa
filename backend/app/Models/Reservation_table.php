<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation_table extends Model
{
    use HasFactory;
    protected $table= "reservation_tables";
    public $timestamps = false; // Bật timestamps

    protected $fillable = [
        'order_id',
        'table_id',
        'assigned_time',
        'reserved_from',
        'reserved_to'

    ];

}
