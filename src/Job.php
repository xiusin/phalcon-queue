<?php

namespace Xiusin\PhalconQueue;

use Serializable;
use Throwable;

abstract class Job implements Serializable
{
    /**
     * 连接对象
     * @var string
     */
    protected string $connection = '';

    /**
     * @var int 延迟执行时间，单位为秒
     */
    protected int $delay = 0;

    /**
     * @var int 重试次数
     */
    protected int $tries;

    /**
     * 重试次数
     * @var int
     */
    protected int $time = 0;

    /**
     * @var string 队列名称
     */
    protected string $queueName = 'default';

    /**
     * @return void 立即投递
     * @throws QueueException
     * @throws Throwable
     */
    public static function dispatchNow()
    {
        $queue = static::dispatch(...func_get_args());
        unset($queue); // 直接销毁投递队列
    }

    /**
     * @return Queue 程序销毁时自动投递
     * @throws QueueException
     * @throws Throwable
     */
    public static function dispatch(): Queue
    {
        return new Queue(new static(...func_get_args()));
    }

    public function getConnection(): string
    {
        return $this->connection;
    }

    public function serialize()
    {
        return serialize(get_object_vars($this));
    }

    public function unserialize($data)
    {
        $data = unserialize($data);

        foreach ($data as $prop => $value) {
            $this->{$prop} = $value;
        }
    }

    /**
     * @param int $delay
     */
    public function setDelay(int $delay): void
    {
        $this->delay = $delay;
    }

    public function getDelay(): int {
        return $this->delay;
    }

    /**
     * @param string $tube
     */
    public function setQueueName(string $tube): void
    {
        $this->queueName = $tube;
    }

    /**
     * @return string
     */
    public function getQueueName(): string
    {
        return $this->queueName;
    }

    abstract public function handle();
}