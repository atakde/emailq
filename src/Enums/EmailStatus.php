<?php

namespace EmailQ\Enums;

enum EmailStatus: string
{
    case WAITING = 'waiting';
    case SENT = 'sent';
    case FAILED = 'failed';
    case SCHEDULED = 'scheduled';
}
