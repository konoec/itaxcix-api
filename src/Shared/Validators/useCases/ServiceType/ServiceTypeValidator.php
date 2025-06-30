<?php

namespace itaxcix\Shared\Validators\useCases\ServiceType;

use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;

class ServiceTypeValidator
{
    private ServiceTypeRepositoryInterface $repository;

    public function __construct(ServiceTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres';
        } elseif ($this->repository->existsByName($data['name'])) {
            $errors['name'] = 'Ya existe un tipo de servicio con este nombre';
        }

        // Validar active
        if (!isset($data['active'])) {
            $data['active'] = true; // Valor por defecto
        } elseif (!is_bool($data['active'])) {
            $errors['active'] = 'El campo activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres';
        } elseif ($this->repository->existsByName($data['name'], $id)) {
            $errors['name'] = 'Ya existe un tipo de servicio con este nombre';
        }

        // Validar active
        if (!isset($data['active'])) {
            $data['active'] = true; // Valor por defecto
        } elseif (!is_bool($data['active'])) {
            $errors['active'] = 'El campo activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateId(int $id): array
    {
        $errors = [];

        if ($id <= 0) {
            $errors['id'] = 'El ID debe ser un número positivo';
        } elseif (!$this->repository->findById($id)) {
            $errors['id'] = 'El tipo de servicio no existe';
        }

        return $errors;
    }
}
