<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChatRoomMessageFile extends Model
{
    protected $table = "group_chat_room_message_files";

    protected $fillable = [
        'message_id',
        'group_id',
        'file',
        'video_thumbnail'
    ];
}
