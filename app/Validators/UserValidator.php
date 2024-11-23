<?php

namespace App\Validators;

use Illuminate\Validation\Rule;

class UserValidator extends ModelValidator
{
    protected $languageArray = 'validation.user';

    private $storeUpdateRules = [
		'email' => 'required|string|max:255|unique:users,email,NULL,id,deleted_at,NULL',
		'password' => 'required|string|min:6',
        'name' => 'required',
        'phone_code' => 'required',
        'phone' => 'required|numeric',
        'gender' => 'required|numeric',
        'device_id' => 'required',
        'device_type_id' => 'required',
        'device_token' => 'required',
        'recommend_code' => 'nullable',
        'avatar' => 'image|mimes:jpeg,jpg,gif,svg,png',
    ];

    private $emailValidateRules = [
		'email' => 'required|string|max:255|unique:users,email,NULL,id,deleted_at,NULL'
    ];

    public function validateRegister($inputs,$language_id)
    {
        if($language_id < 4){
            $this->languageArray = 'validation.user-'.$language_id;
        }
        return parent::validateLaravelRules($inputs, $this->emailValidateRules);
    }

    public function validateStore($inputs,$language_id)
    {
        if($language_id < 4){
            $this->languageArray = 'validation.user-'.$language_id;
        }
        return parent::validateLaravelRules($inputs, $this->storeUpdateRules);
    }

}
