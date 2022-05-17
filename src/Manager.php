<?php

namespace Xiusin\PhalconQueue;

use Phalcon\Di\DiInterface;
use Phalcon\Di\Injectable;
use Throwable;

class Manager extends Injectable
{
    /**
     * @var QueueAdapter[]
     */
    protected array $adapters = [];

    protected array $alias = [];

    public function __construct(DiInterface $di = null)
    {
        if ($di) {
            $this->di = $di;
        }
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
        if(!$cfg['adapter']) {
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