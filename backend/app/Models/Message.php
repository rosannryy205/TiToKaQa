<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table= "messages";
    protected $fillable = ['sender_user_id', 'sender_name', 'receiver_user_id', 'message', 'conversation_id', 'sender_guest_id',
        'receiver_guest_id',];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }


}
