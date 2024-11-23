<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsPostViewHistory extends Model
{
    protected $table = "news_post_view_histories";

    protected $fillable = [
        'link',
        'user_id',
        'type',
        'website_name',
        'headline',
        'description',
        'time',
        'image'
    ];
}
