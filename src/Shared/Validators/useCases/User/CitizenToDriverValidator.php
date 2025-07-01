<?php

namespace itaxcix\Shared\Validators\useCases\User;

use itaxcix\Shared\DTO\useCases\User\CitizenToDriverRequestDTO;

class CitizenToDriverValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['userId']) || !is_numeric($data['userId']) || $data['userId'] <= 0) {
            $errors[] = 'El ID de usuario es requerido y debe ser un número válido.';
        }

        if (!isset($data['plateValue']) || empty(trim($data['plateValue']))) {
            $errors[] = 'La placa del vehículo es requerida.';
        }

        if (isset($data['plateValue']) && strlen(trim($data['plateValue'])) > 20) {
            $errors[] = 'La placa del vehículo no puede exceder los 20 caracteres.';
        }

        return $errors;
    }

    public function createDTO(array $data): CitizenToDriverRequestDTO
    {
        return new CitizenToDriverRequestDTO(
            userId: (int) $data['userId'],
            plateValue: trim($data['plateValue'])
        );
    }
}
