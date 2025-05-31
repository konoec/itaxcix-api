<?php

namespace itaxcix\Infrastructure\Notifications;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailNotificationService implements NotificationServiceInterface {

    /**
     * Envía un correo electrónico con el código de verificación.
     *
     * @param string $to El destinatario del correo.
     * @param string $subject El asunto del correo.
     * @param string $code El código de verificación a enviar.
     * @throws Exception Si ocurre un error al enviar el correo.
     */
    public function send(string $to, string $subject, string $code, string $templateType = 'recovery'): void {
        $mail = new PHPMailer(true);
        try {
            // Configurar servidor SMTP, credenciales, etc.
            $mail->isSMTP();
            $mail->Host = $_ENV['MAILER_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAILER_USERNAME'];
            $mail->Password = $_ENV['MAILER_PASSWORD'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = $_ENV['MAILER_PORT'];

            $mail->setFrom($_ENV['MAILER_FROM'], $_ENV['MAILER_NAME']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;

            $mail->addEmbeddedImage(
                __DIR__ . '/../../../public/images/recovery-illustration.png',
                'image'
            );

            // Cargar plantilla según tipo
            $templateName = match ($templateType) {
                'verification' => 'verification-code',
                default => 'recovery-code',
            };

            $template = EmailTemplateLoader::load($templateName, [
                'code' => $code,
                'minutes' => '5'
            ]);

            $mail->Body = $template;

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Error al enviar correo: " . $e->getMessage());
        }
    }
}