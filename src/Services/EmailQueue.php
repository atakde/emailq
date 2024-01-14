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

    public function sendQueuedEmails(): void
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
        $minutes = QueueSettings::SCHEDULED_EMAILS_RANGE_IN_MINUTES;
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

    public function getByStatus(string $status, int $limit)
    {
        return EmailModel::where('status', $status)->limit($limit)->get();
    }
}
