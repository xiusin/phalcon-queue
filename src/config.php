<?php


return [
    'default' => "database",
    "beanstalkd" => [
        'host' => '127.0.0.1',
        'port' => '11300',
        'connect_timeout' => 30
    ],
    "redis" => [

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
