<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\VehicleCategoryModel;
use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleCategoryEntity;

class DoctrineVehicleCategoryRepository implements VehicleCategoryRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    public function toDomain(VehicleCategoryEntity $entity): VehicleCategoryModel
    {
        return new VehicleCategoryModel(
            $entity->getId(),
            $entity->getName(),
            $entity->isActive()
        );
    }

    public function findAllVehicleCategoryByName(string $name): ?VehicleCategoryModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vc')
            ->from(VehicleCategoryEntity::class, 'vc')
            ->where('vc.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveVehicleCategory(VehicleCategoryModel $vehicleCategoryModel): VehicleCategoryModel
    {
        if ($vehicleCategoryModel->getId()) {
            $entity = $this->entityManager->find(VehicleCategoryEntity::class, $vehicleCategoryModel->getId());
        } else {
            $entity = new VehicleCategoryEntity();
        }

        $entity->setName($vehicleCategoryModel->getName());
        $entity->setActive($vehicleCategoryModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}