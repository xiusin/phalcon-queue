<?php

namespace Xiusin\PhalconQueue\Adapter;

use Xiusin\PhalconQueue\AbstractAdapter;
use Xiusin\PhalconQueue\Job;

class NullQueue extends AbstractAdapter
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function send(Job $job)
    {
    }

    public function consume(string $queue)
    {
    }
}