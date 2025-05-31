<?php

namespace itaxcix\Infrastructure\Notifications;

use Vonage\Client as VonageClient;
use Vonage\Client\Credentials\Basic as VonageBasic;
use Vonage\SMS\Message\SMS as VonageSMS;
use Exception;

class SmsNotificationService implements NotificationServiceInterface {

    public function send(string $to, string $subject, string $code, string $templateType = 'recovery'): void {
        try {
            // Limpiar y validar número destino
            $cleanedTo = preg_replace('/\D/', '', $to);
            if (strlen($cleanedTo) < 10) {
                throw new Exception("Número de destino inválido: debe contener al menos 10 dígitos.");
            }
            $to = '+' . $cleanedTo;

            // Cargar credenciales desde .env
            $apiKey = $_ENV['VONAGE_API_KEY'] ?? throw new Exception('VONAGE_API_KEY no configurado.');
            $apiSecret = $_ENV['VONAGE_API_SECRET'] ?? throw new Exception('VONAGE_API_SECRET no configurado.');
            $from = $_ENV['VONAGE_PHONE'] ?? throw new Exception('VONAGE_PHONE no configurado.');

            // Crear mensaje basado en el tipo de plantilla
            $messageText = match ($templateType) {
                'verification' => "Tu código de verificación es: $code. Este código expira en 5 minutos.",
                'recovery' => "Tu código de recuperación es: $code. Este código expira en 5 minutos.",
                default => "Código: $code. Este código expira en 5 minutos.",
            };

            // Inicializar cliente Vonage
            $client = new VonageClient(new VonageBasic($apiKey, $apiSecret));

            // Crear mensaje
            $message = new VonageSMS($to, $from, $messageText);
            $message->setType('unicode'); // Soporta tildes y caracteres especiales

            // Enviar mensaje
            $response = $client->sms()->send($message);

            // Obtener resultado
            $sentMessage = $response->current();

            // Verificar si hubo error
            if ($sentMessage->getStatus() !== 0) {
                $errorCode = $sentMessage->getStatus();
                $errorMsg = match($errorCode) {
                    1 => 'Throttling',
                    2 => 'Missing parameters',
                    3 => 'Invalid credentials',
                    4 => 'Internal error',
                    5 => 'Invalid message',
                    6 => 'Number barred',
                    7 => 'Partner account barred',
                    8 => 'Roaming partner not found',
                    9 => 'Communication failure',
                    default => 'Error desconocido',
                };

                throw new Exception("Error al enviar SMS: [$errorCode] $errorMsg");
            }

        } catch (Exception $e) {
            throw new Exception("Error al enviar SMS: " . $e->getMessage());
        }
    }
}