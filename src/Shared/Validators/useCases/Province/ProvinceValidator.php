<?php

namespace itaxcix\Shared\Validators\useCases\Province;

use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Province\ProvinceRequestDTO;

class ProvinceValidator
{
    private ProvinceRepositoryInterface $provinceRepository;
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(ProvinceRepositoryInterface $provinceRepository, DepartmentRepositoryInterface $departmentRepository)
    {
        $this->provinceRepository = $provinceRepository;
        $this->departmentRepository = $departmentRepository;
    }

    public function validate(ProvinceRequestDTO $dto, ?int $excludeId = null): array
    {
        $errors = [];

        // Validar nombre
        if (empty($dto->getName())) {
            $errors['name'] = 'El nombre de la provincia es requerido';
        } elseif (strlen($dto->getName()) < 2) {
            $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
        } elseif (strlen($dto->getName()) > 100) {
            $errors['name'] = 'El nombre no puede exceder 100 caracteres';
        } elseif ($this->provinceRepository->existsByName($dto->getName(), $excludeId)) {
            $errors['name'] = 'Ya existe una provincia con este nombre';
        }

        // Validar departamento
        if (empty($dto->getDepartmentId())) {
            $errors['departmentId'] = 'El departamento es requerido';
        } elseif (!$this->departmentRepository->findById($dto->getDepartmentId())) {
            $errors['departmentId'] = 'El departamento seleccionado no existe';
        }

        // Validar ubigeo
        if (empty($dto->getUbigeo())) {
            $errors['ubigeo'] = 'El código UBIGEO es requerido';
        } elseif (!preg_match('/^\d{4}$/', $dto->getUbigeo())) {
            $errors['ubigeo'] = 'El código UBIGEO debe tener exactamente 4 dígitos';
        } elseif ($this->provinceRepository->existsByUbigeo($dto->getUbigeo(), $excludeId)) {
            $errors['ubigeo'] = 'Ya existe una provincia con este código UBIGEO';
        }

        return $errors;
    }

    public function validatePagination(array $params): array
    {
        $errors = [];

        // Validar página
        if (isset($params['page']) && (!is_numeric($params['page']) || $params['page'] < 1)) {
            $errors['page'] = 'La página debe ser un número mayor a 0';
        }

        // Validar elementos por página
        if (isset($params['perPage']) && (!is_numeric($params['perPage']) || $params['perPage'] < 1 || $params['perPage'] > 100)) {
            $errors['perPage'] = 'Los elementos por página deben estar entre 1 y 100';
        }

        // Validar ordenamiento
        $validSortFields = ['id', 'name', 'ubigeo'];
        if (isset($params['sortBy']) && !in_array($params['sortBy'], $validSortFields)) {
            $errors['sortBy'] = 'Campo de ordenamiento inválido';
        }

        if (isset($params['sortOrder']) && !in_array(strtoupper($params['sortOrder']), ['ASC', 'DESC'])) {
            $errors['sortOrder'] = 'Orden de clasificación inválido (ASC o DESC)';
        }

        return $errors;
    }
}
