<?php

namespace itaxcix\Core\UseCases\FuelType;

use itaxcix\Core\Domain\vehicle\FuelTypeModel;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeResponseDTO;

class FuelTypeCreateUseCase
{
    private FuelTypeRepositoryInterface $fuelTypeRepository;

    public function __construct(FuelTypeRepositoryInterface $fuelTypeRepository)
    {
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function execute(FuelTypeRequestDTO $request): FuelTypeResponseDTO
    {
        $fuelTypeModel = new FuelTypeModel(
            id: null,
            name: trim($request->name),
            active: $request->active
        );

        $createdFuelType = $this->fuelTypeRepository->create($fuelTypeModel);

        return FuelTypeResponseDTO::fromModel($createdFuelType);
    }
}
