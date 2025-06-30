<?php

namespace itaxcix\Shared\Validators\useCases\VehicleClass;

use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;

class VehicleClassValidator
{
    private VehicleClassRepositoryInterface $repository;

    public function __construct(VehicleClassRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres';
        } elseif ($this->repository->existsByName($data['name'])) {
            $errors['name'] = 'Ya existe una clase de vehículo con este nombre';
        }

        // Validate active
        if (!isset($data['active'])) {
            $data['active'] = true; // Default value
        } elseif (!is_bool($data['active'])) {
            $errors['active'] = 'El campo activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = [];

        // Validate name
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre es requerido';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($data['name']) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres';
        } elseif ($this->repository->existsByName($data['name'], $id)) {
            $errors['name'] = 'Ya existe una clase de vehículo con este nombre';
        }

        // Validate active
        if (!isset($data['active'])) {
            $data['active'] = true; // Default value
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
            $errors['id'] = 'La clase de vehículo no existe';
        }

        return $errors;
    }
}
