<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Domain\vehicle\ServiceRouteModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ServiceRouteRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ServiceRouteEntity;

class DoctrineServiceRouteRepository implements ServiceRouteRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ServiceRouteEntity $entity): ServiceRouteModel
    {
        return new ServiceRouteModel(
            id: $entity->getId(),
            procedure: $entity->getProcedure(),
            serviceType: $entity->getServiceType(),
            text: $entity->getText(),
            active: $entity->isActive()
        );
    }

    public function saveServiceRoute(ServiceRouteModel $serviceRouteModel): ServiceRouteModel
    {
        $entity = new ServiceRouteEntity();
        $entity->setId($serviceRouteModel->getId());
        $entity->setProcedure($serviceRouteModel->getProcedure());
        $entity->setServiceType($serviceRouteModel->getServiceType());
        $entity->setText($serviceRouteModel->getText());
        $entity->setActive($serviceRouteModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}