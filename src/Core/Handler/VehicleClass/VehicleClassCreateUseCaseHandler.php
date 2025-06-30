<?php

namespace itaxcix\Core\Handler\VehicleClass;

use itaxcix\Core\UseCases\VehicleClass\VehicleClassCreateUseCase;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassResponseDTO;

class VehicleClassCreateUseCaseHandler
{
    private VehicleClassCreateUseCase $useCase;

    public function __construct(VehicleClassCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(VehicleClassRequestDTO $request): VehicleClassResponseDTO
    {
        return $this->useCase->execute($request);
    }
}
