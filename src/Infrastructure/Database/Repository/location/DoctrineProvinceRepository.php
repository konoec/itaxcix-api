<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\location\ProvinceModel;
use itaxcix\Core\Interfaces\location\DepartmentRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DepartmentEntity;
use itaxcix\Infrastructure\Database\Entity\location\ProvinceEntity;

class DoctrineProvinceRepository implements ProvinceRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private DepartmentRepositoryInterface $departmentRepository;

    public function __construct(EntityManagerInterface $entityManager, DepartmentRepositoryInterface $departmentRepository){
        $this->entityManager = $entityManager;
        $this->departmentRepository = $departmentRepository;
    }

    public function toDomain(ProvinceEntity $entity): ProvinceModel
    {
        return new ProvinceModel(
            id: $entity->getId(),
            name: $entity->getName(),
            department: $this->departmentRepository->toDomain($entity->getDepartment()),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findProvinceByName(string $name): ?ProvinceModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(ProvinceEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function saveProvince(ProvinceModel $provinceModel): ProvinceModel
    {
        if ($provinceModel->getId()) {
            $entity = $this->entityManager->find(ProvinceEntity::class, $provinceModel->getId());
        } else {
            $entity = new ProvinceEntity();
        }

        $entity->setName($provinceModel->getName());
        $entity->setDepartment(
            $this->entityManager->getReference(
                DepartmentEntity::class, $provinceModel->getDepartment()->getId()
            )
        );
        $entity->setUbigeo($provinceModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(int $page = 1, int $perPage = 15, array $filters = [], ?string $search = null, string $sortBy = 'name', string $sortOrder = 'ASC'): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('p, d')
            ->from(ProvinceEntity::class, 'p')
            ->leftJoin('p.department', 'd')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        // Aplicar búsqueda global
        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.ubigeo LIKE :search OR d.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Aplicar filtros específicos
        if (isset($filters['name'])) {
            $qb->andWhere('p.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['departmentId'])) {
            $qb->andWhere('d.id = :departmentId')
               ->setParameter('departmentId', $filters['departmentId']);
        }

        if (isset($filters['ubigeo'])) {
            $qb->andWhere('p.ubigeo LIKE :ubigeo')
               ->setParameter('ubigeo', '%' . $filters['ubigeo'] . '%');
        }

        // Aplicar ordenamiento
        $validSortFields = ['id' => 'p.id', 'name' => 'p.name', 'ubigeo' => 'p.ubigeo'];
        $sortField = $validSortFields[$sortBy] ?? 'p.name';
        $qb->orderBy($sortField, $sortOrder);

        $entities = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function findById(int $id): ?ProvinceModel
    {
        $entity = $this->entityManager->createQueryBuilder()
            ->select('p, d')
            ->from(ProvinceEntity::class, 'p')
            ->leftJoin('p.department', 'd')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(ProvinceModel $provinceModel): ProvinceModel
    {
        $entity = new ProvinceEntity();
        $entity->setName($provinceModel->getName());
        $entity->setDepartment(
            $this->entityManager->getReference(
                DepartmentEntity::class, $provinceModel->getDepartment()->getId()
            )
        );
        $entity->setUbigeo($provinceModel->getUbigeo());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(ProvinceModel $provinceModel): ProvinceModel
    {
        $entity = $this->entityManager->find(ProvinceEntity::class, $provinceModel->getId());
        if (!$entity) {
            throw new \InvalidArgumentException('Provincia no encontrada');
        }

        $entity->setName($provinceModel->getName());
        $entity->setDepartment(
            $this->entityManager->getReference(
                DepartmentEntity::class, $provinceModel->getDepartment()->getId()
            )
        );
        $entity->setUbigeo($provinceModel->getUbigeo());

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(ProvinceEntity::class, $id);
        if (!$entity) {
            return false;
        }

        $entity->setActive(false);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return true;
    }

    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(ProvinceEntity::class, 'p')
            ->where('p.name = :name')
            ->setParameter('name', $name);

        if ($excludeId) {
            $qb->andWhere('p.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function existsByUbigeo(string $ubigeo, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(ProvinceEntity::class, 'p')
            ->where('p.ubigeo = :ubigeo')
            ->setParameter('ubigeo', $ubigeo);

        if ($excludeId) {
            $qb->andWhere('p.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function findByDepartmentId(int $departmentId): array
    {
        $entities = $this->entityManager->createQueryBuilder()
            ->select('p, d')
            ->from(ProvinceEntity::class, 'p')
            ->leftJoin('p.department', 'd')
            ->where('d.id = :departmentId')
            ->setParameter('departmentId', $departmentId)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function countTotal(array $filters = [], ?string $search = null): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from(ProvinceEntity::class, 'p')
            ->leftJoin('p.department', 'd');

        // Aplicar búsqueda global
        if ($search) {
            $qb->andWhere('p.name LIKE :search OR p.ubigeo LIKE :search OR d.name LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        // Aplicar filtros específicos
        if (isset($filters['name'])) {
            $qb->andWhere('p.name LIKE :name')
               ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['departmentId'])) {
            $qb->andWhere('d.id = :departmentId')
               ->setParameter('departmentId', $filters['departmentId']);
        }

        if (isset($filters['ubigeo'])) {
            $qb->andWhere('p.ubigeo LIKE :ubigeo')
               ->setParameter('ubigeo', '%' . $filters['ubigeo'] . '%');
        }

        return $qb->getQuery()->getSingleScalarResult();
    }
}