<?php

namespace itaxcix\Core\Handler\DriverStatus;

use itaxcix\Core\UseCases\DriverStatus\DriverStatusUpdateUseCase;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusResponseDTO;

class DriverStatusUpdateUseCaseHandler
{
    private DriverStatusUpdateUseCase $useCase;

    public function __construct(DriverStatusUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DriverStatusRequestDTO $request): DriverStatusResponseDTO
    {
        return $this->useCase->execute($request);
    }
}
