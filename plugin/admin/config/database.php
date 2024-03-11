<?php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => config('env.mysql.host'),
            'port'        => config('env.mysql.port'),
            'database'    => config('env.mysql.database'),
            'username'    => config('env.mysql.username'),
            'password'    => config('env.mysql.password'),
            'charset'     => config('env.mysql.charset'),
            'collation'   => config('env.mysql.collation'),
            'prefix'      => config('env.mysql.prefix'),
            'strict'      => true,
            'engine'      => null,
        ],
        'reser_mysql' => [
            'driver'      => 'mysql',
            'host'        => config('env.mysql.host'),
            'port'        => config('env.mysql.port'),
            'database'    => config('env.reser_mysql.database'),
            'username'    => config('env.reser_mysql.username'),
            'password'    => config('env.reser_mysql.password'),
            'charset'     => config('env.mysql.charset'),
            'collation'   => config('env.mysql.collation'),
            'prefix'      => config('env.reser_mysql.prefix'),
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
