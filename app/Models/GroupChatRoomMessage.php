<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatRoomMessage extends Model
{
    protected $table = "group_chat_room_messages";

    protected $fillable = [
        'group_id',
        'from_user_id',
        'type',
        'message',
        'created_at',
        'updated_at',
        'reply_of',
        'kicked_user_id',
    ];

}
