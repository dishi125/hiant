<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportedUser extends Model
{
    protected $table = "reported_users";

    protected $fillable = [
        'reporter_user_id',
        'reported_user_id',
        'reason',
    ];
}
