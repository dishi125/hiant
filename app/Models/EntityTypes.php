<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityTypes extends Model
{
    protected $table = 'entity_types';
        
    const SHOP = 1;
    const HOSPITAL = 2;
    const NORMALUSER = 3;
    const ADMIN = 4;
    const MANAGER = 5;
    const SUBMANAGER = 6;
    const COMMUNITY = 7;
    const REVIEWS = 8;
    const ASSOCIATION_COMMUNITY = 9;
    const SHOP_POST = 10;
    const REQUESTED_CARD = 11;

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
