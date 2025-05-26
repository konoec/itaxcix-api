<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\location\ProvinceModel;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\ProvinceEntity;

class DoctrineProvinceRepository implements ProvinceRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ProvinceEntity $entity): ProvinceModel
    {
        return new ProvinceModel(
            id: $entity->getId(),
            name: $entity->getName(),
            department: $entity->getDepartment(),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findProvinceByName(string $name): ?ProvinceModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(ProvinceEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveProvince(ProvinceModel $provinceModel): ProvinceModel
    {
        $entity = new ProvinceEntity();
        $entity->setName($provinceModel->getName());
        $entity->setDepartment($provinceModel->getDepartment());
        $entity->setUbigeo($provinceModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}