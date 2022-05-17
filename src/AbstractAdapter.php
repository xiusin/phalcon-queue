<?php

namespace Xiusin\PhalconQueue;

use Phalcon\Di\Injectable;
use Throwable;

abstract class AbstractAdapter extends Injectable implements QueueAdapter
{
    protected array $config = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 失败Job入库
     * @param Job $job
     * @param Throwable $exception
     */
    protected function pushFailedJob(Job $job, Throwable $exception)
    {
        $this->db->insertAsDict($this->di->getShared('config')->path('queue.database.failed_table'),
            [
                "adapter" => $job->getConnection() ?: $this->di->getShared('config')->path('queue.default'),
                "queue" => $job->getQueueName(),
                "payload" => serialize($job),
                "exception" => $exception->getMessage() . PHP_EOL . $exception->getTraceAsString(),
                "failed_at" => date("Y-m-d H:i:s")
            ]
        );
    }
}