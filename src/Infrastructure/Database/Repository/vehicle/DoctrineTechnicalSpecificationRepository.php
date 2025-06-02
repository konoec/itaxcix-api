<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\vehicle\TechnicalSpecificationModel;
use itaxcix\Core\Interfaces\vehicle\TechnicalSpecificationRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\TechnicalSpecificationEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;

class DoctrineTechnicalSpecificationRepository implements TechnicalSpecificationRepositoryInterface {

    private EntityManagerInterface $entityManager;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(EntityManagerInterface $entityManager, VehicleRepositoryInterface $vehicleRepository) {
        $this->entityManager = $entityManager;
        $this->vehicleRepository = $vehicleRepository;
    }
    public function toDomain(TechnicalSpecificationEntity $entity): TechnicalSpecificationModel
    {
        return new TechnicalSpecificationModel(
            id: $entity->getId(),
            vehicle: $this->vehicleRepository->toDomain($entity->getVehicle()),
            dryWeight: $entity->getDryWeight(),
            grossWeight: $entity->getGrossWeight(),
            length: $entity->getLength(),
            height: $entity->getHeight(),
            width: $entity->getWidth(),
            payloadCapacity: $entity->getPayloadCapacity()
        );
    }

    /**
     * @throws ORMException
     */
    public function saveTechnicalSpecification(TechnicalSpecificationModel $technicalSpecificationModel): TechnicalSpecificationModel
    {
        if ($technicalSpecificationModel->getId()) {
            $entity = $this->entityManager->find(TechnicalSpecificationEntity::class, $technicalSpecificationModel->getId());
        } else {
            $entity = new TechnicalSpecificationEntity();
        }

        $entity->setVehicle(
            $this->entityManager->getReference(
                VehicleEntity::class, $technicalSpecificationModel->getVehicle()->getId()
            )
        );
        $entity->setDryWeight($technicalSpecificationModel->getDryWeight());
        $entity->setGrossWeight($technicalSpecificationModel->getGrossWeight());
        $entity->setLength($technicalSpecificationModel->getLength());
        $entity->setHeight($technicalSpecificationModel->getHeight());
        $entity->setWidth($technicalSpecificationModel->getWidth());
        $entity->setPayloadCapacity($technicalSpecificationModel->getPayloadCapacity());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}