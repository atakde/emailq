<?php

namespace EmailQ\Services;

use EmailQ\Models\EmailModel;
use EmailQ\Enums\EmailStatus as EmailStatus;
use EmailQ\Services\EmailSender;
use EmailQ\Enums\QueueSettings;
use EmailQ\Helpers\Validator;

class EmailQueue
{
    public function add(array $params): bool
    {
        $this->validateFields($params);

        if ($this->isTemplate($params)) {
            $email = new EmailModel();

            $templateName = $params['template_name'];
            $emailTemplateProcessor = new EmailTemplateProcessor();

            unset($params['body'], $params['subject']);
            $email->fill($params);
            $email = $emailTemplateProcessor->applyReplacements($email, $templateName, $params);
            $email->status = EmailStatus::WAITING;
            return $email->save();
        } else {
            $email = new EmailModel();
            $email->fill($params);
            $email->status = EmailStatus::WAITING;
            return $email->save();
        }
    }

    private function validateFields(array $params)
    {
        $this->validateEmailField('to', $params);
        $this->validateEmailField('cc', $params);
        $this->validateEmailField('bcc', $params);
        $this->validateEmailField('from', $params);
        $this->validateEmailField('reply_to', $params);

        if ($this->isTemplate($params)) {
            $this->validateStringField('template_name', $params);
        } else {
            $this->validateStringField('subject', $params);
            $this->validateStringField('body', $params);
        }
    }

    private function isTemplate(array $params): bool
    {
        return !empty($params['template_name']);
    }

    private function validateEmailField(string $key, array $params)
    {
        $required = ['to', 'from'];
        if (!empty($params[$key])) {
            if (!Validator::validateEmail($params[$key])) {
                throw new \Exception("Invalid $key email");
            }
        } elseif (in_array($key, $required)) {
            throw new \Exception("$key email is required");
        }
    }

    private function validateStringField(string $key, array $params)
    {
        $required = ['subject'];
        if (in_array($key, $required) && empty($params[$key])) {
            throw new \Exception("$key is required");
        }
    }

    public function remove(int $id): bool
    {
        $email = EmailModel::find($id);
        return $email->delete();
    }

    public function sendQueuedEmails(): void
    {
        $emails = $this->getByStatus(EmailStatus::WAITING, QueueSettings::$MAX_CHUNK_SIZE);
        $emailSender = new EmailSender();
        foreach ($emails as $email) {
            $response = $emailSender->send($email);
            if ($response) {
                $email->status = EmailStatus::SENT;
                $email->save();
            } else {
                $email->status = EmailStatus::FAILED;
                $email->save();
            }
        }
    }

    public function schedule(array $params, string $date): bool
    {
        $email = new EmailModel();
        $email->fill($params);
        $email->status = EmailStatus::SCHEDULED;
        $email->scheduled_at = $date;
        return $email->save();
    }

    public function sendScheduledEmails(): void
    {
        $now = date('Y-m-d H:i:s');
        $minutes = QueueSettings::$SCHEDULED_EMAILS_RANGE_IN_MINUTES;
        $to = date('Y-m-d H:i:s', strtotime($now . " + $minutes minutes"));
        $emails = $this->getSchduledEmailsByRange($now, $to);

        $emailSender = new EmailSender();
        foreach ($emails as $email) {
            $response = $emailSender->send($email);
            if ($response) {
                $email->status = EmailStatus::SENT;
                $email->save();
            } else {
                $email->status = EmailStatus::FAILED;
                $email->save();
            }
        }
    }

    public function getSchduledEmailsByRange(string $from, string $to)
    {
        $emails = EmailModel::where('status', EmailStatus::SCHEDULED)
            ->where('scheduled_at', '>=', $from)
            ->where('scheduled_at', '<=', $to)
            ->get();

        return $emails;
    }

    public function getByStatus(EmailStatus $status, int $limit)
    {
        return EmailModel::where('status', $status)->limit($limit)->get();
    }

    public function setScheduleConfig(array $config)
    {
        QueueSettings::set($config);
    }
}
