<?php namespace App\Validators;

use Validator, Lang;

abstract class ModelValidator
{
    protected function validateLaravelRules($input = [], $rules = [])
    {
        $langArray = isset($this->languageArray) ? $this->languageArray : 'validation.general';
        return Validator::make($input, $rules, Lang::get($langArray));
    }
}