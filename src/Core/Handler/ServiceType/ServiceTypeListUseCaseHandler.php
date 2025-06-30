<?php

namespace itaxcix\Core\Handler\ServiceType;

use itaxcix\Core\UseCases\ServiceType\ServiceTypeListUseCase;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypePaginationRequestDTO;

class ServiceTypeListUseCaseHandler
{
    private ServiceTypeListUseCase $useCase;

    public function __construct(ServiceTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ServiceTypePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

