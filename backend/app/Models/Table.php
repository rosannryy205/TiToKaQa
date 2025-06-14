<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasFactory;
    protected $table= "tables";
    public $timestamps = false;
    use SoftDeletes;

    protected $fillable = [
        'capacity',
        'table_number',
        'deleted_at' 

    ];

    public function orders(){
        return $this->belongsToMany(Order::class, 'reservation_tables');
    }
    public function reservations()
    {
        return $this->hasMany(Reservation_table::class, 'table_id');
    }
}
