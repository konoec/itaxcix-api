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

        if (!isset($data['vehicleId']) || !is_numeric($data['vehicleId']) || $data['vehicleId'] <= 0) {
            $errors[] = 'El ID de vehículo es requerido y debe ser un número válido.';
        }

        return $errors;
    }

    public function createDTO(array $data): CitizenToDriverRequestDTO
    {
        return new CitizenToDriverRequestDTO(
            userId: (int) $data['userId'],
            vehicleId: (int) $data['vehicleId']
        );
    }
}
