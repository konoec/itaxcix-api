<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;

class DoctrineDistrictRepository implements DistrictRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(DistrictEntity $entity): DistrictModel
    {
        return new DistrictModel(
            id: $entity->getId(),
            name: $entity->getName(),
            province: $entity->getProvince(),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findDistrictByName(string $name): ?DistrictModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(DistrictEntity::class, 'd')
            ->where('d.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveDistrict(DistrictModel $districtModel): DistrictModel
    {
        $districtEntity = new DistrictEntity();
        $districtEntity->setName($districtModel->getName());
        $districtEntity->setUbigeo($districtModel->getUbigeo());
        $districtEntity->setProvince($districtModel->getProvince());

        $this->entityManager->persist($districtEntity);
        $this->entityManager->flush();

        return $this->toDomain($districtEntity);
    }
}