<?php

namespace itaxcix\Shared\Validators\useCases\ContactType;

use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;

class ContactTypeValidator
{
    private ContactTypeRepositoryInterface $contactTypeRepository;

    public function __construct(ContactTypeRepositoryInterface $contactTypeRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;
    }

    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validar nombre
        if (!isset($data['name']) || empty(trim($data['name']))) {
            $errors['name'] = 'El nombre es obligatorio';
        } elseif (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen(trim($data['name'])) > 100) {
            $errors['name'] = 'El nombre no puede tener más de 100 caracteres';
        } elseif ($this->contactTypeRepository->existsByName(trim($data['name']))) {
            $errors['name'] = 'Ya existe un tipo de contacto con este nombre';
        }

        // Validar activo (opcional, por defecto true)
        if (isset($data['active']) && $data['active'] !== '' && !in_array($data['active'], ['0', '1', 0, 1, true, false, 'true', 'false'], true)) {
            $errors['active'] = 'El filtro activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateUpdate(array $data, int $id): array
    {
        $errors = [];

        // Validar que el ID existe
        $existingContactType = $this->contactTypeRepository->findById($id);
        if (!$existingContactType) {
            $errors['id'] = 'El tipo de contacto no existe';
            return $errors;
        }

        // Validar nombre
        if (!isset($data['name']) || empty(trim($data['name']))) {
            $errors['name'] = 'El nombre es obligatorio';
        } elseif (strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen(trim($data['name'])) > 100) {
            $errors['name'] = 'El nombre no puede tener más de 100 caracteres';
        } elseif ($this->contactTypeRepository->existsByName(trim($data['name']), $id)) {
            $errors['name'] = 'Ya existe un tipo de contacto con este nombre';
        }

        // Validar activo
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El campo activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validatePagination(array $data): array
    {
        $errors = [];

        // Validar página
        if (isset($data['page']) && (!is_numeric($data['page']) || (int)$data['page'] < 1)) {
            $errors['page'] = 'La página debe ser un número mayor a 0';
        }

        // Validar límite
        if (isset($data['limit']) && (!is_numeric($data['limit']) || (int)$data['limit'] < 1 || (int)$data['limit'] > 100)) {
            $errors['limit'] = 'El límite debe ser un número entre 1 y 100';
        }

        // Validar campo de ordenamiento
        $allowedSortFields = ['id', 'name', 'active'];
        if (isset($data['sortBy']) && !in_array($data['sortBy'], $allowedSortFields)) {
            $errors['sortBy'] = 'Campo de ordenamiento no válido. Valores permitidos: ' . implode(', ', $allowedSortFields);
        }

        // Validar dirección de ordenamiento
        if (isset($data['sortDirection']) && !in_array(strtoupper($data['sortDirection']), ['ASC', 'DESC'])) {
            $errors['sortDirection'] = 'Dirección de ordenamiento no válida. Valores permitidos: ASC, DESC';
        }

        // Validar filtro activo
        if (isset($data['active']) && $data['active'] !== '' && !in_array($data['active'], ['0', '1', 0, 1, true, false], true)) {
            $errors['active'] = 'El filtro activo debe ser verdadero o falso';
        }

        return $errors;
    }
}
