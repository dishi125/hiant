<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeleteAccountReason extends Model
{
    protected $table = "delete_account_reasons";

    protected $fillable = [
        'user_id',
        'reason',
        'is_deleted_user',
        'is_admin_read',
    ];
}
