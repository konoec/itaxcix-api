<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\BrandModel;
use itaxcix\Core\Interfaces\vehicle\BrandRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\BrandEntity;

class DoctrineBrandRepository implements BrandRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(BrandEntity $entity): BrandModel
    {
        return new BrandModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllBrandByName(string $name): ?BrandModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('b')
            ->from(BrandEntity::class, 'b')
            ->where('b.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveBrand(BrandModel $brandModel): BrandModel
    {
        if ($brandModel->getId()) {
            $entity = $this->entityManager->find(BrandEntity::class, $brandModel->getId());
        } else {
            $entity = new BrandEntity();
        }

        $entity->setName($brandModel->getName());
        $entity->setActive($brandModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}