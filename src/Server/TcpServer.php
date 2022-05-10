<?php

namespace Asiries335\redisSteamPhp\Server;

use Asiries335\redisSteamPhp\Consumer\ConsumerInterface;
use React\Socket\ConnectionInterface;

/**
 *
 */
class TcpServer implements ServerInterface
{

    private array $config;

    /**
     * @var \React\Socket\ServerInterface
     */
    private $tcpServer = null;


    /**
     * @var ConsumerInterface[]
     */
    private array $consumers;

    /**
     * @param array $config
     * @return void
     */
    public function setConfig(array $config): void {
        $this->config = $config;
    }

    /**
     * @return void
     */
    public function start(): void {
        if ($this->tcpServer !== null) {
            return;
        }

        $ip = $this->config['ip'] ?? '0.0.0.0';
        $port = $this->config['port'] ?? '2341';

        $this->tcpServer = new \React\Socket\TcpServer($ip . ':' . $port);

        $this->tcpServer->on('connection', function (ConnectionInterface $connection) {

            $connection->pipe($connection);

            $connection->on('data', function ($payload) {
                foreach ($this->consumers as $consumer) {
                    $consumer->handle($payload);
                }
            });
        });
    }

    /**
     * @return void
     */
    public function down(): void {
        $this->tcpServer->close();
    }

    /**
     * @param ConnectionInterface[] $consumers
     * @return void
     */
    public function setConsumers(array $consumers): void {
        $this->consumers = $consumers;
    }
}