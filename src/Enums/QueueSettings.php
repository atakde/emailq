<?php

namespace EmailQ\Enums;

class QueueSettings
{
    public static $MAX_CHUNK_SIZE = 5000;
    public static $SCHEDULED_EMAILS_RANGE_IN_MINUTES = 5;

    public static function set(array $config)
    {
        self::$MAX_CHUNK_SIZE = $config['MAX_CHUNK_SIZE'] ?? self::$MAX_CHUNK_SIZE;
        self::$SCHEDULED_EMAILS_RANGE_IN_MINUTES = $config['SCHEDULED_EMAILS_RANGE_IN_MINUTES']
            ?? self::$SCHEDULED_EMAILS_RANGE_IN_MINUTES;
    }
}
