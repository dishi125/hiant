<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikedGroupChatRoomMessage extends Model
{
    protected $table = "liked_group_chat_room_messages";

    protected $fillable = [
      'message_id',
      'user_id',
      'group_id',
    ];
}
