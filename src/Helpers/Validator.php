<?php

namespace EmailQ\Helpers;

class Validator
{
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateRequired(array $params, array $required): void
    {
        foreach ($required as $key) {
            if (empty($params[$key])) {
                throw new \Exception("$key is required");
            }
        }
    }

    public static function validateEmailFields(array $params, array $fields): void
    {
        foreach ($fields as $key) {
            if (!empty($params[$key])) {
                if (!self::validateEmail($params[$key])) {
                    throw new \Exception("Invalid $key email");
                }
            }
        }
    }

    public static function validateDateFields(array $params, array $fields): void
    {
        foreach ($fields as $key) {
            if (!empty($params[$key])) {
                if (!self::validateDate($params[$key])) {
                    throw new \Exception("Invalid $key date");
                }
            }
        }
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
