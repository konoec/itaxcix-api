<?php

namespace itaxcix\Shared\Validators\useCases\Category;

use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Category\CategoryRequestDTO;

class CategoryValidator
{
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;

    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository)
    {
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
    }

    public function validate(CategoryRequestDTO $request): array
    {
        $errors = [];

        // Validar nombre
        if (empty(trim($request->name))) {
            $errors['name'] = 'El nombre de la categoría es requerido';
        } elseif (strlen(trim($request->name)) < 2) {
            $errors['name'] = 'El nombre de la categoría debe tener al menos 2 caracteres';
        } elseif (strlen(trim($request->name)) > 100) {
            $errors['name'] = 'El nombre de la categoría no puede exceder 100 caracteres';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ0-9\s\-\.]+$/', trim($request->name))) {
            $errors['name'] = 'El nombre de la categoría solo puede contener letras, números, espacios, guiones y puntos';
        } else {
            // Verificar duplicados
            if ($this->vehicleCategoryRepository->existsByName(trim($request->name), $request->id)) {
                $errors['name'] = 'Ya existe una categoría con este nombre';
            }
        }

        // Validar estado activo
        if (!is_bool($request->active)) {
            $errors['active'] = 'El estado activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateForUpdate(CategoryRequestDTO $request, int $id): array
    {
        $request->id = $id;
        $errors = $this->validate($request);

        // Verificar que la categoría existe
        $existingCategory = $this->vehicleCategoryRepository->findById($id);
        if (!$existingCategory) {
            $errors['id'] = 'La categoría especificada no existe';
        }

        return $errors;
    }
}
