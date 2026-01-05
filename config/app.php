<?php

return [

    'name' => 'Lider VYG',

    'env' => 'production',

    'debug' => true, // solo mientras depuramos

    'url' => 'http://localhost',

    'timezone' => 'UTC',

    'locale' => 'en',

    'fallback_locale' => 'en',

    'faker_locale' => 'en_US',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key (CRÍTICO)
    |--------------------------------------------------------------------------
    */

    'key' => 'base64:ltlmqYd2BRwoIBc0MPitWY17zMT8i2y3oPgQppt9FgI=',

    'cipher' => 'AES-256-CBC',

    'previous_keys' => [],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    */

    'maintenance' => [
        'driver' => 'file',
    ],

];
