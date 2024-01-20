<?php

namespace EmailQ\Services;

use EmailQ\Enums\QueueSettings;
use EmailQ\Models\EmailModel;
use EmailQ\Services\TemplateService;

class EmailTemplateProcessor
{
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
