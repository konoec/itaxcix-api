<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\ColorModel;
use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ColorEntity;

class DoctrineColorRepository implements ColorRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ColorEntity $entity): ColorModel
    {
        return new ColorModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllColorByName(string $name): ?ColorModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(ColorEntity::class, 'c')
            ->where('c.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveColor(ColorModel $colorModel): ColorModel
    {
        if ($colorModel->getId()) {
            $entity = $this->entityManager->find(ColorEntity::class, $colorModel->getId());
        } else {
            $entity = new ColorEntity();
        }

        $entity->setName($colorModel->getName());
        $entity->setActive($colorModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}