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
use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;

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

    /**
     * Busca empresas con filtros avanzados, búsqueda y paginación
     */
    public function findCompaniesPaginatedWithFilters(CompanyPaginationRequestDTO $dto): PaginationResponseDTO
    {
        $offset = ($dto->getPage() - 1) * $dto->getPerPage();

        // Query builder base
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(CompanyEntity::class, 'c');

        $countQb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(CompanyEntity::class, 'c');

        // Aplicar filtros
        $this->applyFilters($qb, $dto);
        $this->applyFilters($countQb, $dto);

        // Aplicar búsqueda global
        if ($dto->getSearch() !== null && trim($dto->getSearch()) !== '') {
            $searchTerm = '%' . strtolower(trim($dto->getSearch())) . '%';

            $searchCondition = $qb->expr()->orX(
                $qb->expr()->like('LOWER(c.ruc)', ':search'),
                $qb->expr()->like('LOWER(c.name)', ':search')
            );

            $qb->andWhere($searchCondition)->setParameter('search', $searchTerm);
            $countQb->andWhere($searchCondition)->setParameter('search', $searchTerm);
        }

        // Aplicar ordenamiento
        $this->applySorting($qb, $dto);

        // Obtener total de elementos
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        // Aplicar paginación y obtener resultados
        $query = $qb->setFirstResult($offset)
            ->setMaxResults($dto->getPerPage())
            ->getQuery();

        $entities = $query->getResult();
        $items = array_map(fn($entity) => $this->toDomain($entity), $entities);

        $lastPage = (int) ceil($total / $dto->getPerPage());

        $meta = new PaginationMetaDTO(
            total: $total,
            perPage: $dto->getPerPage(),
            currentPage: $dto->getPage(),
            lastPage: $lastPage
        );

        // Agregar metadatos adicionales usando propiedades públicas
        $meta->search = $dto->getSearch();
        $meta->filters = $dto->getFilters();
        $meta->sortBy = $dto->getSortBy();
        $meta->sortDirection = $dto->getSortDirection();

        return new PaginationResponseDTO(items: $items, meta: $meta);
    }

    /**
     * Aplica filtros específicos al QueryBuilder
     */
    private function applyFilters($qb, CompanyPaginationRequestDTO $dto): void
    {
        $filters = $dto->getFilters();

        // Filtro por RUC exacto
        if (isset($filters['ruc'])) {
            $qb->andWhere('c.ruc = :ruc')
               ->setParameter('ruc', $filters['ruc']);
        }

        // Filtro por nombre (contiene)
        if (isset($filters['name'])) {
            $qb->andWhere('LOWER(c.name) LIKE :name')
               ->setParameter('name', '%' . strtolower($filters['name']) . '%');
        }

        // Filtro por estado activo
        if (isset($filters['active'])) {
            $qb->andWhere('c.active = :active')
               ->setParameter('active', $filters['active']);
        }

        // Filtros de fecha (asumiendo que la entidad tiene campos de fecha)
        if (isset($filters['createdFrom'])) {
            $qb->andWhere('DATE(c.createdAt) >= :createdFrom')
               ->setParameter('createdFrom', $filters['createdFrom']);
        }

        if (isset($filters['createdTo'])) {
            $qb->andWhere('DATE(c.createdAt) <= :createdTo')
               ->setParameter('createdTo', $filters['createdTo']);
        }
    }

    /**
     * Aplica ordenamiento al QueryBuilder
     */
    private function applySorting($qb, CompanyPaginationRequestDTO $dto): void
    {
        $sortBy = $dto->getSortBy();
        $sortDirection = strtoupper($dto->getSortDirection());

        // Mapear campos del DTO a campos de la entidad
        $fieldMapping = [
            'id' => 'c.id',
            'ruc' => 'c.ruc',
            'name' => 'c.name',
            'active' => 'c.active',
            'created_at' => 'c.createdAt'
        ];

        if (isset($fieldMapping[$sortBy])) {
            $qb->orderBy($fieldMapping[$sortBy], $sortDirection);

            // Ordenamiento secundario para consistencia
            if ($sortBy !== 'id') {
                $qb->addOrderBy('c.id', 'ASC');
            }
        } else {
            // Ordenamiento por defecto
            $qb->orderBy('c.name', 'ASC')
               ->addOrderBy('c.id', 'ASC');
        }
    }

    // Implementación de los métodos requeridos por la interfaz
    public function findAll(CompanyPaginationRequestDTO $request): array
    {
        return $this->findCompaniesPaginatedWithFilters($request)->items;
    }

    public function findById(int $id): ?CompanyModel
    {
        return $this->findCompanyById($id);
    }

    public function create(CompanyModel $company): CompanyModel
    {
        return $this->saveCompany($company);
    }

    public function update(CompanyModel $company): CompanyModel
    {
        return $this->saveCompany($company);
    }

    public function delete(int $id): bool
    {
        return $this->deleteCompany($id);
    }

    public function existsByRuc(string $ruc, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(CompanyEntity::class, 'c')
            ->where('c.ruc = :ruc')
            ->setParameter('ruc', $ruc);

        if ($excludeId !== null) {
            $qb->andWhere('c.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function count(CompanyPaginationRequestDTO $request): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from(CompanyEntity::class, 'c');

        // Aplicar los mismos filtros que en findAll
        $this->applyFilters($qb, $request);

        // Aplicar búsqueda global si existe
        if ($request->getSearch() !== null && trim($request->getSearch()) !== '') {
            $searchTerm = '%' . strtolower(trim($request->getSearch())) . '%';

            $searchCondition = $qb->expr()->orX(
                $qb->expr()->like('LOWER(c.ruc)', ':search'),
                $qb->expr()->like('LOWER(c.name)', ':search')
            );

            $qb->andWhere($searchCondition)->setParameter('search', $searchTerm);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}

