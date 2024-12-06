<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Model Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the model used by Plink to store and retrieve
    | one-time passwords. By default, it uses the 'App\Models\User' model.
    |
    */

    'models' => [
        'authenticatable' => env('AUTH_MODEL', App\Models\User::class),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mailable Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the Mailable class used by Plink to send emails.
    | Change this to your own Mailable class if you want to customize the email
    | sending behavior.
    |
    */

    'mailable' => BenBjurstrom\Plink\Mail\PlinkMail::class,

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    |
    | This setting determines the email template used by Plink to send emails.
    | Switch to 'plink::mail.basic' if you prefer to use the standard laravel
    | <x-mail::message> template.
    |
    */

    'template' => 'plink::mail.plink',
    // 'template' => 'plink::mail.basic',
];
