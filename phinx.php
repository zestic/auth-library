<?php

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'production_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'development_db',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8',
        ],
        'testing_postgres' => [
            'adapter' => 'pgsql',
            'host' => 'localhost',
            'name' => 'testdb',
            'user' => 'testuser',
            'pass' => 'testpass',
            'port' => '5432',
            'charset' => 'utf8',
        ],
        'testing_mysql' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'testdb',
            'user' => 'testuser',
            'pass' => 'testpass',
            'port' => '3306',
            'charset' => 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
