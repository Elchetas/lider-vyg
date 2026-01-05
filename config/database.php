<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | En Docker / Render:
    | - SQLite SOLO para desarrollo local
    | - MySQL recomendado en producción
    |
    */

    'default' => 'sqlite',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        /*
        |--------------------------------------------------------------------------
        | SQLite (SOLO LOCAL / LECTURA)
        |--------------------------------------------------------------------------
        */

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => database_path('database.sqlite'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | MySQL / MariaDB (RENDER / PRODUCCIÓN)
        |--------------------------------------------------------------------------
        */

        'mysql' => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'lider_vyg',
            'username' => 'lider_user',
            'password' => 'lider_password',
            'unix_socket' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql')
                ? [
                    \PDO::MYSQL_ATTR_SSL_CA => null,
                ]
                : [],
        ],

        /*
        |--------------------------------------------------------------------------
        | MariaDB (ALIAS)
        |--------------------------------------------------------------------------
        */

        'mariadb' => [
            'driver' => 'mariadb',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'lider_vyg',
            'username' => 'lider_user',
            'password' => 'lider_password',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis (NO USADO EN RENDER FREE)
    |--------------------------------------------------------------------------
    */

    'redis' => [

        'client' => 'phpredis',

        'options' => [
            'cluster' => 'redis',
            'prefix' => Str::slug('lider-vyg').'-db-',
            'persistent' => false,
        ],

        'default' => [
            'host' => '127.0.0.1',
            'password' => null,
            'port' => 6379,
            'database' => 0,
        ],

        'cache' => [
            'host' => '127.0.0.1',
            'password' => null,
            'port' => 6379,
            'database' => 1,
        ],

    ],

];
