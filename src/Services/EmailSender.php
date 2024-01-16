<?php

namespace EmailQ\Services;

use EmailQ\Models\EmailModel as EmailModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender
{
    public function send(EmailModel $email): bool
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USERNAME'];
            $mail->Password   = $_ENV['SMTP_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 587;
            $mail->SMTPSecure = 'tls';

            $mail->setFrom($email->from, $email->from_name);
            $mail->addAddress($email->to);

            if (!empty($email->cc)) {
                $mail->addCC($email->cc);
            }

            if (!empty($email->bcc)) {
                $mail->addBCC($email->bcc);
            }

            if (!empty($email->reply_to)) {
                $mail->addReplyTo($email->reply_to);
            }

            $mail->isHTML(true);
            $mail->Subject = $email->subject;
            $mail->Body    = $email->body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // TODO: Log error
            return false;
        }
    }
}
