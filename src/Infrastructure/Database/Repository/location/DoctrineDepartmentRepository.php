<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
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

    public function saveDepartment(DepartmentModel $departmentModel): DepartmentModel
    {
        $departmentEntity = new DepartmentEntity();
        $departmentEntity->setName($departmentModel->getName());
        $departmentEntity->setUbigeo($departmentModel->getUbigeo());

        $this->entityManager->persist($departmentEntity);
        $this->entityManager->flush();

        return $this->toDomain($departmentEntity);
    }
}