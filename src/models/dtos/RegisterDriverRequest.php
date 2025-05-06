<?php

namespace itaxcix\models\dtos;

use Exception;

class RegisterDriverRequest
{
    public function __construct(
        public readonly string $documentType,
        public readonly string $documentNumber,
        public readonly string $alias,
        public readonly string $password,
        public readonly array $contactMethod,
        public readonly string $licensePlate
    ) {
        self::validate(
            $this->documentType,
            $this->documentNumber,
            $this->alias,
            $this->password,
            $this->contactMethod,
            $this->licensePlate
        );
    }

    private static function validate(
        string $documentType,
        string $documentNumber,
        string $alias,
        string $password,
        array $contactMethod,
        string $licensePlate
    ): void {
        // Validación del tipo de documento: solo se permite RUC
        if ($documentType !== '4') {
            throw new Exception("Solo se permite RUC como tipo de documento.", 400);
        }

        // Validación del RUC - 11 dígitos y debe empezar con 10, 15, 16, 17 o 20
        if (!preg_match('/^(10|15|16|17|20)\d{9}$/', $documentNumber)) {
            throw new Exception("Número de RUC inválido. Debe tener 11 dígitos y empezar con 10, 15, 16, 17 o 20.", 400);
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

        if (isset($contactMethod['email']) && !filter_var($contactMethod['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Correo electrónico no válido.", 400);
        }

        if (isset($contactMethod['phone']) && !preg_match('/^\+?[0-9]{8,15}$/', $contactMethod['phone'])) {
            throw new Exception("Número de teléfono no válido. Debe tener entre 8 y 15 dígitos.", 400);
        }

        // Validación de la placa del vehículo
        if (!preg_match('/^[A-Z0-9]{6}$/', $licensePlate)) {
            throw new Exception("Placa del vehículo no válida. Debe tener 6 caracteres alfanuméricos mayúsculos.", 400);
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
