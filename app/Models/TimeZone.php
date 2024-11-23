<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    protected $table = 'time_zones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'abbr',
        'offset',
        'isdst',
        'text',
        'utc',
        'created_at',
        'updated_at'
    ];



    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'abbr' => 'string',
        'offset' => 'string',
        'isdst' => 'string',
        'text' => 'string',
        'utc' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * @param $utc
     * @return mixed
     */
    public function setUtcAttribute($utc)
    {
        $this->attributes['utc'] = json_encode($utc);
    }
}
