<?php

namespace itaxcix\Infrastructure\Database\Repository\company;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\company\CompanyEntity;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;

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

    public function findAllCompanies(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompanyEntity::class, 'c')
            ->orderBy('c.name', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map(fn($entity) => $this->toDomain($entity), $entities);
    }

    public function findAllCompaniesPaginated(int $page, int $perPage): PaginationResponseDTO
    {
        $offset = ($page - 1) * $perPage;

        // Query para obtener el total de elementos
        $totalQuery = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(CompanyEntity::class, 'c')
            ->getQuery();

        $total = (int) $totalQuery->getSingleScalarResult();

        // Query para obtener los elementos paginados
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompanyEntity::class, 'c')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('c.ruc', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($perPage)
            ->getQuery();

        $entities = $query->getResult();
        $items = array_map(fn($entity) => $this->toDomain($entity), $entities);

        $lastPage = (int) ceil($total / $perPage);

        $meta = new PaginationMetaDTO(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );

        return new PaginationResponseDTO(items: $items, meta: $meta);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveCompany(CompanyModel $companyModel): CompanyModel
    {
        if ($companyModel->getId()) {
            $entity = $this->entityManager->find(CompanyEntity::class, $companyModel->getId());
        } else {
            $entity = new CompanyEntity();
        }

        $entity->setRuc($companyModel->getRuc());
        $entity->setName($companyModel->getName());
        $entity->setActive($companyModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findCompanyById(int $id): ?CompanyModel
    {
        $entity = $this->entityManager->find(CompanyEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function deleteCompany(int $id): bool
    {
        $entity = $this->entityManager->find(CompanyEntity::class, $id);
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }
}