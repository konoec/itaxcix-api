<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\TucModalityModel;
use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucModalityEntity;

class DoctrineTucModalityRepository implements TucModalityRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(TucModalityEntity $entity): TucModalityModel
    {
        return new TucModalityModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllTucModalityByName(string $name): ?TucModalityModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('t')
            ->from(TucModalityEntity::class, 't')
            ->where('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveTucModality(TucModalityModel $brandModel): TucModalityModel
    {
        if ($brandModel->getId()) {
            $entity = $this->entityManager->find(TucModalityEntity::class, $brandModel->getId());
        } else {
            $entity = new TucModalityEntity();
        }

        $entity->setName($brandModel->getName());
        $entity->setActive($brandModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}