<?php

namespace itaxcix\Infrastructure\Database\Repository\configuration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\QueryBuilder;
use itaxcix\Core\Domain\configuration\ConfigurationModel;
use itaxcix\Core\Interfaces\configuration\ConfigurationRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\configuration\ConfigurationEntity;
use itaxcix\Shared\DTO\useCases\Configuration\ConfigurationPaginationRequestDTO;

class DoctrineConfigurationRepository implements ConfigurationRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(ConfigurationEntity $entity): ConfigurationModel
    {
        return new ConfigurationModel(
            id: $entity->getId(),
            key: $entity->getKey(),
            value: $entity->getValue(),
            active: $entity->isActive()
        );
    }

    public function findConfigurationByKey(string $key): ?ConfigurationModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(ConfigurationEntity::class, 'c')
            ->where('c.key = :key')
            ->andWhere('c.active = true')
            ->setParameter('key', $key)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveConfiguration(ConfigurationModel $configurationModel): ConfigurationModel
    {
        if ($configurationModel->getId()) {
            $entity = $this->entityManager->find(ConfigurationEntity::class, $configurationModel->getId());
        } else {
            $entity = new ConfigurationEntity();
        }

        $entity->setKey($configurationModel->getKey());
        $entity->setValue($configurationModel->getValue());
        $entity->setActive($configurationModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(ConfigurationPaginationRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')
            ->from(ConfigurationEntity::class, 'c');

        // Aplicar filtros
        $this->applyFilters($qb, $dto);

        // Aplicar ordenamiento
        $qb->orderBy('c.' . $dto->getSortBy(), $dto->getSortDirection());

        // Calcular total de registros
        $totalQb = clone $qb;
        $totalQb->select('COUNT(c.id)');
        $total = (int) $totalQb->getQuery()->getSingleScalarResult();

        // Aplicar paginación
        $offset = ($dto->getPage() - 1) * $dto->getPerPage();
        $qb->setFirstResult($offset)
            ->setMaxResults($dto->getPerPage());

        $entities = $qb->getQuery()->getResult();

        // Convertir entidades a modelos de dominio
        $items = array_map(fn($entity) => $this->toDomain($entity), $entities);

        return [
            'items' => $items,
            'meta' => [
                'total' => $total,
                'perPage' => $dto->getPerPage(),
                'currentPage' => $dto->getPage(),
                'lastPage' => (int) ceil($total / $dto->getPerPage()),
                'search' => $dto->getSearch(),
                'filters' => $this->getAppliedFilters($dto),
                'sortBy' => $dto->getSortBy(),
                'sortDirection' => $dto->getSortDirection()
            ]
        ];
    }

    public function findById(int $id): ?ConfigurationModel
    {
        $entity = $this->entityManager->find(ConfigurationEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findByKey(string $key): ?ConfigurationModel
    {
        $entity = $this->entityManager->getRepository(ConfigurationEntity::class)
            ->findOneBy(['key' => $key]);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(ConfigurationModel $configuration): ConfigurationModel
    {
        $entity = new ConfigurationEntity();
        $entity->setKey($configuration->getKey());
        $entity->setValue($configuration->getValue());
        $entity->setActive($configuration->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(ConfigurationModel $configuration): ConfigurationModel
    {
        $entity = $this->entityManager->find(ConfigurationEntity::class, $configuration->getId());
        if (!$entity) {
            throw new \InvalidArgumentException('Configuration not found');
        }

        $entity->setKey($configuration->getKey());
        $entity->setValue($configuration->getValue());
        $entity->setActive($configuration->isActive());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(ConfigurationEntity::class, $id);
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->flush();
            return true;
        }
        return false;
    }

    public function existsByKey(string $key, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('COUNT(c.id)')
            ->from(ConfigurationEntity::class, 'c')
            ->where('c.key = :key')
            ->setParameter('key', $key);

        if ($excludeId !== null) {
            $qb->andWhere('c.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    private function applyFilters(QueryBuilder $qb, ConfigurationPaginationRequestDTO $dto): void
    {
        // Búsqueda global
        if ($dto->getSearch()) {
            $qb->andWhere('(c.key LIKE :search OR c.value LIKE :search)')
                ->setParameter('search', '%' . $dto->getSearch() . '%');
        }

        // Filtro por clave
        if ($dto->getKey()) {
            $qb->andWhere('c.key LIKE :key')
                ->setParameter('key', '%' . $dto->getKey() . '%');
        }

        // Filtro por valor
        if ($dto->getValue()) {
            $qb->andWhere('c.value LIKE :value')
                ->setParameter('value', '%' . $dto->getValue() . '%');
        }

        // Filtro por estado activo
        if ($dto->getActive() !== null) {
            $qb->andWhere('c.active = :active')
                ->setParameter('active', $dto->getActive());
        }

        // Solo activos
        if ($dto->getOnlyActive()) {
            $qb->andWhere('c.active = true');
        }
    }

    private function getAppliedFilters(ConfigurationPaginationRequestDTO $dto): array
    {
        $filters = [];

        if ($dto->getKey()) $filters['key'] = $dto->getKey();
        if ($dto->getValue()) $filters['value'] = $dto->getValue();
        if ($dto->getActive() !== null) $filters['active'] = $dto->getActive();
        if ($dto->getOnlyActive()) $filters['onlyActive'] = true;

        return $filters;
    }
}
