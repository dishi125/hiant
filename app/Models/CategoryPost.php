<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CategoryPost extends Model
{
    protected $table = "category_posts";

    protected $fillable = [
        'category_id',
        'title',
        'agency_name',
        'image',
        'subcript',
        'is_pin_to_top',
    ];

    public function getImageAttribute()
    {
        $image = $this->attributes['image'] ?? null;
        if ($image!=NULL && !filter_var($image, FILTER_VALIDATE_URL)) {
            $image = Storage::disk('s3')->url($image);
        } else {
            $image = ($image) ? $image : null;
        }

        return $this->attributes['image'] = $image;
    }

}
