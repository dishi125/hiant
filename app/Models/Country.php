<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','code','slug','order'
    ];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'code' => 'string',
        'order' => 'int',
        'slug' => 'string',
    ];

    const DEFAULT_COUNTRY = 'US';

    const COUNTRIES_INSTA_OPTIONS = ['US','JP','KR'];
}
