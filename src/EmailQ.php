<?php

namespace EmailQ;

use EmailQ\Services\DatabaseManager;
use EmailQ\Services\EmailQueue;

class EmailQ
{
    private array $config;
    private EmailQueue $emailQueue;
    private DatabaseManager $databaseManager;

    public function __construct(array $config = [])
    {
        $this->setEmailQueue();
        $this->setConfig($config);
        $this->setScheduleConfig($config);
        $this->initDBConnection($config);
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

    private function initDBConnection($config)
    {
        if (empty($config)) {
            throw new \Exception('Configurations are not set');
        }

        $this->databaseManager = new DatabaseManager($config);
        $this->databaseManager->connect();
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
