<?php

namespace itaxcix\Shared\Validators\useCases\District;

use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Shared\DTO\useCases\District\DistrictRequestDTO;

class DistrictValidator
{
    private DistrictRepositoryInterface $districtRepository;
    private ProvinceRepositoryInterface $provinceRepository;

    public function __construct(
        DistrictRepositoryInterface $districtRepository,
        ProvinceRepositoryInterface $provinceRepository
    ) {
        $this->districtRepository = $districtRepository;
        $this->provinceRepository = $provinceRepository;
    }

    public function validateCreate(DistrictRequestDTO $dto): array
    {
        $errors = [];

        // Validar nombre
        if (empty($dto->getName())) {
            $errors['name'] = 'El nombre del distrito es obligatorio';
        } elseif (strlen($dto->getName()) < 2) {
            $errors['name'] = 'El nombre del distrito debe tener al menos 2 caracteres';
        } elseif (strlen($dto->getName()) > 100) {
            $errors['name'] = 'El nombre del distrito no puede exceder 100 caracteres';
        } elseif ($this->districtRepository->existsByName($dto->getName())) {
            $errors['name'] = 'Ya existe un distrito con este nombre';
        }

        // Validar provincia
        if ($dto->getProvinceId() <= 0) {
            $errors['provinceId'] = 'La provincia es obligatoria';
        } elseif (!$this->provinceRepository->findById($dto->getProvinceId())) {
            $errors['provinceId'] = 'La provincia especificada no existe';
        }

        // Validar ubigeo (opcional)
        if ($dto->getUbigeo() !== null) {
            if (strlen($dto->getUbigeo()) > 20) {
                $errors['ubigeo'] = 'El código ubigeo no puede exceder 20 caracteres';
            } elseif ($this->districtRepository->existsByUbigeo($dto->getUbigeo())) {
                $errors['ubigeo'] = 'Ya existe un distrito con este código ubigeo';
            }
        }

        return $errors;
    }

    public function validateUpdate(DistrictRequestDTO $dto): array
    {
        $errors = [];

        // Validar ID
        if (!$dto->getId() || $dto->getId() <= 0) {
            $errors['id'] = 'ID de distrito inválido';
            return $errors; // Si no hay ID válido, no continuar
        }

        // Verificar que el distrito existe
        if (!$this->districtRepository->findById($dto->getId())) {
            $errors['id'] = 'El distrito especificado no existe';
            return $errors;
        }

        // Validar nombre
        if (empty($dto->getName())) {
            $errors['name'] = 'El nombre del distrito es obligatorio';
        } elseif (strlen($dto->getName()) < 2) {
            $errors['name'] = 'El nombre del distrito debe tener al menos 2 caracteres';
        } elseif (strlen($dto->getName()) > 100) {
            $errors['name'] = 'El nombre del distrito no puede exceder 100 caracteres';
        } elseif ($this->districtRepository->existsByName($dto->getName(), $dto->getId())) {
            $errors['name'] = 'Ya existe otro distrito con este nombre';
        }

        // Validar provincia
        if ($dto->getProvinceId() <= 0) {
            $errors['provinceId'] = 'La provincia es obligatoria';
        } elseif (!$this->provinceRepository->findById($dto->getProvinceId())) {
            $errors['provinceId'] = 'La provincia especificada no existe';
        }

        // Validar ubigeo (opcional)
        if ($dto->getUbigeo() !== null) {
            if (strlen($dto->getUbigeo()) > 20) {
                $errors['ubigeo'] = 'El código ubigeo no puede exceder 20 caracteres';
            } elseif ($this->districtRepository->existsByUbigeo($dto->getUbigeo(), $dto->getId())) {
                $errors['ubigeo'] = 'Ya existe otro distrito con este código ubigeo';
            }
        }

        return $errors;
    }

    public function validateDelete(int $id): array
    {
        $errors = [];

        if ($id <= 0) {
            $errors['id'] = 'ID de distrito inválido';
            return $errors;
        }

        if (!$this->districtRepository->findById($id)) {
            $errors['id'] = 'El distrito especificado no existe';
        }

        return $errors;
    }
}
