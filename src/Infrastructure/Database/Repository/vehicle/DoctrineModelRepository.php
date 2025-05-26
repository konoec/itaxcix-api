<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Domain\vehicle\ModelModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ModelEntity;

class DoctrineModelRepository implements ModelRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ModelEntity $entity): ModelModel
    {
        return new ModelModel(
            id: $entity->getId(),
            name: $entity->getName(),
            brand: $entity->getBrand(),
            active: $entity->isActive()
        );
    }

    public function findAllModelByName(string $name): ?ModelModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from(ModelEntity::class, 'm')
            ->where('m.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveModel(ModelModel $modelModel): ModelModel
    {
        $entity = new ModelEntity();
        $entity->setId($modelModel->getId());
        $entity->setName($modelModel->getName());
        $entity->setBrand($modelModel->getBrand());
        $entity->setActive($modelModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}