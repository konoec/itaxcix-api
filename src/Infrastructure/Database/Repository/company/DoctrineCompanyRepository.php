<?php

namespace itaxcix\Infrastructure\Database\Repository\company;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\company\CompanyEntity;

class DoctrineCompanyRepository implements CompanyRepositoryInterface {

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(CompanyEntity $entity): CompanyModel
    {
        return new CompanyModel(
            id: $entity->getId(),
            ruc: $entity->getRuc(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findCompanyByRuc(string $ruc): ?CompanyModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompanyEntity::class, 'c')
            ->where('c.ruc = :ruc')
            ->setParameter('ruc', $ruc)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveCompany(CompanyModel $companyModel): CompanyModel
    {
        $entity = new CompanyEntity();
        $entity->setId($companyModel->getId());
        $entity->setRuc($companyModel->getRuc());
        $entity->setName($companyModel->getName());
        $entity->setActive($companyModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}