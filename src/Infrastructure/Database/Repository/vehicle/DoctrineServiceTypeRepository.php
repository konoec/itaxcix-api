<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\ServiceTypeModel;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ServiceTypeEntity;

class DoctrineServiceTypeRepository implements ServiceTypeRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ServiceTypeEntity $entity): ServiceTypeModel
    {
        return new ServiceTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllServiceTypeByName(string $name): ?ServiceTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('s')
            ->from(ServiceTypeEntity::class, 's')
            ->where('s.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveServiceType(ServiceTypeModel $serviceTypeModel): ServiceTypeModel
    {
        if ($serviceTypeModel->getId()) {
            $entity = $this->entityManager->find(ServiceTypeEntity::class, $serviceTypeModel->getId());
        } else {
            $entity = new ServiceTypeEntity();
        }

        $entity->setName($serviceTypeModel->getName());
        $entity->setActive($serviceTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}