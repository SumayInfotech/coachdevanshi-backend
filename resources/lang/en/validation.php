<?php

return [

    'required' => 'The :attribute field is required.',
    'email'    => 'The :attribute must be a valid email address.',
    'unique'   => 'The :attribute has already been taken.',
    'digits'   => 'The :attribute must be :digits digits.',
    'min'      => [
        'string' => 'The :attribute must be at least :min characters.',
    ],
    'max'      => [
        'string' => 'The :attribute may not be greater than :max characters.',
        'file'   => 'The :attribute may not be greater than :max kilobytes.',
    ],
    'image'    => 'The :attribute must be an image.',
    'mimes'    => 'The :attribute must be a file of type: :values.',
    'exists'   => 'The selected :attribute is invalid.',
    'in'       => 'The selected :attribute is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Attribute Names
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name'       => 'name',
        'email'      => 'email',
        'mobile_no'  => 'mobile number',
        'password'   => 'password',
        'status'     => 'status',
        'role_uuid'  => 'role',
        'image'      => 'profile picture',
        'state_id'   => 'state',
        'city_id'    => 'city',
    ],
];
