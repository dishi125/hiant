<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceTypes extends Model
{
    protected $table = 'device_types';
        
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'id' => 'int',
        'name' => 'string',        
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
