<?php

namespace EmailQ\Services;

use EmailQ\Enums\EmailStatus;
use EmailQ\Enums\QueueSettings;
use EmailQ\Helpers\Validator;
use EmailQ\Interfaces\ProcessorInterface;
use EmailQ\Models\EmailModel;
use EmailQ\Services\TemplateService;

class EmailTemplateProcessor implements ProcessorInterface
{
    public function create($params, $status = EmailStatus::WAITING)
    {
        $this->validateFields($params);

        $templateName = $params['template_name'];

        unset($params['body'], $params['subject']);

        $email = new EmailModel();
        $email->fill($params);
        $email = $this->applyReplacements($email, $templateName, $params);
        $email->status = $status;

        return $email->save();
    }

    public function validateFields(array $params)
    {
        Validator::validateRequired($params, ['to', 'from', 'template_name']);
        Validator::validateEmailFields($params, ['to', 'cc', 'bcc', 'from', 'reply_to']);
        Validator::validateDateFields($params, ['scheduled_at']);
    }

    public function applyReplacements(EmailModel $email, string $templateName, array $replacements): EmailModel
    {
        $template = (new TemplateService())->getByName($templateName);
        if (!$template) {
            throw new \Exception('Template not found');
        }

        $email->subject = $this->replaceContent($template->subject, $replacements);
        $email->body = $this->replaceContent($template->body, $replacements);
        $email->body = $this->addTrackingImage($email->body);
        return $email;
    }

    private function replaceContent(string $content, array $replacements): string
    {
        foreach ($replacements as $placeholder => $replacement) {
            $content = str_replace("{{ $placeholder }}", $replacement, $content);
        }

        return $content;
    }

    private function addTrackingImage(string $body): string
    {
        if (!empty(QueueSettings::$TRACKING_IMAGE)) {
            $body .= ' <p><img src="' . QueueSettings::$TRACKING_IMAGE . '" /></p>';
        }

        return $body;
    }
}
