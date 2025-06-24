<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_user_id',
        'receiver_user_id',
        'sender_guest_id',
        'receiver_guest_id',
        'status',
        'last_message_at',
        'is_read',
        'sender_name',
        'sender_avatar',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }


    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany(); // lấy tin nhắn mới nhất
    }


}
