<?php

namespace Xiusin\PhalconQueue\Adapter;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPExchange;
use AMQPExchangeException;
use AMQPQueueException;
use Throwable;
use Xiusin\PhalconQueue\AbstractAdapter;
use Xiusin\PhalconQueue\Job;

class AmqpQueue extends AbstractAdapter
{
    protected AMQPConnection $connection;

    protected AMQPChannel $channel;

    protected AMQPExchange $exchange;

    protected \AMQPQueue $queue;

    protected string $table;

    /**
     * @throws AMQPConnectionException
     * @throws AMQPExchangeException
     * @throws AMQPChannelException|AMQPQueueException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->connection = new AMQPConnection($config);
        if (!$this->connection->connect()) {
            throw new AMQPConnectionException('AMQP Connection failed');
        }
        $this->channel = new AMQPChannel($this->connection);

        $this->exchange = new AMQPExchange($this->channel);
        $this->exchange->setName($this->getDI()->getShared('config')->path('queue.amqp.exchange')); // exchange name
        $this->exchange->setType(AMQP_EX_TYPE_DIRECT); // direct exchange type
        $this->exchange->setFlags(AMQP_DURABLE);  // the exchange will survive server restarts
        $this->exchange->declareExchange(); // declare the exchange

        $amqpQueue = new \AMQPQueue($this->channel);
        $amqpQueue->setFlags(AMQP_DURABLE);
        $amqpQueue->declareQueue();
        $this->queue = $amqpQueue;
    }

    /**
     * @throws Throwable
     */
    public function consume(string $queue)
    {
        $this->queue->setName($queue);
        while (true) {
            $queueInfo = $this->queue->get();
            $body = $queueInfo->getBody();
            $this->queue->ack($queueInfo->getDeliveryTag()); // acknowledge the message
        }


    }

    /**
     * @throws AMQPExchangeException
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     */
    public function send(Job $job)
    {
        $this->exchange->publish(serialize($job), $job->getQueueName());
    }

    public function __destruct()
    {
        try {
            $this->connection->disconnect();
        } catch (Throwable $exception) {
        }
    }
}