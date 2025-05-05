<?php

namespace itaxcix\models\dtos;

use Exception;

class ResetPasswordRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly string $newPassword
    ) {
        self::validate($this->userId, $this->newPassword);
    }

    private static function validate(int $userId, string $newPassword): void
    {
        // Validación del userId
        if ($userId <= 0) {
            throw new \Exception("ID de usuario inválido.", 400);
        }

        // Validación avanzada de la contraseña
        self::validatePassword($newPassword);
    }

    private static function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new \Exception("La contraseña debe tener al menos 8 caracteres.", 400);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new \Exception("La contraseña debe contener al menos una letra mayúscula.", 400);
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new \Exception("La contraseña debe contener al menos una letra minúscula.", 400);
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new \Exception("La contraseña debe contener al menos un número.", 400);
        }

        if (!preg_match('/[\W_]/', $password)) {
            throw new \Exception("La contraseña debe contener al menos un carácter especial (ej: !, @, #).", 400);
        }
    }
}