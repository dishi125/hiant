<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    protected $table = "advances";

    protected $fillable = [
        'title',
        'is_show',
        'key',
    ];
}
