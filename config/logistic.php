<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Logistic API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the external logistic system API integration.
    | This file contains the base URL and endpoints for the logistic system.
    |
    */

    'api' => [
        'base_url' => env('LOGISTIC_API_BASE_URL', 'https://logistic.takshallinone.in'),
        'create_user_with_role_endpoint' => env('LOGISTIC_API_CREATE_USER_ENDPOINT', '/api/v1/users/create-with-role'),
    ],

    /*
    |--------------------------------------------------------------------------
    | API Timeout Settings
    |--------------------------------------------------------------------------
    |
    | Timeout settings for API requests in seconds.
    |
    */

    'timeout' => env('LOGISTIC_API_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Default Role
    |--------------------------------------------------------------------------
    |
    | Default role to assign when creating users in the logistic system.
    |
    */

    'default_role' => env('LOGISTIC_API_DEFAULT_ROLE', 'lm-center'),

];

