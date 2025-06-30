<?php

namespace itaxcix\Core\Handler\VehicleClass;

use itaxcix\Core\UseCases\VehicleClass\VehicleClassUpdateUseCase;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassResponseDTO;

class VehicleClassUpdateUseCaseHandler
{
    private VehicleClassUpdateUseCase $useCase;

    public function __construct(VehicleClassUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, VehicleClassRequestDTO $request): VehicleClassResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}
