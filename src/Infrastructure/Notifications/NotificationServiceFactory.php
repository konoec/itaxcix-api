<?php

namespace itaxcix\Infrastructure\Notifications;

use Exception;

class NotificationServiceFactory
{
    private EmailNotificationService $emailService;
    private SmsNotificationService $smsService;

    public function __construct(
        EmailNotificationService $emailService,
        SmsNotificationService $smsService
    ) {
        $this->emailService = $emailService;
        $this->smsService = $smsService;
    }

    public function getServiceForContactType(int $contactTypeId): NotificationServiceInterface {
        if ($contactTypeId === 1) { // 1 = Correo
            return $this->emailService;
        } elseif ($contactTypeId === 2) { // 2 = Teléfono
            return $this->smsService;
        }

        throw new Exception("Tipo de contacto no soportado para notificaciones.", 400);
    }
}