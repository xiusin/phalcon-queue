<?php

namespace Xiusin\PhalconQueue;

interface QueueAdapter
{
    public function consume(string $queue);

    public function send(Job $job);
}