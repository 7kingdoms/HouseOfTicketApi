<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
      'domain' => 'mail.iskill.cozing.com',
      'secret' => 'key-48000cc226f6c4b503ddc5a2de4f6f19',
    ],

    'ses' => [
        'key' => 'AKIAIW3AMPXA3EXJLGCA',
        'secret' => 'AiS3oVTQe5QVSiCKNTNrqYG3GQ6LzkLZkOjg81Q16ZYd',
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

];
