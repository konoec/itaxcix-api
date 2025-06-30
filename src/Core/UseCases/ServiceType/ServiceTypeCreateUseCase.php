<?php

namespace itaxcix\Core\UseCases\ServiceType;

use itaxcix\Core\Domain\vehicle\ServiceTypeModel;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ServiceType\ServiceTypeResponseDTO;

class ServiceTypeCreateUseCase
{
    private ServiceTypeRepositoryInterface $repository;

    public function __construct(ServiceTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ServiceTypeRequestDTO $request): ServiceTypeResponseDTO
    {
        $serviceType = new ServiceTypeModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $savedServiceType = $this->repository->saveServiceType($serviceType);

        return new ServiceTypeResponseDTO(
            id: $savedServiceType->getId(),
            name: $savedServiceType->getName(),
            active: $savedServiceType->isActive()
        );
    }
}

