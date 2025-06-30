<?php

namespace itaxcix\Core\Handler\DriverStatus;

use itaxcix\Core\UseCases\DriverStatus\DriverStatusListUseCase;
use itaxcix\Shared\DTO\useCases\DriverStatus\DriverStatusPaginationRequestDTO;

class DriverStatusListUseCaseHandler
{
    private DriverStatusListUseCase $useCase;

    public function __construct(DriverStatusListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(DriverStatusPaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}
