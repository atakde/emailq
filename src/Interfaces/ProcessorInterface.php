<?php

namespace EmailQ\Interfaces;

use EmailQ\Enums\EmailStatus;

interface ProcessorInterface
{
    public function create(array $params, EmailStatus $status);
    public function validateFields(array $params);
}
