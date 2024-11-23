<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//class Role extends \jeremykenedy\LaravelRoles\Models\Role
class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','slug','description','level','created_at','updated_at','deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];
    
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'name' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'level' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}
