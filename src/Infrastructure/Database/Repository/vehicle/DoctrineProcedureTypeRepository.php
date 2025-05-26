<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ProcedureTypeEntity;

class DoctrineProcedureTypeRepository implements ProcedureTypeRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ProcedureTypeEntity $entity): ProcedureTypeModel
    {
        return new ProcedureTypeModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findAllProcedureTypeByName(string $name): ?ProcedureTypeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(ProcedureTypeEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveProcedureType(ProcedureTypeModel $procedureTypeModel): ProcedureTypeModel
    {
        $entity = new ProcedureTypeEntity();
        $entity->setId($procedureTypeModel->getId());
        $entity->setName($procedureTypeModel->getName());
        $entity->setActive($procedureTypeModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}