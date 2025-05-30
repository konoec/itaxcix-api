<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\VehicleModel;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;

class DoctrineVehicleRepository implements VehicleRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(VehicleEntity $entity): VehicleModel {
        return new VehicleModel(
            $entity->getId(),
            $entity->getLicensePlate(),
            $entity->getModel(),
            $entity->getColor(),
            $entity->getManufactureYear(),
            $entity->getSeatCount(),
            $entity->getPassengerCount(),
            $entity->getFuelType(),
            $entity->getVehicleClass(),
            $entity->getCategory(),
            $entity->isActive()
        );
    }

    public function findAllVehicleByPlate(string $plate): ?VehicleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.licensePlate = :plate')
            ->setParameter('plate', $plate)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveVehicle(VehicleModel $vehicleModel): VehicleModel
    {
        $entity = new VehicleEntity();
        $entity->setLicensePlate($vehicleModel->getLicensePlate());
        $entity->setModel($vehicleModel->getModel());
        $entity->setColor($vehicleModel->getColor());
        $entity->setManufactureYear($vehicleModel->getManufactureYear());
        $entity->setSeatCount($vehicleModel->getSeatCount());
        $entity->setPassengerCount($vehicleModel->getPassengerCount());
        $entity->setFuelType($vehicleModel->getFuelType());
        $entity->setVehicleClass($vehicleModel->getVehicleClass());
        $entity->setCategory($vehicleModel->getCategory());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAllVehicleById(int $id): ?VehicleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findVehicleById(int $id): ?VehicleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}