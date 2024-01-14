<?php

namespace EmailQ;

use EmailQ\Services\EmailQueue;

class EmailQ
{
    private EmailQueue $emailQueue;

    public function __construct()
    {
        $this->emailQueue = new EmailQueue();
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
