<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\location\CoordinatesModel;
use itaxcix\Core\Interfaces\location\CoordinatesRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\CoordinatesEntity;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;

class DoctrineCoordinatesRepository implements CoordinatesRepositoryInterface
{
    private DistrictRepositoryInterface $districtRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        DistrictRepositoryInterface $districtRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->districtRepository = $districtRepository;
        $this->entityManager = $entityManager;
    }

    public function toDomain(CoordinatesEntity $entity): CoordinatesModel
    {
        return new CoordinatesModel(
            id: $entity->getId(),
            name: $entity->getName(),
            district: $this->districtRepository->toDomain($entity->getDistrict()),
            latitude: $entity->getLatitude(),
            longitude: $entity->getLongitude()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveCoordinates(CoordinatesModel $coordinatesModel): CoordinatesModel
    {
        if ($coordinatesModel->getId()) {
            $entity = $this->entityManager->find(CoordinatesEntity::class, $coordinatesModel->getId());
        } else {
            $entity = new CoordinatesEntity();
        }

        $entity->setName($coordinatesModel->getName());
        $entity->setDistrict(
            $this->entityManager->getReference(
                DistrictEntity::class,
                $coordinatesModel->getDistrict()->getId()
            )
        );
        $entity->setLatitude($coordinatesModel->getLatitude());
        $entity->setLongitude($coordinatesModel->getLongitude());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findByDistrictId(int $districtId): ?CoordinatesModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CoordinatesEntity::class, 'c')
            ->where('c.district = :districtId')
            ->setParameter('districtId', $districtId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}