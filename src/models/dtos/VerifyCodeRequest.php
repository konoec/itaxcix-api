<?php

namespace itaxcix\models\dtos;

use Exception;

class VerifyCodeRequest
{
    public function __construct(
        public readonly string $code,
        public readonly ?string $email = null,
        public readonly ?string $phone = null
    ) {
        self::validate($this->code, $this->email, $this->phone);
    }

    private static function validate(string $code, ?string $email, ?string $phone): void
    {
        // Validar que se haya proporcionado al menos un medio de contacto
        if (!$email && !$phone) {
            throw new Exception("Se requiere al menos un email o número de teléfono.", 400);
        }

        // Validar que el código no esté vacío
        if (empty(trim($code))) {
            throw new Exception("El código es obligatorio.", 400);
        }

        // Opcional: validar longitud del código (por ejemplo, 6 caracteres)
        if (strlen($code) !== 6) {
            throw new Exception("El código debe tener exactamente 6 caracteres.", 400);
        }

        // Opcional: validar que el código sea solo números o alfanumérico
        if (!preg_match('/^[A-Za-z0-9]{6}$/', $code)) {
            throw new Exception("El código debe ser alfanumérico y tener 6 caracteres.", 400);
        }

        // Validar formato del email, si se proporciona
        if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Correo electrónico no válido.", 400);
        }

        // Validar formato del teléfono, si se proporciona
        if ($phone !== null && !preg_match('/^\+?[0-9]{8,15}$/', $phone)) {
            throw new Exception("Número de teléfono no válido. Debe tener entre 8 y 15 dígitos.", 400);
        }
    }
}