<?php

namespace itaxcix\Core\Handler\VehicleClass;

use itaxcix\Core\UseCases\VehicleClass\VehicleClassListUseCase;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassPaginationRequestDTO;

class VehicleClassListUseCaseHandler
{
    private VehicleClassListUseCase $useCase;

    public function __construct(VehicleClassListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(VehicleClassPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
