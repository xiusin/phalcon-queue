<?php


return [
    'default' => 'database',
    'beanstalkd' => [
        'host' => '127.0.0.1',
        'port' => '11300',
        'connect_timeout' => 30
    ],
    'amqp' => [

    ],
    'sync' => [

    ],
    'null' => [

    ],
    'database' => [
        'connection' => 'db',
        'table' => 'jobs',
        'failed_table' => 'failed_jobs',
        'schema' => "juwei_crm",
        'interval' => 200,  // 轮空时间间隔 (ms)
    ]
];
