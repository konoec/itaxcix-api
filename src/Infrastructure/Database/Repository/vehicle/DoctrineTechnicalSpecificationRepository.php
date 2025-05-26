<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\TechnicalSpecificationModel;
use itaxcix\Core\Interfaces\vehicle\TechnicalSpecificationRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\TechnicalSpecificationEntity;

class DoctrineTechnicalSpecificationRepository implements TechnicalSpecificationRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }
    public function toDomain(TechnicalSpecificationEntity $entity): TechnicalSpecificationModel
    {
        return new TechnicalSpecificationModel(
            $entity->getId(),
            $entity->getVehicle(),
            $entity->getDryWeight(),
            $entity->getGrossWeight(),
            $entity->getLength(),
            $entity->getHeight(),
            $entity->getWidth(),
            $entity->getPayloadCapacity()
        );
    }

    public function saveTechnicalSpecification(TechnicalSpecificationModel $technicalSpecificationModel): TechnicalSpecificationModel
    {
        $entity = new TechnicalSpecificationEntity();
        $entity->setId($technicalSpecificationModel->getId());
        $entity->setVehicle($technicalSpecificationModel->getVehicle());
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