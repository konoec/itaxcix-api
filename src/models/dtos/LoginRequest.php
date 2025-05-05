<?php

namespace itaxcix\models\dtos;

use Exception;

class LoginRequest
{
    public function __construct(
        public readonly string $alias,
        public readonly string $password
    ) {
        self::validate($this->alias, $this->password);
    }

    private static function validate(string $alias, string $password): void
    {
        // Validación del alias
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $alias)) {
            throw new Exception("Alias inválido. Debe tener entre 3 y 20 caracteres alfanuméricos o guiones bajos.", 400);
        }

        // Validación avanzada de la contraseña
        self::validatePassword($password);
    }

    private static function validatePassword(string $password): void
    {
        if (strlen($password) < 8) {
            throw new Exception("La contraseña debe tener al menos 8 caracteres.", 400);
        }

        if (!preg_match('/[A-Z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra mayúscula.", 400);
        }

        if (!preg_match('/[a-z]/', $password)) {
            throw new Exception("La contraseña debe contener al menos una letra minúscula.", 400);
        }

        if (!preg_match('/[0-9]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un número.", 400);
        }

        if (!preg_match('/[\W_]/', $password)) {
            throw new Exception("La contraseña debe contener al menos un carácter especial (ej: !, @, #).", 400);
        }
    }
}