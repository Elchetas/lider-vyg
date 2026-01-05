<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templates are stored in resources/views. You may have additional
    | view paths if you are using packages or custom setups.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This is where Blade stores compiled PHP versions of your templates.
    | THIS PATH MUST EXIST, otherwise Laravel throws:
    | "Please provide a valid cache path."
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
