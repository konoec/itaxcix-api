<?php

namespace itaxcix\services\notifications;

interface NotificationServiceInterface {
    public function send(string $to, string $subject, string $code, string $templateType = 'recovery'): void;
}