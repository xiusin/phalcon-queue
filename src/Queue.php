<?php

namespace Xiusin\PhalconQueue;

use Phalcon\Di\Injectable;
use Throwable;

class Queue extends Injectable
{
    protected QueueAdapter $adapter;

    /**
     * @var Job 要投递的任务对象
     */
    private Job $job;

    /**
     * @throws Throwable
     * @throws QueueException
     */
    public function __construct(Job $job)
    {
        $this->job = $job;

        $connection = $job->getConnection();

        /**
         * @var $manager Manager
         */
        $manager = $this->getDI()->getShared(Manager::serviceName());

        $this->adapter = $manager->adapter($connection);
    }

    public function delay(int $delay): self
    {
        $this->job->setDelay($delay);
        return $this;
    }

    public function onQueue(string $tube): Queue
    {
        $this->job->setQueueName($tube);
        return $this;
    }

    public function __destruct()
    {
        $this->adapter->send($this->job);
    }
}