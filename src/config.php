<?php


return [
    'default' => "database",
    "beanstalkd" => [
        'host' => '127.0.0.1',
        'port' => '11300',
        'connect_timeout' => 30
    ],
    "redis" => [
        'unix' => '',
        'host' => '127.0.0.1',
        'port' => 6379,
        'connectTimeout' => 2.5,
        'auth' => [],
        'ssl' => ['verify_peer' => false],
        'database' => 0
    ],
    "amqp" => [

    ],
    "database" => [
        'connection' => 'db',
        'table' => 'jobs',
        'failed_table' => 'failed_jobs',
        'schema' => "juwei_crm",
    ]
];
