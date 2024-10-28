<?php

namespace Xiusin\PhalconQueue\Adapter;

use Phalcon\Db\Adapter\AdapterInterface;
use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Throwable;
use Xiusin\PhalconQueue\AbstractAdapter;
use Xiusin\PhalconQueue\Job;
use Xiusin\PhalconQueue\QueueException;

class DatabaseQueue extends AbstractAdapter
{
    protected AdapterInterface $connection;

    protected string $table;

    /**
     * @throws QueueException
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        $this->table = $this->config['schema'] . '.' . $this->config['table'];

        $this->connection = $this->getDI()->getShared($this->config['connection'] ?: 'db');

        if (!$this->connection->tableExists($this->config['table'], $this->config['schema'])) {
            $ret = $this->connection->createTable($this->config['table'], $this->config['schema'], [
                'columns' => [
                    new Column(
                        'id',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'size' => 10,
                            'notNull' => true,
                            'autoIncrement' => true,
                            'primary' => true,
                        ]
                    ),
                    new Column(
                        'queue',
                        [
                            'type' => Column::TYPE_VARCHAR,
                            'size' => 100,
                            'notNull' => true,
                        ]
                    ),
                    new Column(
                        'payload',
                        [
                            'type' => Column::TYPE_TEXT,
                        ]
                    ),
                    new Column(
                        'available_at',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                    new Column(
                        'created_at',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true
                        ]
                    ),
                ],
                'indexes' => [
                    new Index("idx_queue", ['queue', 'available_at'], ''),
                ]
            ]);

            if (!$ret) {
                throw new QueueException('create table ' . $this->table . ' failed');
            }
        }
    }

    /**
     * @throws Throwable
     */
    public function consume(string $queue)
    {
        ini_set('memory_limit', '256M');
        while (true) {
            $job = $this->connection->query(
                "SELECT * FROM " . $this->table . " WHERE `queue` = ? and `available_at` <= ? ORDER BY id ASC LIMIT 1", //  FOR UPDATE
                [$queue, time()]
            )->fetch();

            if ($job) {
                $payload = null;
                try {
                    $payload = unserialize($job['payload']);
                    $payload->handle();
                } catch (Throwable $e) {
                    try {
                        $this->pushFailedJob($payload, $e);
                    } catch (Throwable $e) {
                    }
                }
                unset($payload);
                $this->connection->delete($this->table, "id = ?", [$job['id']]);
            } else {
                usleep($this->config['interval'] ?? 200);
            }
        }
    }

    public function send(Job $job)
    {
        $this->connection->insert(
            $this->table,
            [
                $job->getQueueName(),
                serialize($job),
                null,
                time() + $job->getDelay(),
                time()
            ],
            [
                'queue',
                'payload',
                'reserved_at',
                'available_at',
                'created_at'
            ]
        );
    }
}
