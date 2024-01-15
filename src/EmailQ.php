<?php

namespace EmailQ;

use Illuminate\Database\Capsule\Manager as Capsule;
use EmailQ\Services\EmailQueue;

class EmailQ
{
    private array $config;
    private EmailQueue $emailQueue;

    public function __construct(array $config = [])
    {
        $this->setEmailQueue();
        $this->setConfig($config);
        $this->setScheduleConfig($config);
        $this->initDBConnection();
    }

    private function setEmailQueue()
    {
        $this->emailQueue = new EmailQueue();
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    private function setScheduleConfig()
    {
        $this->emailQueue->setScheduleConfig([
            'MAX_CHUNK_SIZE' => $this->config['MAX_CHUNK_SIZE'] ?? 5000,
            'SCHEDULED_EMAILS_RANGE_IN_MINUTES' => $this->config['SCHEDULED_EMAILS_RANGE_IN_MINUTES'] ?? 5,
        ]);
    }

    public function initDBConnection()
    {
        if (empty($this->config)) {
            throw new \Exception('Configurations are not set');
        }

        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => $this->config['DB_DRIVER'],
            'host' => $this->config['DB_HOST'],
            'database' => $this->config['DB_NAME'],
            'username' => $this->config['DB_USER'],
            'password' => $this->config['DB_PASSWORD'],
            'charset' => $this->config['DB_CHARSET'],
            'collation' => $this->config['DB_COLLATION'],
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    public function queue($params)
    {
        return $this->emailQueue->add($params);
    }

    public function dequeue($id)
    {
        return $this->emailQueue->remove($id);
    }

    public function sendQueuedEmails()
    {
        $this->emailQueue->sendQueuedEmails();
    }

    public function schedule($date, $params)
    {
        return $this->emailQueue->schedule($date, $params);
    }

    public function sendScheduledEmails()
    {
        $this->emailQueue->sendScheduledEmails();
    }
}
