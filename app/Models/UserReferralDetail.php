<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReferralDetail extends Model
{
    protected $table = 'user_referral_details';

    protected $fillable = [
        'user_id', 'is_sent', 'created_at', 'updated_at'
    ];
}
