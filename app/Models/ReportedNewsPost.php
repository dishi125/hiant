<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportedNewsPost extends Model
{
    protected $table = "reported_news_posts";

    protected $fillable = [
      'type',
      'website_name',
      'link',
      'user_id',
      'reason',
      'is_blocked'
    ];

}
