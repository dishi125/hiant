<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CMSPages extends Model
{
    protected $table = 'cms_pages';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'created_at',
        'updated_at'
    ];
}
