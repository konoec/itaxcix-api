<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\vehicle\ModelModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ModelEntity;

class DoctrineModelRepository implements ModelRepositoryInterface {

    private EntityManagerInterface $entityManager;
    private BrandRepositoryInterface $brandRepository;

    public function __construct(EntityManagerInterface $entityManager, BrandRepositoryInterface $brandRepository) {
        $this->entityManager = $entityManager;
        $this->brandRepository = $brandRepository;
    }

    public function toDomain(ModelEntity $entity): ModelModel
    {
        return new ModelModel(
            id: $entity->getId(),
            name: $entity->getName(),
            brand: $this->brandRepository->toDomain($entity->getBrand()),
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

    /**
     * @throws ORMException
     */
    public function saveModel(ModelModel $modelModel): ModelModel
    {
        if ($modelModel->getId()) {
            $entity = $this->entityManager->find(ModelEntity::class, $modelModel->getId());
        } else {
            $entity = new ModelEntity();
        }

        $entity->setName($modelModel->getName());
        $entity->setBrand(
            $this->entityManager->getReference(
                BrandEntity::class, $modelModel->getBrand()->getId()
            )
        );
        $entity->setActive($modelModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}