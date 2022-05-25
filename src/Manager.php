<?php

namespace Xiusin\PhalconQueue;

use app\components\queue\adapter\BeanstalkdQueue;
use Phalcon\Di\DiInterface;
use Phalcon\Di\Injectable;
use Throwable;
use Xiusin\PhalconQueue\Adapter\AmqpQueue;
use Xiusin\PhalconQueue\Adapter\DatabaseQueue;
use Xiusin\PhalconQueue\Adapter\NullQueue;
use Xiusin\PhalconQueue\Adapter\SyncQueue;

class Manager extends Injectable
{
    /**
     * @var QueueAdapter[]
     */
    protected array $adapters = [];

    protected array $config = [];

    protected array $alias = [
        'beanstalkd' => BeanstalkdQueue::class,
        "database" => DatabaseQueue::class,
        'amqp' => AmqpQueue::class,
        'sync' => SyncQueue::class,
        'null' => NullQueue::class
    ];

    public function __construct(array $config = [], DiInterface $di = null)
    {
        if ($di) {
            $this->di = $di;
        }
        if (!$config) { // 如果不传, 则默认读取目录内配置文件
            $config = require __DIR__ . '/config.php';
        }

        $this->setConfig($config);
    }

    public static function serviceName(): string
    {
        return 'queueManager';
    }

    /**
     * 判断是否有对应的队列启用
     * @param string|null $name
     * @return bool
     */
    public function connected(?string $name = null): bool
    {
        return isset($this->adapters[$name ?: $this->getDefaultAdapter()]);
    }

    public function setConfig(array $config)
    {
        $this->di->getShared('config')->set('queue', $config);
    }

    private function getDefaultAdapter()
    {
        return $this->di->getShared('config')->path('queue.default');
    }

    /**
     * 获取适配驱动
     *
     * @throws Throwable
     * @throws QueueException
     */
    public function adapter(?string $name = null): QueueAdapter
    {
        $name = $name ?: $this->getDefaultAdapter();
        if (!$this->connected($name)) {
            $cfg = $this->getAdapterConfig($name);
            $adapter = $cfg['adapter'];
            $this->adapters[$name] = new $adapter($cfg);
        }
        return $this->adapters[$name];
    }

    /**
     * 获取适配器配置信息
     * @throws QueueException|Throwable
     */
    protected function getAdapterConfig(string $name)
    {
        $cfg = $this->di->getShared('config')->path('queue.' . $name)->toArray();
        if (!isset($cfg['adapter'])) {
            $cfg['adapter'] = $this->alias[$name] ?? '';
        }
        if (!$cfg['adapter']) {
            throw new QueueException("unsupported queue adapter");
        }

        return $cfg;
    }

    /**
     * 注册别名
     *
     * @param array $alias
     */
    public function alias(array $alias)
    {
        $this->alias = array_merge($this->alias, $alias);
    }
}