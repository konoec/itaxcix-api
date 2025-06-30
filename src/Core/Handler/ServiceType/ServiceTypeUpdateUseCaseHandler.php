<?php

namespace itaxcix\Core\Handler\ServiceType;

use itaxcix\Core\UseCases\ServiceType\ServiceTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeResponseDTO;

class ServiceTypeUpdateUseCaseHandler
{
    private ServiceTypeUpdateUseCase $useCase;

    public function __construct(ServiceTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, ServiceTypeRequestDTO $request): ServiceTypeResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}

