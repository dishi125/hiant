<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'status';
        
    const ACTIVE = 1;
    const INACTIVE = 2;
    const PENDING = 3;
    const EXPIRE = 4;
    const HIDDEN = 5;
    const UNHIDE = 6;
    const FUTURE = 7;

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
