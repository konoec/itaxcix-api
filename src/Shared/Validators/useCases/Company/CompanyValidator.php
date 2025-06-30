<?php

namespace itaxcix\Shared\Validators\useCases\Company;

class CompanyValidator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validar RUC
        if (empty($data['ruc'])) {
            $errors['ruc'] = 'El RUC es obligatorio';
        } elseif (!$this->isValidRuc($data['ruc'])) {
            $errors['ruc'] = 'El RUC debe tener 11 dígitos';
        }

        // Validar nombre (opcional pero si se envía debe ser válido)
        if (isset($data['name']) && !empty($data['name'])) {
            if (strlen($data['name']) < 2) {
                $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
            } elseif (strlen($data['name']) > 255) {
                $errors['name'] = 'El nombre no puede exceder 255 caracteres';
            }
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

        // Validar RUC para filtro
        if (isset($params['ruc']) && !empty($params['ruc']) && !$this->isValidRuc($params['ruc'])) {
            $errors['ruc'] = 'El RUC para filtro debe tener 11 dígitos';
        }

        // Validar ordenamiento
        if (isset($params['sortBy']) && !empty($params['sortBy'])) {
            $validSortFields = ['id', 'ruc', 'name', 'active'];
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

    private function isValidRuc(string $ruc): bool
    {
        return preg_match('/^\d{11}$/', $ruc);
    }
}
