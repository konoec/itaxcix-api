<?php

namespace itaxcix\Infrastructure\Notifications;

interface NotificationServiceInterface {
    public function send(string $to, string $subject, string $code, string $templateType = 'recovery'): void;
}