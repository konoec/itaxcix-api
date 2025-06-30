<?php

namespace itaxcix\Core\UseCases\District;

use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Shared\DTO\useCases\District\DistrictRequestDTO;
use itaxcix\Shared\DTO\useCases\District\DistrictResponseDTO;
use RuntimeException;

class DistrictUpdateUseCase
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

    public function execute(DistrictRequestDTO $dto): DistrictResponseDTO
    {
        // Verificar que el distrito existe
        $existingDistrict = $this->districtRepository->findById($dto->getId());
        if (!$existingDistrict) {
            throw new RuntimeException("District with ID {$dto->getId()} not found");
        }

        // Obtener la provincia
        $province = $this->provinceRepository->findById($dto->getProvinceId());
        if (!$province) {
            throw new RuntimeException("Province with ID {$dto->getProvinceId()} not found");
        }

        // Crear el modelo actualizado
        $district = new DistrictModel(
            id: $dto->getId(),
            name: $dto->getName(),
            province: $province,
            ubigeo: $dto->getUbigeo()
        );

        // Actualizar en el repositorio
        $updatedDistrict = $this->districtRepository->update($district);

        // Retornar DTO de respuesta
        return DistrictResponseDTO::fromModel($updatedDistrict);
    }
}
