<?php

namespace app\components\queue\adapter;

use Phalcon\Db\Adapter\AdapterInterface;
use Throwable;
use Xiusin\PhalconQueue\AbstractAdapter;
use Xiusin\PhalconQueue\Job;

class RedisQueue extends AbstractAdapter
{
    protected AdapterInterface $connection;

    protected string $table;

    public function __construct(array $config)
    {

    }

    /**
     * @throws Throwable
     */
    public function consume(string $queue)
    {
    }

    public function send(Job $job)
    {

    }
}