<?php

namespace itaxcix\models\dtos;

use Exception;

class RegisterCitizenRequest
{
    public function __construct(
        public readonly int $documentType,
        public readonly string $documentNumber,
        public readonly string $alias,
        public readonly string $password,
        public readonly array $contactMethod
    ) {
        self::validate($this->documentType, $this->documentNumber, $this->alias, $this->password, $this->contactMethod);
    }

    private static function validate(
        int $documentType,
        string $documentNumber,
        string $alias,
        string $password,
        array $contactMethod
    ): void {
        // Validación del tipo de documento
        if (!in_array($documentType, [1, 2, 3])) {
            throw new Exception("Tipo de documento inválido.", 400);
        }

        // Validación del número de documento según tipo
        switch ($documentType) {
            case 1: // DNI - 8 dígitos
                if (!preg_match('/^\d{8}$/', $documentNumber)) {
                    throw new Exception("Número de DNI inválido. Debe tener 8 dígitos.", 400);
                }
                break;

            case 3: // Carné de Extranjería - E + 8 números o C + 8 números
                if (!preg_match('/^[EC]\d{8}$/i', $documentNumber)) {
                    throw new Exception("Número de Carné de Extranjería inválido. Ejemplo: E12345678", 400);
                }
                break;

            case 2: // Pasaporte - 8 o 9 caracteres alfanuméricos
                if (!preg_match('/^[A-Za-z0-9]{8,9}$/', $documentNumber)) {
                    throw new Exception("Número de pasaporte inválido. Debe tener entre 8 y 9 caracteres alfanuméricos.", 400);
                }
                break;
        }

        // Validación del alias
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $alias)) {
            throw new Exception("Alias inválido. Debe tener entre 3 y 20 caracteres alfanuméricos o guiones bajos.", 400);
        }

        // Validación avanzada de la contraseña
        self::validatePassword($password);

        // Validación del contacto
        if (!isset($contactMethod['email']) && !isset($contactMethod['phone'])) {
            throw new Exception("Debe proporcionar un email o teléfono.", 400);
        }

        // Validación del email (si se proporciona)
        if (isset($contactMethod['email']) && !filter_var($contactMethod['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Correo electrónico no válido.", 400);
        }

        // Validación del teléfono (si se proporciona)
        if (isset($contactMethod['phone']) && !preg_match('/^\+?[0-9]{8,15}$/', $contactMethod['phone'])) {
            throw new Exception("Número de teléfono no válido. Debe tener entre 8 y 15 dígitos.", 400);
        }
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