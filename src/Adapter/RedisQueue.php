<?php

namespace app\components\queue\adapter;

use Redis;
use RedisException;
use Throwable;
use Xiusin\PhalconQueue\AbstractAdapter;
use Xiusin\PhalconQueue\Job;

class RedisQueue extends AbstractAdapter
{
    protected Redis $client;

    protected string $table;

    /**
     * @throws RedisException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->client = new Redis();

        if ($config['unix']) {
            $ret = $this->client->connect($config['unix']);
        } else {
            $ret = $this->client->connect($config['host'], $config['port'], $config['timeout']);
        }

        if (!$ret) {
            throw new RedisException('Redis connect failed');
        }

        if (($config['password'] ?? '') && !$this->client->auth($config['password'])) {
            throw new RedisException('Redis auth failed');
        }

        if (!$this->client->select($config['database'] ?? 0)) {
            throw new RedisException('Redis select database failed');
        }
    }

    /**
     * @throws Throwable
     */
    public function consume(string $queue)
    {
        while (true) {
            $this->client->blPop(); // 取出一个任务
        }
    }

    public function send(Job $job)
    {

    }
}