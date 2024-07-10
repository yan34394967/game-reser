<?php

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            // 数据库类型
            'type' => 'mysql',
            // 服务器地址
            'hostname' => config('env.mysql.host', '127.0.0.1'),
            // 数据库名
            'database' => config('env.mysql.database'),
            // 数据库用户名
            'username' => config('env.mysql.username'),
            // 数据库密码
            'password' => config('env.mysql.password'),
            // 数据库连接端口
            'hostport' => config('env.mysql.port'),
            // 数据库连接参数
            'params' => [
                // 连接超时3秒
                \PDO::ATTR_TIMEOUT => 3,
            ],
            // 数据库编码默认采用utf8
            'charset' => config('env.mysql.charset', 'utf8mb4'),
            // 数据库表前缀
            'prefix' => config('env.mysql.prefix'),
            // 断线重连
            'break_reconnect' => true,
            // 关闭SQL监听日志
            'trigger_sql' => false,
            // 自定义分页类
            'bootstrap' =>  ''
        ],
    ],
];
