<?php

namespace itaxcix\Core\UseCases\Brand;

use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Brand\BrandRequestDTO;
use itaxcix\Shared\DTO\useCases\Brand\BrandResponseDTO;

class BrandCreateUseCase
{
    private BrandRepositoryInterface $brandRepository;

    public function __construct(BrandRepositoryInterface $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    public function execute(BrandRequestDTO $dto): BrandResponseDTO
    {
        $brandModel = new BrandModel(
            id: null,
            name: trim($dto->getName()),
            active: $dto->isActive()
        );

        $createdBrand = $this->brandRepository->create($brandModel);

        return new BrandResponseDTO(
            id: $createdBrand->getId(),
            name: $createdBrand->getName(),
            active: $createdBrand->isActive()
        );
    }
}
