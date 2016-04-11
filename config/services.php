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
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'google' => [
        'client_id' => '311950536621-5qcn0pehiqhs1adkd1ruh4f76343h76a.apps.googleusercontent.com',
        'client_secret' => 'QFDlV7sfL40KylsdBJGYEFJD',
        'redirect' => 'http://iolab.sk:8013/auth/provider/google/callback',
    ],
    'facebook' => [
        'client_id' => '976155262445119',
        'client_secret' => 'f07157bb9cdac5ef85b50a701280be97',
        'redirect' => 'http://147.175.105.140:8013/auth/provider/facebook/callback',
    ],
    //147.175.105.140

];
