<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\VehicleUserModel;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleUserEntity;

class DoctrineVehicleUserRepository implements VehicleUserRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(VehicleUserEntity $entity): VehicleUserModel {
        return new VehicleUserModel(
            id: $entity->getId(),
            user: $entity->getUser(),
            vehicle: $entity->getVehicle(),
            active: $entity->isActive()
        );
    }

    public function findVehicleUserByVehicleId(int $vehicleId): ?VehicleUserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vu')
            ->from(VehicleUserEntity::class, 'vu')
            ->innerJoin('vu.user', 'u')
            ->innerJoin('vu.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveVehicleUser(VehicleUserModel $vehicleUserModel): VehicleUserModel
    {
        $entity = new VehicleUserEntity();
        $entity->setUser($vehicleUserModel->getUser());
        $entity->setVehicle($vehicleUserModel->getVehicle());
        $entity->setActive($vehicleUserModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}