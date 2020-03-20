<?php

return [

    /*
    |--------------------------------------------------------------------------
    | TAP Keys
    |--------------------------------------------------------------------------
    |
    | The TAP publishable key and secret key give you access to TAP's
    | API. The "publishable" key is typically used when interacting with
    | TAP.js while the "secret" key accesses private API endpoints.
    |
    */

    'secret' => env('TAP_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Tap.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'USD'),

    'webhook_url' => env('WEBHOOK_URL', 'http://payment.test/tap/handle')
];
