<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Token Lifetimes
    |--------------------------------------------------------------------------
    |
    | This option controls the default access token and refresh token lifetimes
    | when issuing them via the Passport::tokensExpireIn and
    | Passport::refreshTokensExpireIn methods. You're free to tweak
    | these values based on the requirements of your application.
    |
    */

    'tokens_expire_in' => env('PASSPORT_TOKENS_EXPIRE_IN', 86400), // Default access token lifetime (in seconds)
    'refresh_tokens_expire_in' => env('PASSPORT_REFRESH_TOKENS_EXPIRE_IN', 864000), // Default refresh token lifetime (in seconds)

    /*
    |--------------------------------------------------------------------------
    | Personal Access Token Lifetimes
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of minutes that personal access tokens
    | should be allowed to remain active. By default, personal access tokens
    | are set to expire after one year (525600 minutes).
    |
    */

    'personal_access_tokens_expire_in' => env('PASSPORT_PERSONAL_ACCESS_TOKENS_EXPIRE_IN', 525600),

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    |
    | Passport allows you to define a set of scopes for your API routes. These
    | scopes can be assigned to tokens to control the access of the token
    | to certain parts of your API. You may define your scopes and
    | their descriptions here.
    |
    */

    'scopes' => [
        'read' => 'Read access',
        'write' => 'Write access',
        // Define your custom scopes here...
    ],
];
