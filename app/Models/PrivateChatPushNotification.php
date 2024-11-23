<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateChatPushNotification extends Model
{
    protected $table = "private_chat_push_notifications";

    protected $fillable = [
        'user_id',
        'from_user_id',
        'push'
    ];

}
