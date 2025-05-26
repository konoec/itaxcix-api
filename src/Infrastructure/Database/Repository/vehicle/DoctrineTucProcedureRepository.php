<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\TucProcedureModel;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucProcedureEntity;

class DoctrineTucProcedureRepository implements TucProcedureRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(TucProcedureEntity $entity): TucProcedureModel
    {
        return new TucProcedureModel(
            id: $entity->getId(),
            vehicle: $entity->getVehicle(),
            company: $entity->getCompany(),
            district: $entity->getDistrict(),
            status: $entity->getStatus(),
            type: $entity->getType(),
            modality:  $entity->getModality(),
            procedureDate: $entity->getProcedureDate(),
            issueDate: $entity->getProcedureDate(),
            expirationDate: $entity->getExpirationDate()
        );
    }

    public function saveTucProcedure(TucProcedureModel $tucProcedureModel): TucProcedureModel
    {
        $entity = new TucProcedureEntity();
        $entity->setId($tucProcedureModel->getId());
        $entity->setVehicle($tucProcedureModel->getVehicle());
        $entity->setCompany($tucProcedureModel->getCompany());
        $entity->setDistrict($tucProcedureModel->getDistrict());
        $entity->setStatus($tucProcedureModel->getStatus());
        $entity->setType($tucProcedureModel->getType());
        $entity->setModality($tucProcedureModel->getModality());
        $entity->setProcedureDate($tucProcedureModel->getProcedureDate());
        $entity->setIssueDate($tucProcedureModel->getIssueDate());
        $entity->setExpirationDate($tucProcedureModel->getExpirationDate());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}