<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Signup Fields
    |--------------------------------------------------------------------------
    |
    | Here, you can specify what fields you want to store for your user. The
    | AuthController@signup method will automatically search for current
    | request data fields using names that are contained in this array.
    |
    */
    'signup_fields' => [
        'user_type','id_card','name','surname','birthday','country_id','province_id'
       ,'distric_id','postcode', 'gender','phone', 'email', 'password'
    ],

    /*
    |--------------------------------------------------------------------------
    | Signup Fields Rules
    |--------------------------------------------------------------------------
    |
    | Here you can put the rules you want to use for the validator instance
    | in the signup method.
    |
    */
    'signup_fields_rules' => [
    //  'user_type' => 'required',
      'id_card' => 'required',
      'name' => 'required',
    	'surname' => 'required',
    	'country_id' => 'required',
    	'province_id' => 'required',
    	'distric_id' => 'required',
    	'postcode' => 'required',
    	'gender' => 'required',
    	'phone' => 'required',
    	'email' => 'required|email|unique:users',
    	'password' => 'required|min:6'
    ],

    /*
    |--------------------------------------------------------------------------
    | Signup Token Release
    |--------------------------------------------------------------------------
    |
    | If this field is "true", an authentication token will be automatically
    | released after signup. Otherwise, the signup method will return a simple
    | success message.
    |
    */
    'signup_token_release' => env('API_SIGNUP_TOKEN_RELEASE', true),

    /*
    |--------------------------------------------------------------------------
    | Password Reset Token Release
    |--------------------------------------------------------------------------
    |
    | If this field is "true", an authentication token will be automatically
    | released after password reset. Otherwise, the signup method will return a
    | simple success message.
    |
    */
    'reset_token_release' => env('API_RESET_TOKEN_RELEASE', true),

    /*
    |--------------------------------------------------------------------------
    | Recovery Email Subject
    |--------------------------------------------------------------------------
    |
    | The email address you want use to send the recovery email.
    |
    */
    'recovery_email_subject' => env('API_RECOVERY_EMAIL_SUBJECT', true),

];
