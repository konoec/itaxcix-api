<?php

namespace itaxcix\Core\UseCases\ServiceType;

use itaxcix\Core\Domain\vehicle\ServiceRouteModel;
use itaxcix\Core\Interfaces\vehicle\ServiceRouteRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;

class ServiceTypeDeleteUseCase
{
    private ServiceTypeRepositoryInterface $repository;
    private ServiceRouteRepositoryInterface $routeRepository;

    public function __construct(ServiceTypeRepositoryInterface $repository, ServiceRouteRepositoryInterface $routeRepository)
    {
        $this->repository = $repository;
        $this->routeRepository = $routeRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the service type is associated with any service routes
        $serviceRoutes = $this->routeRepository->findByServiceTypeId($id);
        if (!empty($serviceRoutes)) {
            return false;
        }

        return $this->repository->delete($id);
    }
}

