<?php

namespace itaxcix\Core\UseCases\ServiceType;

use itaxcix\Core\Domain\vehicle\ServiceTypeModel;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeResponseDTO;

class ServiceTypeUpdateUseCase
{
    private ServiceTypeRepositoryInterface $repository;

    public function __construct(ServiceTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, ServiceTypeRequestDTO $request): ServiceTypeResponseDTO
    {
        $serviceType = new ServiceTypeModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updatedServiceType = $this->repository->saveServiceType($serviceType);

        return new ServiceTypeResponseDTO(
            id: $updatedServiceType->getId(),
            name: $updatedServiceType->getName(),
            active: $updatedServiceType->isActive()
        );
    }
}

