<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Notifications\PasswordReset;
use Session;
use DB;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'email', 'password', 'username', 'email_verified_at', 'status_id','last_verify', 'remember_token', 'created_at', 'updated_at', 'deleted_at', 'last_login', 'chat_status', 'lang_id','inquiry_phone','connect_instagram','is_admin_access','is_support_user','is_show_gender','is_show_mbti'
    ];

    protected $appends = ['display_created_at', 'all_entity_type_id', 'entity_type_id', 'name','gender','phone_code','mobile','recommended_code','verify_status','sns_type', 'sns_link','mbti','avatar'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'int',
        'email' => 'string',
        'password' => 'string',
        'remember_token' => 'string',
        'status_id' => 'int',
        'last_verify' => 'date',
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getEntityTypeIdAttribute()
    {
        $name = '';
        $id = $this->attributes['id'] ?? "";
        $user = UserEntityRelation::where('user_id',$id)->where('entity_id',$id)->first();

       return $this->attributes['entity_type_id'] = !empty($user) ? $user->entity_type_id : NULL;
    }

    public function getAllEntityTypeIdAttribute()
    {
        $name = '';
        $id = $this->attributes['id'] ?? "";
        $all_entity_type_id = UserEntityRelation::where('user_id',$id)->pluck('entity_type_id')->toArray();

       return $this->attributes['all_entity_type_id'] = !empty($all_entity_type_id) ? $all_entity_type_id : [];
    }

    public function getRecommendedCodeAttribute()
    {
        $recommended_code = '';
        $id = $this->attributes['id'] ?? "";
//        $user = User::find($id);
//        $entity_type_id = $user->entity_type_id;
        /*if($entity_type_id == EntityTypes::ADMIN || $entity_type_id == EntityTypes::MANAGER || $entity_type_id == EntityTypes::SUBMANAGER){
            $manager = Manager::where('user_id',$id)->first();
            $recommended_code = $manager ? $manager->recommended_code : '';
        }else {*/
            $userDetail = UserDetail::where('user_id',$id)->first();
            $recommended_code = $userDetail ? $userDetail->recommended_code : '';
//        }
        return $this->attributes['recommended_code'] = $recommended_code;
    }

    public function getNameAttribute()
    {
        $name = '';
        $id = $this->attributes['id'] ?? "";
//        $user = User::find($id);
//        $entity_type_id = $user->entity_type_id ?? '';
       /* if($entity_type_id == EntityTypes::ADMIN || $entity_type_id == EntityTypes::MANAGER || $entity_type_id == EntityTypes::SUBMANAGER){
            $manager = Manager::where('user_id',$id)->first();
            $name = $manager ? $manager->name : '';
        }else {*/
            $userDetail = UserDetail::where('user_id',$id)->first();
            $name = $userDetail ? $userDetail->name : '';
//        }
        return $this->attributes['name'] = $name;
    }
    public function getGenderAttribute()
    {
        $gender = '';
        $id = $this->attributes['id'] ?? "";
//        $user = User::find($id);
//        $entity_type_id = $user->entity_type_id ?? '';
       /* if($entity_type_id == EntityTypes::ADMIN || $entity_type_id == EntityTypes::MANAGER || $entity_type_id == EntityTypes::SUBMANAGER){
            $gender = '';
        }else {*/
            $userDetail = UserDetail::where('user_id',$id)->first();
            $gender = $userDetail ? $userDetail->gender : '';
//        }
        return $this->attributes['gender'] = $gender;
    }
    public function getMbtiAttribute()
    {
        $mbti = '';
        $id = $this->attributes['id'] ?? "";
        $userDetail = UserDetail::where('user_id',$id)->first();
        $mbti = $userDetail ? $userDetail->mbti : '';
        return $this->attributes['mbti'] = $mbti;
    }
    public function getPhoneCodeAttribute()
    {
        $phone_code = '';
        $id = $this->attributes['id'] ?? "";
//        $user = User::find($id);
//        $entity_type_id = $user->entity_type_id ?? '';
        /*if($entity_type_id == EntityTypes::ADMIN || $entity_type_id == EntityTypes::MANAGER || $entity_type_id == EntityTypes::SUBMANAGER){
            $phone_code = '';
        }else {*/
            $userDetail = UserDetail::where('user_id',$id)->first();
            $phone_code = $userDetail ? $userDetail->phone_code : '';
//        }
        return $this->attributes['phone_code'] = $phone_code;
    }
    public function getMobileAttribute()
    {
        $mobile = '';
        $id = $this->attributes['id'] ?? "";
//        $user = User::find($id);
//        $entity_type_id = $user->entity_type_id ?? '';
        /*if($entity_type_id == EntityTypes::ADMIN || $entity_type_id == EntityTypes::MANAGER || $entity_type_id == EntityTypes::SUBMANAGER){
            $manager = Manager::where('user_id',$id)->first();
            $mobile = $manager ? $manager->mobile : '';
        }else {*/
            $userDetail = UserDetail::where('user_id',$id)->first();
            $mobile = $userDetail ? $userDetail->mobile : '';
//        }
        return $this->attributes['mobile'] = $mobile;
    }
    public function getAvatarAttribute()
    {
        $avatar = '';
        $id = $this->attributes['id'] ?? "";
//        $user = User::find($id);
//        $entity_type_id = $user->entity_type_id ?? '';
        /*if($entity_type_id == EntityTypes::ADMIN || $entity_type_id == EntityTypes::MANAGER || $entity_type_id == EntityTypes::SUBMANAGER){
            $manager = Manager::where('user_id',$id)->first();
            if ($manager && $manager->avatar != NULL && !filter_var($manager->avatar, FILTER_VALIDATE_URL)) {
                $avatar = Storage::disk('s3')->url($manager->avatar);
            } else {
                $avatar = $manager->avatar;
            }
        }else {*/
            $userDetail = UserDetail::where('user_id',$id)->first();
            if ($userDetail && $userDetail->avatar != NULL && !filter_var($userDetail->avatar, FILTER_VALIDATE_URL)) {
                $avatar = Storage::disk('s3')->url($userDetail->avatar);
            } else {
                $avatar = ($userDetail) ? $userDetail->avatar : '';
            }
//        }
        return $this->attributes['avatar'] = $avatar;
    }
    public function getCreatedAtAttribute($date)
    {
        $date = new Carbon($date);
        return $date->format('d-m-Y H:i');
    }

    public function getUpdatedAtAttribute($date)
    {
        $date = new Carbon($date);
        return $date->format('d-m-Y H:i');
    }

    public function getLastVerifyAttribute($date)
    {
        if($date){
            $date = new Carbon($date);
            return $date->format('Y-m-d');
        }
        return $date;
    }

    public function getVerifyStatusAttribute()
    {
        $id = $this->attributes['id'] ?? "";
        if ($id==""){
            return "";
        }

        $user = User::find($id);
        $config = Config::where('key',Config::REVERIFY_USER_PHONE_NUMBER_DAYS)->first();
        $days = $config ? (int) filter_var($config->value, FILTER_SANITIZE_NUMBER_INT) : 90;

        $lastDate = new Carbon($user->last_verify);
        $currentDate = Carbon::now();
        $lastDate = $lastDate->addDays($days);

        if($currentDate->equalTo($lastDate) || $currentDate->greaterThan($lastDate)) {
            return $this->attributes['verify_status'] = 1;
        }else {
            return $this->attributes['verify_status'] = 0;
        }
    }

    public function entityType()
    {
        return $this->hasMany(UserEntityRelation::class, 'user_id', 'id');
    }

    public function getSnsTypeAttribute()
    {
        $sns_type = '';
        $id = $this->attributes['id'] ?? "";

        $userDetail = UserDetail::where('user_id',$id)->first();
        $sns_type = $userDetail && $userDetail->sns_type ? $userDetail->sns_type : '';

        return $this->attributes['sns_type'] = $sns_type;
    }

    public function getSnsLinkAttribute()
    {
        $sns_link = '';
        $id = $this->attributes['id'] ?? "";

        $userDetail = UserDetail::where('user_id',$id)->first();
        $sns_link = $userDetail && $userDetail->sns_link ? $userDetail->sns_link : '';

        return $this->attributes['sns_link'] = $sns_link;
    }

    public function AllDevices()
    {
        return $this->hasMany(UserDevices::class);
    }

    public function getDisplayCreatedAtAttribute(){
        if (isset($this->attributes['created_at'])) {
            $created_at = $this->attributes['created_at'];
            return $this->attributes['display_created_at'] = Carbon::parse($created_at)->format('Y-m-d H:i:s');
        }
        return "";
    }

    public function sendPasswordResetNotification($token)
    {
        $url = Session::get('login-route');
        $id = $this->attributes['id'];
        $user = User::find($id);
        $isAdminUser = $user->hasAnyRole(['Admin','Manager','sub_manager']);
        $this->notify(new PasswordReset($token,$this->attributes['email'],$isAdminUser,$url));
    }
}
