<?php

namespace App\Validators;
use Illuminate\Validation\Rule;

class LoginValidator extends ModelValidator
{
    protected $languageArray = 'validation.user';

    private $storeUpdateRules = [
        'email' => 'required',
        'password' => 'required',
        // 'device_token' => 'required'
    ];

    private $forgotEmailRules = [
        'phone_code' => 'required',
        'phone' => 'required',
    ];

    private $forgotPasswordRules = [
        'email' => 'required|exists:users,email,deleted_at,NULL',
    ];

    private $updatePasswordRules = [
        'email' => 'required|exists:users,email,deleted_at,NULL',
        'password' => 'required'
    ];

    private $reverifyNumberRules = [
        'phone_code' => 'required',
        'phone' => 'required|numeric|unique:users_detail,mobile,NULL,id,deleted_at,NULL',
    ];

    private $updateDeviceTokenRules = [
        'device_token' => 'required',
    ];

    private $updateProfileRules = [
        'name' => 'required',
        'email' => 'required',
        'phone' => 'required',
    ];

    public function validateStore($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->storeUpdateRules);
    }

    public function validateForgotEmail($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->forgotEmailRules);
    
    }
    public function validateForgotPassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->forgotPasswordRules);
    }
    public function validateUpdatePassword($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->updatePasswordRules);
    }
    public function validateReverifyNumber($inputs,$id)
    {
        $this->reverifyNumberRules['phone'] = ['required', Rule::unique('users_detail', 'mobile')->ignore($id)->where(function ($query) use ($inputs,$id){
            $query->whereNull('users_detail.deleted_at');
        })];
        return parent::validateLaravelRules($inputs, $this->reverifyNumberRules);
    }
    public function validateUpdateToken($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->updateDeviceTokenRules);
    }
    public function validateUpdateProfile($inputs)
    {
        return parent::validateLaravelRules($inputs, $this->updateProfileRules);
    }
}
