<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;
    protected $table = 'addresses';
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    protected $fillable = [
        'address','address2','zipcode','latitude','longitude','main_country','country_id','state_id','city_id','main_address','entity_type_id','entity_id','created_at','updated_at'
    ];

    protected $casts = [
        'address' => 'string',
        'address2' => 'string',
        'zipcode' => 'string',
        'latitude' => 'string',
        'longitude' => 'string',
        'main_country' => 'string',
        'country_id' => 'int',
        'state_id' => 'int',
        'city_id' => 'int',
        'main_address' => 'boolean',
        'entity_type_id' => 'int',
        'entity_id' => 'int',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['entity_type_name', 'country_name', 'state_name', 'city_name'];

    public function getEntityTypeNameAttribute()
    {
        $value = $this->attributes['entity_type_id'];
        $entity = EntityTypes::find($value);
        return $this->attributes['entity_type_name'] = !empty($entity) ? $entity->name : '';
    }

    public function getCountryNameAttribute()
    {
        $value = $this->attributes['country_id'];
        $country = Country::find($value);
        return $this->attributes['country_name'] = !empty($country) ? $country->name : '';
    }

    public function getStateNameAttribute()
    {
        $value = $this->attributes['state_id'];
        $state = State::find($value);
        return $this->attributes['state_name'] = !empty($state) ? $state->name : '';
    }

    public function getCityNameAttribute()
    {
        $value = $this->attributes['city_id'];
        $city = City::find($value);
        return $this->attributes['city_name'] = !empty($city) ? $city->name : '';
    }
    public function getZipcodeAttribute($zipcode)
    {
        $value = $zipcode == NULL ? "" : $zipcode;
        return $value;
    }

    public function getAddress2Attribute($address2)
    {
        $value = $address2 == NULL ? "" : $address2;
        return $value;
    }

    public function entityType()
    {
        return $this->hasOne(EntityTypes::class, 'id', 'entity_type_id');
    }

    public function country()
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

    public function state()
    {
        return $this->hasOne(State::class, 'id', 'state_id');
    }

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }
}
