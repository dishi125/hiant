<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatRoomKeyword extends Model
{
    protected $table = "group_chat_room_keywords";

    protected $fillable = [
      'group_id',
      'keyword_id'
    ];

}
