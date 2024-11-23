<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupBlockedUser extends Model
{
    protected $table = "group_blocked_users";

    protected $fillable = [
        'group_id',
        'user_id'
    ];

}
