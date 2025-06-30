<?php

namespace itaxcix\Core\Handler\ServiceType;

use itaxcix\Core\UseCases\ServiceType\ServiceTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeResponseDTO;

class ServiceTypeCreateUseCaseHandler
{
    private ServiceTypeCreateUseCase $useCase;

    public function __construct(ServiceTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ServiceTypeRequestDTO $request): ServiceTypeResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

