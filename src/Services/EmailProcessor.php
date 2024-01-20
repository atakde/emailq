<?php

namespace EmailQ\Services;

use EmailQ\Enums\EmailStatus;
use EmailQ\Enums\QueueSettings;
use EmailQ\Helpers\Validator;
use EmailQ\Interfaces\ProcessorInterface;
use EmailQ\Models\EmailModel;

class EmailProcessor implements ProcessorInterface
{
    public function create(array $params, $status = EmailStatus::WAITING)
    {
        $this->validateFields($params);

        $params = $this->addTrackingImage($params);

        $email = new EmailModel();
        $email->fill($params);
        $email->status = $status;
        return $email->save();
    }

    public function validateFields(array $params)
    {
        Validator::validateRequired($params, ['to', 'from', 'subject', 'body']);
        Validator::validateEmailFields($params, ['to', 'cc', 'bcc', 'from', 'reply_to']);
        Validator::validateDateFields($params, ['scheduled_at']);
    }

    private function addTrackingImage(array $params): array
    {
        if (!empty(QueueSettings::$TRACKING_IMAGE)) {
            $params['body'] .= ' <p><img src="' . QueueSettings::$TRACKING_IMAGE . '" /></p>';
        }

        return $params;
    }
}
