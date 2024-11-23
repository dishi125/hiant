<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DB;

class UserDetail extends Model
{
    use SoftDeletes;
    protected $table = 'users_detail';

    protected $dates = ['deleted_at'];

    const INSTAGRAM = 'instagram';
    const FACEBOOK = 'facebook';
    const WEIBO = 'weibo';

    const POINTS_40 = 40;

    protected $fillable = [
       'phone_code','hide_popup', 'package_plan_id','last_plan_update','language_id','plan_expire_date','recommended_code','recommended_by','country_id','manager_id','business_approved_by','name','mobile','gender','avatar','user_id','device_type_id','device_id','device_token', 'sns_type', 'sns_link', 'created_at','updated_at','points_updated_on','points','level','count_days','card_number', 'is_outside','is_character_as_profile','is_referral_read','mbti','supporter_type'
    ];


    protected $casts = [
        'name' => 'string',
        'recommended_code' => 'string',
        'recommended_by' => 'int',
        'mobile' => 'string',
        'gender' => 'int',
        'avatar' => 'string',
        'phone_code' => 'string',
        'user_id' => 'int',
        'package_plan_id' => 'int',
        'last_plan_update' => 'date',
        'hide_popup' => 'int',
        'language_id' => 'int',
        'plan_expire_date' => 'date',
        'country_id' => 'int',
        'manager_id' => 'int',
        'business_approved_by' => 'int',
        'device_type_id' => 'int',
        'device_id' => 'string',
        'device_token' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['level_name','user_points'];

    public function parentFollowersDetail(){
        return $this->belongsTo(UserDetail::class,'recommended_by','user_id')->with('parentFollowersDetail');
    }

    public function followersDetail(){
        return $this->hasMany(UserDetail::class,'recommended_by','user_id')->with('followersDetail');
    }

    public function getUserPointsAttribute(){

        $user_points = NULL;
        if(!empty($this->attributes['points'])){
            $points = $this->attributes['points'];
            $startPoints = DB::table('levels')->where('points','<',$points)->orderBy('id','desc')->limit(1)->first('points');
            $endPoints = DB::table('levels')->where('points','>',$points)->orderBy('id','asc')->limit(1)->first('points');

            $start = !empty($startPoints) ? (int)$startPoints->points : 0;
            $end = !empty($endPoints) ? (int)$endPoints->points : 0;
            $per = ($end - $start);
            $percentage = ((($points - $start)/$per) * 100);

            $user_points = [
                'start' => $start,
                'end' => $end,
                'percentage' => $percentage
            ];
        }

        return $this->attributes['user_points'] = $user_points;
    }

    public function getLevelNameAttribute()
    {
        $value = $this->attributes['level'] ?? '';
        $levelName = NULL;
        if (!empty($value)) {
            $getLevel = DB::table('levels')->select('name')->where('id',$value)->first();
            $levelName = !empty($getLevel) ? $getLevel->name : NULL;
        }
        return $this->attributes['level_name'] = $levelName;
    }


    public function getAvatarAttribute()
    {
        $value = $this->attributes['avatar'];
        if (empty($value)) {
            return $this->attributes['avatar'] = asset('img/avatar/avatar-1.png');
        } else {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                return $this->attributes['avatar'] = Storage::disk('s3')->url($value);
            } else {
                return $this->attributes['avatar'] = $value;
            }
        }
    }

    public function getPlanExpireDateAttribute($date)
    {
        $date = new Carbon($date);
        return $date->format('M d');
    }

}
