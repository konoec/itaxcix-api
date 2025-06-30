<?php

namespace itaxcix\Shared\Validators\useCases\Brand;

use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Brand\BrandRequestDTO;

class BrandValidator
{
    private BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function validate(BrandRequestDTO $dto, ?int $excludeId = null): array
    {
        $errors = [];

        // Validar nombre
        if (empty(trim($dto->getName()))) {
            $errors['name'] = 'El nombre de la marca es requerido';
        } else {
            $name = trim($dto->getName());

            // Validar longitud
            if (strlen($name) > 50) {
                $errors['name'] = 'El nombre de la marca no puede exceder 50 caracteres';
            }

            // Validar formato básico
            if (!preg_match('/^[a-zA-Z0-9\s\-\.]+$/u', $name)) {
                $errors['name'] = 'El nombre de la marca contiene caracteres no válidos';
            }

            // Validar unicidad
            if ($this->brandRepository->existsByName($name, $excludeId)) {
                $errors['name'] = 'Ya existe una marca con este nombre';
            }
        }

        return $errors;
    }

    public function validateForUpdate(BrandRequestDTO $dto, int $id): array
    {
        return $this->validate($dto, $id);
    }

    public function validateForCreate(BrandRequestDTO $dto): array
    {
        return $this->validate($dto);
    }
}
