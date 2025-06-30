<?php

namespace itaxcix\Shared\Validators\useCases\Department;

use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;

class DepartmentValidator
{
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre del departamento es requerido';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre del departamento debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre del departamento no puede exceder 100 caracteres';
        } elseif ($this->departmentRepository->existsByName($data['name'])) {
            $errors['name'] = 'Ya existe un departamento con este nombre';
        }

        // Validar ubigeo
        if (empty($data['ubigeo'])) {
            $errors['ubigeo'] = 'El código ubigeo es requerido';
        } elseif (!preg_match('/^\d{2}$/', $data['ubigeo'])) {
            $errors['ubigeo'] = 'El código ubigeo debe tener exactamente 2 dígitos';
        } elseif ($this->departmentRepository->existsByUbigeo($data['ubigeo'])) {
            $errors['ubigeo'] = 'Ya existe un departamento con este código ubigeo';
        }

        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre del departamento es requerido';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre del departamento debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre del departamento no puede exceder 100 caracteres';
        } elseif ($this->departmentRepository->existsByName($data['name'], $id)) {
            $errors['name'] = 'Ya existe un departamento con este nombre';
        }

        // Validar ubigeo
        if (empty($data['ubigeo'])) {
            $errors['ubigeo'] = 'El código ubigeo es requerido';
        } elseif (!preg_match('/^\d{2}$/', $data['ubigeo'])) {
            $errors['ubigeo'] = 'El código ubigeo debe tener exactamente 2 dígitos';
        } elseif ($this->departmentRepository->existsByUbigeo($data['ubigeo'], $id)) {
            $errors['ubigeo'] = 'Ya existe un departamento con este código ubigeo';
        }

        return $errors;
    }

    public function validatePagination(array $data): array
    {
        $errors = [];

        if (isset($data['page']) && (!is_numeric($data['page']) || $data['page'] < 1)) {
            $errors['page'] = 'La página debe ser un número mayor a 0';
        }

        if (isset($data['limit']) && (!is_numeric($data['limit']) || $data['limit'] < 1 || $data['limit'] > 100)) {
            $errors['limit'] = 'El límite debe ser un número entre 1 y 100';
        }

        if (isset($data['orderBy']) && !in_array($data['orderBy'], ['name', 'ubigeo', 'id'])) {
            $errors['orderBy'] = 'El campo de ordenamiento no es válido';
        }

        if (isset($data['orderDirection']) && !in_array(strtoupper($data['orderDirection']), ['ASC', 'DESC'])) {
            $errors['orderDirection'] = 'La dirección de ordenamiento debe ser ASC o DESC';
        }

        return $errors;
    }
}
