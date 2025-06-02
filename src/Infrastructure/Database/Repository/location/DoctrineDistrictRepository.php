<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;
use itaxcix\Infrastructure\Database\Entity\location\ProvinceEntity;
use RuntimeException;

class DoctrineDistrictRepository implements DistrictRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private ProvinceRepositoryInterface $provinceRepository;

    public function __construct(EntityManagerInterface $entityManager, ProvinceRepositoryInterface $provinceRepository) {
        $this->entityManager = $entityManager;
        $this->provinceRepository = $provinceRepository;
    }

    public function toDomain(DistrictEntity $entity): DistrictModel
    {
        return new DistrictModel(
            id: $entity->getId(),
            name: $entity->getName(),
            province: $this->provinceRepository->toDomain($entity->getProvince()),
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

    /**
     * @throws ORMException
     */
    public function saveDistrict(DistrictModel $districtModel): DistrictModel
    {
        if ($districtModel->getId()) {
            $entity = $this->entityManager->find(DistrictEntity::class, $districtModel->getId());
        } else {
            $entity = new DistrictEntity();
        }

        $entity->setName($districtModel->getName());
        $entity->setUbigeo($districtModel->getUbigeo());
        $entity->setProvince(
            $this->entityManager->getReference(
                ProvinceEntity::class,
                $districtModel->getProvince()->getId()
            )
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}