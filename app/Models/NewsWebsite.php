<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsWebsite extends Model
{
    protected $table = "news_websites";

    protected $fillable = [
      'website_name',
      'type',
      'order',
    ];

}
