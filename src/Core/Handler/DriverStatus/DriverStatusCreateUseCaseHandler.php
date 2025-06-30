<?php

namespace itaxcix\Core\Handler\DriverStatus;

use itaxcix\Core\UseCases\DriverStatus\DriverStatusCreateUseCase;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusResponseDTO;

class DriverStatusCreateUseCaseHandler
{
    private DriverStatusCreateUseCase $useCase;

    public function __construct(DriverStatusCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DriverStatusRequestDTO $request): DriverStatusResponseDTO
    {
        return $this->useCase->execute($request);
    }
}
