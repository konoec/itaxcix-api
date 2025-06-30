<?php

namespace itaxcix\Core\UseCases\Brand;

use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Brand\BrandRequestDTO;
use itaxcix\Shared\DTO\useCases\Brand\BrandResponseDTO;

class BrandUpdateUseCase
{
    private BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function execute(int $id, BrandRequestDTO $dto): BrandResponseDTO
    {
        $existingBrand = $this->brandRepository->findById($id);

        if (!$existingBrand) {
            throw new \InvalidArgumentException('Marca no encontrada');
        }

        $brandModel = new BrandModel(
            id: $id,
            name: trim($dto->getName()),
            active: $dto->isActive()
        );

        $updatedBrand = $this->brandRepository->update($brandModel);

        return new BrandResponseDTO(
            id: $updatedBrand->getId(),
            name: $updatedBrand->getName(),
            active: $updatedBrand->isActive()
        );
    }
}
