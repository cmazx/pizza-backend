<?php

//Heroku way configuration
$dbUrl = getenv("DATABASE_URL");
$dbUrlArray = parse_url($dbUrl);
$host = $dbUrlArray["host"] ?? null;
$username = $dbUrlArray["user"] ?? null;
$password = $dbUrlArray["pass"] ?? null;
$database = substr($dbUrlArray["path"], 1);

return [
    'default' => env('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => $dbUrl ?:env('DATABASE_URL'),
            'host' => $dbUrl ? $host :env('DB_HOST', 'postgres'),
            'port' => env('DB_PORT', '5432'),
            'database' => $dbUrl ? $database : env('DB_DATABASE', 'pizzario'),
            'username' => $dbUrl ? $username :env('DB_USERNAME', 'pizzario'),
            'password' => $dbUrl ? $password : env('DB_PASSWORD', 'pizzario'),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],
    ],
    'migrations' => 'migrations',
];
