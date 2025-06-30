<?php

namespace itaxcix\Shared\Validators\useCases\UserStatus;

class UserStatusValidator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es obligatorio';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 255) {
            $errors['name'] = 'El nombre no puede exceder 255 caracteres';
        }

        // Validar estado activo
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El estado activo debe ser un valor booleano';
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        return $this->validateCreate($data);
    }

    public function validatePagination(array $params): array
    {
        $errors = [];

        // Validar página
        if (isset($params['page']) && (!is_numeric($params['page']) || $params['page'] < 1)) {
            $errors['page'] = 'La página debe ser un número entero mayor a 0';
        }

        // Validar elementos por página
        if (isset($params['perPage']) && (!is_numeric($params['perPage']) || $params['perPage'] < 1 || $params['perPage'] > 100)) {
            $errors['perPage'] = 'Los elementos por página deben estar entre 1 y 100';
        }

        // Validar ordenamiento
        if (isset($params['sortBy']) && !empty($params['sortBy'])) {
            $validSortFields = ['id', 'name', 'active'];
            if (!in_array($params['sortBy'], $validSortFields)) {
                $errors['sortBy'] = 'Campo de ordenamiento inválido. Campos válidos: ' . implode(', ', $validSortFields);
            }
        }

        // Validar dirección de ordenamiento
        if (isset($params['sortDirection']) && !empty($params['sortDirection'])) {
            if (!in_array(strtolower($params['sortDirection']), ['asc', 'desc'])) {
                $errors['sortDirection'] = 'La dirección de ordenamiento debe ser "asc" o "desc"';
            }
        }

        // Validar filtro de estado activo
        if (isset($params['active']) && $params['active'] !== null && !is_bool($params['active']) && !in_array($params['active'], ['true', 'false', '1', '0'])) {
            $errors['active'] = 'El filtro de estado activo debe ser un valor booleano';
        }

        return $errors;
    }

    public function validateDelete(int $id): array
    {
        $errors = [];

        if ($id <= 0) {
            $errors['id'] = 'El ID debe ser un número entero positivo';
        }

        return $errors;
    }
}
