<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Link Expiration and Throttling
    |--------------------------------------------------------------------------
    |
    | These settings control the security aspects of the generated links,
    | including their expiration time and the throttling mechanism to prevent
    | abuse.
    |
    */

    'expiration' => 5, // Minutes

    'limits' => [
        ['limit' => 1, 'minutes' => 1],
        ['limit' => 3, 'minutes' => 5],
        ['limit' => 5, 'minutes' => 30],
    ],

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
    | Switch to 'plink::mail.notification' if you prefer to use the default
    | Laravel notification template.
    |
    */

    'template' => 'plink::mail.plink',
    // 'template' => 'plink::mail.notification'
];
