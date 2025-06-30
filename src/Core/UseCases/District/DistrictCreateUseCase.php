<?php

namespace itaxcix\Core\UseCases\District;

use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Shared\DTO\useCases\District\DistrictRequestDTO;
use itaxcix\Shared\DTO\useCases\District\DistrictResponseDTO;
use RuntimeException;

class DistrictCreateUseCase
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
        // Obtener la provincia
        $province = $this->provinceRepository->findById($dto->getProvinceId());

        if (!$province) {
            throw new RuntimeException("Province with ID {$dto->getProvinceId()} not found");
        }

        // Crear el modelo
        $district = new DistrictModel(
            id: null,
            name: $dto->getName(),
            province: $province,
            ubigeo: $dto->getUbigeo()
        );

        // Guardar en el repositorio
        $createdDistrict = $this->districtRepository->create($district);

        // Retornar DTO de respuesta
        return DistrictResponseDTO::fromModel($createdDistrict);
    }
}
