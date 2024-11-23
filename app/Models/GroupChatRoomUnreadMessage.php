<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatRoomUnreadMessage extends Model
{
    protected $table = "group_chat_room_unread_messages";

    protected $fillable = [
        'group_id',
        'message_id',
        'user_id'
    ];
}
