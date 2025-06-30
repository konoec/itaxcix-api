<?php

namespace itaxcix\Shared\Validators\useCases\Model;

use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;

class ModelValidator
{
    private ModelRepositoryInterface $modelRepository;
    private BrandRepositoryInterface $brandRepository;

    public function __construct(
        ModelRepositoryInterface $modelRepository,
        BrandRepositoryInterface $brandRepository
    ) {
        $this->modelRepository = $modelRepository;
        $this->brandRepository = $brandRepository;
    }

    public function validateCreate(array $data): array
    {
        $errors = [];

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre del modelo es requerido';
        } else {
            $name = trim($data['name']);
            if (strlen($name) < 2) {
                $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
            } elseif (strlen($name) > 100) {
                $errors['name'] = 'El nombre no puede exceder 100 caracteres';
            } elseif (isset($data['brandId']) && $data['brandId'] !== null) {
                // Validar nombre único por marca
                if ($this->modelRepository->existsByNameAndBrand($name, $data['brandId'])) {
                    $errors['name'] = 'Ya existe un modelo con este nombre para la marca seleccionada';
                }
            }
        }

        // Validar marca (opcional)
        if (isset($data['brandId']) && $data['brandId'] !== null) {
            if (!is_numeric($data['brandId']) || $data['brandId'] <= 0) {
                $errors['brandId'] = 'La marca debe ser un ID válido';
            } else {
                $brand = $this->brandRepository->findById((int)$data['brandId']);
                if (!$brand) {
                    $errors['brandId'] = 'La marca seleccionada no existe';
                } elseif (!$brand->isActive()) {
                    $errors['brandId'] = 'La marca seleccionada no está activa';
                }
            }
        }

        // Validar estado activo
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El estado activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validateUpdate(array $data, int $modelId): array
    {
        $errors = [];

        // Validar que el modelo existe
        $existingModel = $this->modelRepository->findById($modelId);
        if (!$existingModel) {
            $errors['id'] = 'El modelo especificado no existe';
            return $errors;
        }

        // Validar nombre
        if (empty($data['name'])) {
            $errors['name'] = 'El nombre del modelo es requerido';
        } else {
            $name = trim($data['name']);
            if (strlen($name) < 2) {
                $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
            } elseif (strlen($name) > 100) {
                $errors['name'] = 'El nombre no puede exceder 100 caracteres';
            } elseif (isset($data['brandId']) && $data['brandId'] !== null) {
                // Validar nombre único por marca (excluyendo el modelo actual)
                if ($this->modelRepository->existsByNameAndBrand($name, $data['brandId'], $modelId)) {
                    $errors['name'] = 'Ya existe un modelo con este nombre para la marca seleccionada';
                }
            }
        }

        // Validar marca (opcional)
        if (isset($data['brandId']) && $data['brandId'] !== null) {
            if (!is_numeric($data['brandId']) || $data['brandId'] <= 0) {
                $errors['brandId'] = 'La marca debe ser un ID válido';
            } else {
                $brand = $this->brandRepository->findById((int)$data['brandId']);
                if (!$brand) {
                    $errors['brandId'] = 'La marca seleccionada no existe';
                } elseif (!$brand->isActive()) {
                    $errors['brandId'] = 'La marca seleccionada no está activa';
                }
            }
        }

        // Validar estado activo
        if (isset($data['active']) && !is_bool($data['active'])) {
            $errors['active'] = 'El estado activo debe ser verdadero o falso';
        }

        return $errors;
    }

    public function validatePagination(array $queryParams): array
    {
        $errors = [];

        // Validar página
        if (isset($queryParams['page'])) {
            if (!is_numeric($queryParams['page']) || $queryParams['page'] < 1) {
                $errors['page'] = 'La página debe ser un número mayor a 0';
            }
        }

        // Validar elementos por página
        if (isset($queryParams['perPage'])) {
            if (!is_numeric($queryParams['perPage']) || $queryParams['perPage'] < 1 || $queryParams['perPage'] > 100) {
                $errors['perPage'] = 'Los elementos por página deben estar entre 1 y 100';
            }
        }

        // Validar ordenamiento
        if (isset($queryParams['sortBy'])) {
            $allowedSortFields = ['id', 'name', 'active'];
            if (!in_array($queryParams['sortBy'], $allowedSortFields)) {
                $errors['sortBy'] = 'Campo de ordenamiento no válido';
            }
        }

        if (isset($queryParams['sortOrder'])) {
            if (!in_array(strtoupper($queryParams['sortOrder']), ['ASC', 'DESC'])) {
                $errors['sortOrder'] = 'El orden debe ser ASC o DESC';
            }
        }

        return $errors;
    }
}
