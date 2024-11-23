<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevices extends Model
{
    protected $table = 'user_devices';

    protected $fillable = [
        'user_id','device_token', 'created_at','updated_at'
    ];

}
