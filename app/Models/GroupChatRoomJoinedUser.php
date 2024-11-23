<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatRoomJoinedUser extends Model
{
    protected $table = "group_chat_room_joined_users";

    protected $fillable = [
        'group_chat_room_id',
        'user_id',
        'push_notification',
    ];

}
