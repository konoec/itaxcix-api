<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\location\DepartmentModel;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DepartmentEntity;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;

class DoctrineDepartmentRepository implements DepartmentRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DepartmentEntity $entity): DepartmentModel
    {
        return new DepartmentModel(
            id: $entity->getId(),
            name: $entity->getName(),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findDepartmentByName(string $name): ?DepartmentModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(DepartmentEntity::class, 'd')
            ->where('d.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveDepartment(DepartmentModel $departmentModel): DepartmentModel
    {
        if ($departmentModel->getId()) {
            $entity = $this->entityManager->find(DepartmentEntity::class, $departmentModel->getId());
        } else {
            $entity = new DepartmentEntity();
        }

        $entity->setName($departmentModel->getName());
        $entity->setUbigeo($departmentModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}