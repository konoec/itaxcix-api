<?php

namespace itaxcix\Core\UseCases\FuelType;

use itaxcix\Core\Domain\vehicle\FuelTypeModel;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\FuelType\FuelTypeResponseDTO;

class FuelTypeUpdateUseCase
{
    private FuelTypeRepositoryInterface $fuelTypeRepository;

    public function __construct(FuelTypeRepositoryInterface $fuelTypeRepository)
    {
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function execute(int $id, FuelTypeRequestDTO $request): FuelTypeResponseDTO
    {
        $fuelTypeModel = new FuelTypeModel(
            id: $id,
            name: trim($request->name),
            active: $request->active
        );

        $updatedFuelType = $this->fuelTypeRepository->update($fuelTypeModel);

        return FuelTypeResponseDTO::fromModel($updatedFuelType);
    }
}
