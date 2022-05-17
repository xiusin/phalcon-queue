<?php

namespace Xiusin\PhalconQueue;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class QueueProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared(Manager::serviceName(), function () use ($di) {
            return new Manager();
        });
    }
}