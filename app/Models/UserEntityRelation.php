<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEntityRelation extends Model
{
    protected $table = 'user_entity_relation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id','entity_type_id','entity_id','created_at','updated_at'
    ];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [        
        'user_id' => 'int',
        'entity_type_id' => 'int',
        'entity_id' => 'int',        
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
