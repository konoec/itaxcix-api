<?php

namespace itaxcix\Shared\Validators\useCases\FuelType;

use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeRequestDTO;

class FuelTypeValidator
{
    private FuelTypeRepositoryInterface $fuelTypeRepository;

    public function __construct(FuelTypeRepositoryInterface $fuelTypeRepository)
    {
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function validate(FuelTypeRequestDTO $request): array
    {
        $errors = [];

        // Validar nombre
        if (empty(trim($request->name))) {
            $errors['name'] = 'El nombre del tipo de combustible es requerido';
        } elseif (strlen(trim($request->name)) < 2) {
            $errors['name'] = 'El nombre del tipo de combustible debe tener al menos 2 caracteres';
        } elseif (strlen(trim($request->name)) > 50) {
            $errors['name'] = 'El nombre del tipo de combustible no puede exceder 50 caracteres';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ0-9\s\-\.]+$/', trim($request->name))) {
            $errors['name'] = 'El nombre del tipo de combustible solo puede contener letras, números, espacios, guiones y puntos';
        } else {
            // Verificar duplicados
            if ($this->fuelTypeRepository->existsByName(trim($request->name), $request->id)) {
                $errors['name'] = 'Ya existe un tipo de combustible con este nombre';
            }
        }

        // Validar estado activo
        if (!is_bool($request->active)) {
            $errors['active'] = 'El estado activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateForUpdate(FuelTypeRequestDTO $request, int $id): array
    {
        $request->id = $id;
        $errors = $this->validate($request);

        // Verificar que el tipo de combustible existe
        $existingFuelType = $this->fuelTypeRepository->findById($id);
        if (!$existingFuelType) {
            $errors['id'] = 'El tipo de combustible especificado no existe';
        }

        return $errors;
    }
}
