<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReferral extends Model
{
    protected $table = 'user_referrals';

    protected $fillable = [
        'referred_by', 'referral_user', 'has_coffee_access', 'created_at', 'updated_at'
    ];
}
