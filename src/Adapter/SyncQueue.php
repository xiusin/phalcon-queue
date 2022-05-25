<?php

namespace Xiusin\PhalconQueue\Adapter;

use Throwable;
use Xiusin\PhalconQueue\AbstractAdapter;
use Xiusin\PhalconQueue\Job;
use Xiusin\PhalconQueue\QueueException;

class SyncQueue extends AbstractAdapter
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * 同步队列直接消费任务
     *
     * @param Job $job
     */
    public function send(Job $job)
    {
        try {
            $job->handle();
        } catch (Throwable $e) {
            $this->pushFailedJob($job, $e);
        }
    }

    /**
     * @throws QueueException
     */
    public function consume(string $queue)
    {
        throw new QueueException('SyncQueue does not support consume');
    }
}