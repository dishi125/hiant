<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GroupChatRoom extends Model
{
    protected $table = 'group_chat_rooms';

    protected $fillable = [
        'title',
        'created_by',
        'image',
        'push_notification',
    ];

    protected $appends = ['group_image'];

    public function getGroupImageAttribute()
    {
        $value = isset($this->attributes['image']) ? $this->attributes['image'] : "";
        if (empty($value)) {
            return $this->attributes['group_image'] = asset('img/avatar/avatar-1.png');
        } else {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                return $this->attributes['group_image'] = Storage::disk('s3')->url($value);
            } else {
                return $this->attributes['group_image'] = $value;
            }
        }
    }

}
