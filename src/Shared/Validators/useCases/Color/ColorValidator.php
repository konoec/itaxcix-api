<?php

namespace itaxcix\Shared\Validators\useCases\Color;

use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Color\ColorRequestDTO;

class ColorValidator
{
    private ColorRepositoryInterface $colorRepository;

    public function __construct(ColorRepositoryInterface $colorRepository)
    {
        $this->colorRepository = $colorRepository;
    }

    public function validate(ColorRequestDTO $request): array
    {
        $errors = [];

        // Validar nombre
        if (empty(trim($request->name))) {
            $errors['name'] = 'El nombre del color es requerido';
        } elseif (strlen(trim($request->name)) < 2) {
            $errors['name'] = 'El nombre del color debe tener al menos 2 caracteres';
        } elseif (strlen(trim($request->name)) > 50) {
            $errors['name'] = 'El nombre del color no puede exceder 50 caracteres';
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', trim($request->name))) {
            $errors['name'] = 'El nombre del color solo puede contener letras y espacios';
        } else {
            // Verificar duplicados
            if ($this->colorRepository->existsByName(trim($request->name), $request->id)) {
                $errors['name'] = 'Ya existe un color con este nombre';
            }
        }

        // Validar estado activo
        if (!is_bool($request->active)) {
            $errors['active'] = 'El estado activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateForUpdate(ColorRequestDTO $request, int $id): array
    {
        $request->id = $id;
        $errors = $this->validate($request);

        // Verificar que el color existe
        $existingColor = $this->colorRepository->findById($id);
        if (!$existingColor) {
            $errors['id'] = 'El color especificado no existe';
        }

        return $errors;
    }
}
