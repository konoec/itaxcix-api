<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\VehicleClassModel;
use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleClassEntity;

class DoctrineVehicleClassRepository implements VehicleClassRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(VehicleClassEntity $entity): VehicleClassModel
    {
        return new VehicleClassModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllVehicleClassByName(string $name): ?VehicleClassModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleClassEntity::class, 'v')
            ->where('v.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveVehicleClass(VehicleClassModel $vehicleClassModel): VehicleClassModel
    {
        if ($vehicleClassModel->getId()){
            $entity = $this->entityManager->find(VehicleClassEntity::class, $vehicleClassModel->getId());
        } else {
            $entity = new VehicleClassEntity();
        }

        $entity->setName($vehicleClassModel->getName());
        $entity->setActive($vehicleClassModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}