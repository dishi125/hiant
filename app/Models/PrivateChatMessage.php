<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateChatMessage extends Model
{
    protected $table = "private_chat_messages";

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'type',
        'message',
        'status',
        'created_at',
        'updated_at',
        'reply_of',
        'is_liked',
    ];

    protected $casts = [
        'from_user_id ' => 'int',
        'to_user_id' => 'int',
        'type' => 'string',
        'message' => 'string',
        'status' => 'int',
        'created_at' => 'string',
        'updated_at' => 'string'
    ];

}
