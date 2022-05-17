<?php

namespace app\components\queue\adapters;

use app\components\queue\AbstractAdapter;
use app\components\queue\Job;
use app\components\queue\QueueException;
use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Throwable;

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