<?php

namespace EmailQ\Helpers;

class Validator
{
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
