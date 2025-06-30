<?php

namespace itaxcix\Shared\Validators\useCases\User;

use itaxcix\Shared\DTO\useCases\User\DriverToCitizenRequestDTO;

class DriverToCitizenValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['userId']) || !is_numeric($data['userId']) || $data['userId'] <= 0) {
            $errors[] = 'El ID de usuario es requerido y debe ser un número válido.';
        }

        return $errors;
    }

    public function createDTO(array $data): DriverToCitizenRequestDTO
    {
        return new DriverToCitizenRequestDTO(
            userId: (int) $data['userId']
        );
    }
}
