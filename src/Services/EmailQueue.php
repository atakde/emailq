<?php

namespace EmailQ\Services;

use EmailQ\Models\EmailModel;
use Exception;
use EmailQ\Enums\EmailStatus;
use EmailQ\Services\EmailSender;
use EmailQ\Enums\QueueSettings;

class EmailQueue
{
    public function add(array $params): bool
    {
        $email = new EmailModel();
        $email->fill($params);
        return $email->save();
    }

    public function remove(int $id): bool
    {
        $email = EmailModel::find($id);
        return $email->delete();
    }

    public function send(): void
    {
        $emails = $this->getByStatus(EmailStatus::WAITING, QueueSettings::MAX_CHUNK_SIZE);
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

    public function getByStatus(string $status, int $limit)
    {
        return EmailModel::where('status', $status)->limit($limit)->get();
    }
}
