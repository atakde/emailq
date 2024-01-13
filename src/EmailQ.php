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

    public function sendQueued()
    {
        return $this->emailQueue->send();
    }
}
