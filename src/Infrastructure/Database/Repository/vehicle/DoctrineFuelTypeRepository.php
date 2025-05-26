<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\FuelTypeModel;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\FuelTypeEntity;

class DoctrineFuelTypeRepository implements FuelTypeRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(FuelTypeEntity $entity): FuelTypeModel
    {
        return new FuelTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllFuelTypeByName(string $name): ?FuelTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('f')
            ->from(FuelTypeEntity::class, 'f')
            ->where('f.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveFuelType(FuelTypeModel $fuelTypeModel): FuelTypeModel
    {
        $entity = new FuelTypeEntity();
        $entity->setId($fuelTypeModel->getId());
        $entity->setName($fuelTypeModel->getName());
        $entity->setActive($fuelTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}