<?php

return [
    /**
     * Options (mysql, sqlite)
     */
    'driver' => 'mysql',
    'mysql' =>[
        'host' => 'localhost',
        'database' => 'db_dimensao',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci'
    ] ,
    'sqlite' =>[
        'database' => 'database.db',
    ],
];