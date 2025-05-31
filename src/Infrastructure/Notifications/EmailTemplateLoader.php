<?php

namespace itaxcix\Infrastructure\Notifications;

class EmailTemplateLoader {
    public static function load(string $templateName, array $replacements = []): string {
        $path = __DIR__ . "/../../templates/emails/{$templateName}.html";

        if (!file_exists($path)) {
            throw new \Exception("La plantilla {$templateName} no existe.");
        }

        $content = file_get_contents($path);

        foreach ($replacements as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        return $content;
    }
}