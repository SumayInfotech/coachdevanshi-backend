<?php

return [

    'required' => 'Il campo :attribute è obbligatorio.',
    'email'    => ':attribute deve essere un indirizzo email valido.',
    'unique'   => ':attribute è già stato utilizzato.',
    'digits'   => ':attribute deve contenere :digits cifre.',
    'min'      => [
        'string' => ':attribute deve contenere almeno :min caratteri.',
    ],
    'max'      => [
        'string' => ':attribute non può superare :max caratteri.',
        'file'   => ':attribute non può superare :max kilobyte.',
    ],
    'image'    => ':attribute deve essere un\'immagine.',
    'mimes'    => ':attribute deve essere un file di tipo: :values.',
    'exists'   => ':attribute selezionato non è valido.',
    'in'       => ':attribute selezionato non è valido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Attribute Names
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name'       => 'nome',
        'email'      => 'email',
        'mobile_no'  => 'numero di cellulare',
        'password'   => 'password',
        'status'     => 'stato',
        'role_uuid'  => 'ruolo',
        'image'      => 'immagine profilo',
        'state_id'   => 'stato',
        'city_id'    => 'città',
    ],
];
