<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

    'general' => [
        'laravel_error' => 'Thanks! An error has occurred.',
        'unauthenticated' => 'Not authenticated.',
    ],

    'user' => [
        'email.required' => 'The email is required.',
        'email.string' => 'The email is invalid.',
        'email.max' => 'The email is too long.',
        'email.unique' => 'This Email already exists.',
        'password.required' => 'The password is required.',
        'password.string' => 'The password is invalid.',
        'password.max' => 'The password is too long.',
        'name.required' => 'The name is required.',
        'name.string' => 'The name is invalid.',
        'device_id.required' => 'The device id required.',
        'device_type.required' => 'The device type is required.',
        'phone.required' => 'The Phone is required',
        'phone.numeric' => 'The Phone is invalid',
        'phone.unique' => 'The Phone already exists.',
        'phone_code.required' => 'The Phone Code is required',
        'gender' => 'The Gender is required.'
    ],
    
    'profile' => [
        'email.required' => 'The email is required.',
        'email.string' => 'The email is invalid.',
        'email.max' => 'The email is too long.',
        'email.unique' => 'This Email is already exists.',
        'password.required' => 'The password is required.',
        'password.string' => 'The password is invalid.',
        'password.max' => 'The password is too long.',
        'display_name.required' => 'The User Name is required.',
        'first_name.required' => 'The First Name is required.',
        'last_name.required' => 'The Last Name is required.',
        'device_id.required' => 'The device id required.',
        'avatar.mimes' => 'The Avatar is invalid.',
        'device_type.required' => 'The device type is required.',
        'birthday.required' => 'The Birthday is required',
        'phone.required' => 'The Phone is required',
        'phone.numeric' => 'The Phone is invalid',
        'phone.unique' => 'The Phone is already exists.',
        'phone_code.required' => 'The Phone Code is required',
        'gender' => 'The Gender is required.'
    ], 

    'manager' => [
        'email.required' => 'The email is required.',
        'email.string' => 'The email is invalid.',
        'email.max' => 'The email is too long.',
        'email.unique' => 'This email already exists.',
        'password.required' => 'The password is required.',
        'password.string' => 'The password is invalid.',
        'password.max' => 'The password is too long.',
        'name.required' => 'The name is required.',
        'name.string' => 'The name is invalid.',
        'country.required' => 'The country is required.',
        'state.required' => 'The state is required.',
        'city.required' => 'The city is required',        
    ],    
    'request-form' => [
        'name.required' => 'The Name is required.',
        'user_id.required' => 'The User is required',
        'user_id.unique' => 'You have already requsted for one Business.',
        'category_id.required' => 'The Category is required.',
        'entity_type_id.required' => 'The Entity Type is required.',
        'entity_type_id.in' => 'The Entity Type is invalid.',
        'address.required' => 'The Address is required.',
        'latitude.required' => 'The Latitude is Required.',
        'longitude.required' => 'The Longitude is Required.',
        'city_id.required' => 'The city is required.',
        'state_id.required' => 'The State is required.',
        'country_id.required' => 'The country is required.',
        'email.required' => 'The Email Address is required.',
        'email.email' => 'The Email Address is invalid.',
        'email.unique' => 'You have already requsted for this Business.',
        'recommend_code.nullable' => 'The Recommend Code is nullable.',
        'business_licence.required' => 'The Business Licence is required.',
        'business_licence.mimes' => 'The Business Licence is invalid.',
        'best_portfolio.required' => 'The Portfolio is required.',
        'best_portfolio.mimes' => 'The Portfolio is invalid.',
        'interior_photo.required' => 'The Interior Photo is required.',
        'interior_photo.mimes' => 'The Interior Photo is invalid.',
        'identification_card.required' => 'The Identification Card is required.',
        'identification_card.mimes' => 'The Identification Card is invalid.',
    ],
    'shop-profile' => [
        'main_name' => 'The main name is required',
        'category_id' => 'The category id is required',
        'shop_name' => 'The shop name required',
        'email' => 'The email is required',
        'recommend_code' => 'The recommended code is required',
        'shop_id' => 'The shop id is required',
        'portfolio_images' => 'The portfolio images is required',
        'portfolio_image_id' => 'The portfolio image id is required',
        'video_length' => 'The video max length is 15s',
        'latitude' => 'The latitude is required',
        'longitude' => 'The longitude id is required',
    ],
    'shop-price' => [
        'shop_id' => 'The shop id is required',
        'name' => 'The name is required',
        'price' => 'The price is required',
        'discounted_price' => 'The discounted price is required',
        'item_category_id' => 'The item category id is required',
    ],
    'user-profile' => [
        'avatar' => 'The avatar image is required',
        'name' => 'The name is required',
        'gender' => 'The gender is required',
        'mobile' => 'The mobile is required'
    ],
    'hospital-profile' => [
        'main_name' => 'The main name is required',
        'description' => 'The description is required',
        'latitude' => 'The latitude is required',
        'longitude' => 'The longitude id is required',
    ],
    'doctor' => [
        'name' => 'The name is required',
        'gender' => 'The gender is required',
        'avatar' => 'The avatar is required',
        'hospital_id' => 'The hospital id is required',
    ],
    'post' => [
        'title' => 'The title is required',
        'subtitle' => 'The subtitle is required',
        'from_date.required' => 'The from date is required',
        'from_date.date' => 'The from date should be a date',
        'from_date.date_format' => 'Please enter from date in Y-m-d format',
        'from_date.after' => "Please enter today's date or future date in from date ",
        'to_date.required' => 'The to date is required',
        'to_date.date' => 'The to date should be a date',
        'to_date.date_format' => 'Please enter to date in Y-m-d format',
        'to_date.after' => "Please enter date after from date in to date ",
        'final_price' => 'The final price is required',
        'discount_percentage' => 'The disount percentage is required',
        'category_id' => 'The category id is required',
        'is_discount' => 'The is discount is required',
        'thumbnail' => 'The thumbnail is required',
        'hospital_id' => 'The hospital id is required',
    ],
    'request-service' => [
        'entity_type_id' => 'The entity type id is required',
        'entity_id' => 'The entity id is required',
        'booking_date.required' => 'The booking date is required',
        'booking_date.date' => 'The booking date should be a date',
        'booking_date.date_format' => 'Please enter booking date in Y-m-d H:m:i format',
        'booking_date.after' => "Please enter today's date or future date in booking date ",
        'revenue' => 'The revenue is required',
        'comment' => 'The comment is required',
    ],
    'reviews' => [
        'before_images' => 'The before images is required',
        'after_images' => 'The after images is required',
        'rating' => 'The rating is required',
        'review_comment' => 'The review comment is required',
        'shop_id' =>'The shop id is required',
        'shop_id.exists' =>'The shop does not exists',
        'booking_id' =>'The booking id id is required',
        'booking_id.exists' =>'The booking id does not exists',
        'hospital_id' =>'The hospital id id is required',
        'hospital_id.exists' =>'The hospital id does not exists',
        'category_id' =>'The category id id is required',
        'category_id.exists' =>'The category id does not exists',
        'doctor_id' =>'The doctor id id is required',
        'doctor_id.exists' =>'The doctor id does not exists',
    ],
    'community' => [
        'title' => 'The title is required',
        'description' => 'The description is required',
        'category_id' => 'The category id is required',
        'images' => 'The images is required',
    ],
    'report' => [
        'report_type' =>'The report type id is required',
        'report_type.exists' =>'The report type id does not exists',
        'report_category' =>'The report category id is required',
        'report_category.exists' =>'The report category id does not exists',
        'entity_id' =>'The category id id is required',
    ],
    'category' => [
        'category_type_id' => 'The category type id is required',
        'language_id' => 'The language id is required',
    ]

];
