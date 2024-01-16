<?php

namespace EmailQ\Helpers;

class Validator
{
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateDate(string $date): bool
    {
        $now = new \DateTime();
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if (!$date) {
            return false;
        }

        return $date > $now;
    }
}
