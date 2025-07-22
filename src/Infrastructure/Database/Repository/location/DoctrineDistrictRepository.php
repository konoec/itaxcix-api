<?php

namespace itaxcix\Infrastructure\Database\Repository\location;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\location\DistrictModel;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\location\ProvinceRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;
use itaxcix\Infrastructure\Database\Entity\location\ProvinceEntity;
use RuntimeException;

class DoctrineDistrictRepository implements DistrictRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private ProvinceRepositoryInterface $provinceRepository;

    public function __construct(EntityManagerInterface $entityManager, ProvinceRepositoryInterface $provinceRepository) {
        $this->entityManager = $entityManager;
        $this->provinceRepository = $provinceRepository;
    }

    public function toDomain(DistrictEntity $entity): DistrictModel
    {
        return new DistrictModel(
            id: $entity->getId(),
            name: $entity->getName(),
            province: $this->provinceRepository->toDomain($entity->getProvince()),
            ubigeo: $entity->getUbigeo()
        );
    }

    public function findDistrictByName(string $name): ?DistrictModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(DistrictEntity::class, 'd')
            ->where('d.name = :name')
            ->setParameter('name', $name)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function saveDistrict(DistrictModel $districtModel): DistrictModel
    {
        if ($districtModel->getId()) {
            $entity = $this->entityManager->find(DistrictEntity::class, $districtModel->getId());
        } else {
            $entity = new DistrictEntity();
        }

        $entity->setName($districtModel->getName());
        $entity->setUbigeo($districtModel->getUbigeo());
        $entity->setProvince(
            $this->entityManager->getReference(
                ProvinceEntity::class,
                $districtModel->getProvince()->getId()
            )
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAll(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?int $provinceId = null,
        ?string $ubigeo = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc'
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('d, p')
            ->from(DistrictEntity::class, 'd')
            ->leftJoin('d.province', 'p');

        // Aplicar filtros
        if ($search) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('LOWER(d.name)', ':search'),
                    $qb->expr()->like('LOWER(d.ubigeo)', ':search'),
                    $qb->expr()->like('LOWER(p.name)', ':search')
                )
            )
            ->setParameter('search', '%' . strtolower($search) . '%');
        }

        if ($name) {
            $qb->andWhere('LOWER(d.name) LIKE :name')
               ->setParameter('name', '%' . strtolower($name) . '%');
        }

        if ($provinceId) {
            $qb->andWhere('d.province = :provinceId')
               ->setParameter('provinceId', $provinceId);
        }

        if ($ubigeo) {
            $qb->andWhere('LOWER(d.ubigeo) LIKE :ubigeo')
               ->setParameter('ubigeo', '%' . strtolower($ubigeo) . '%');
        }

        // Validar campo de ordenamiento
        $validSortFields = ['id', 'name', 'ubigeo'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'name';
        }

        $sortDirection = strtolower($sortDirection) === 'desc' ? 'DESC' : 'ASC';
        $qb->orderBy("d.$sortBy", $sortDirection);

        // Contar total antes de paginar
        $countQb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT d.id)')
            ->from(DistrictEntity::class, 'd')
            ->leftJoin('d.province', 'p');

        // Aplicar los mismos filtros para el conteo
        if ($search) {
            $countQb->andWhere(
                $countQb->expr()->orX(
                    $countQb->expr()->like('LOWER(d.name)', ':search'),
                    $countQb->expr()->like('LOWER(d.ubigeo)', ':search'),
                    $countQb->expr()->like('LOWER(p.name)', ':search')
                )
            )
            ->setParameter('search', '%' . strtolower($search) . '%');
        }

        if ($name) {
            $countQb->andWhere('LOWER(d.name) LIKE :name')
                   ->setParameter('name', '%' . strtolower($name) . '%');
        }

        if ($provinceId) {
            $countQb->andWhere('d.province = :provinceId')
                   ->setParameter('provinceId', $provinceId);
        }

        if ($ubigeo) {
            $countQb->andWhere('LOWER(d.ubigeo) LIKE :ubigeo')
                   ->setParameter('ubigeo', '%' . strtolower($ubigeo) . '%');
        }

        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $qb->setFirstResult($offset)
           ->setMaxResults($perPage);

        $entities = $qb->getQuery()->getResult();
        $items = array_map([$this, 'toDomain'], $entities);

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'lastPage' => (int) ceil($total / $perPage)
        ];
    }

    public function findById(int $id): ?DistrictModel
    {
        $entity = $this->entityManager->find(DistrictEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function create(DistrictModel $district): DistrictModel
    {
        $entity = new DistrictEntity();
        $entity->setName($district->getName());
        $entity->setUbigeo($district->getUbigeo());

        if ($district->getProvince()) {
            $provinceEntity = $this->entityManager->getReference(
                ProvinceEntity::class,
                $district->getProvince()->getId()
            );
            $entity->setProvince($provinceEntity);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function update(DistrictModel $district): DistrictModel
    {
        $entity = $this->entityManager->find(DistrictEntity::class, $district->getId());

        if (!$entity) {
            throw new RuntimeException("District with ID {$district->getId()} not found");
        }

        $entity->setName($district->getName());
        $entity->setUbigeo($district->getUbigeo());

        if ($district->getProvince()) {
            $provinceEntity = $this->entityManager->getReference(
                ProvinceEntity::class,
                $district->getProvince()->getId()
            );
            $entity->setProvince($provinceEntity);
        }

        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function delete(int $id): bool
    {
        $entity = $this->entityManager->find(DistrictEntity::class, $id);

        if (!$entity) {
            return false;
        }

        try {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            // Si ocurre un error al eliminar, podemos lanzar una excepción o manejarlo de otra manera
            throw new RuntimeException("Error eliminando distrito con ID $id: " . $e->getMessage());
        }

        return true;
    }
    public function existsByName(string $name, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DistrictEntity::class, 'd')
            ->where('LOWER(d.name) = LOWER(:name)')
            ->setParameter('name', $name);

        if ($excludeId) {
            $qb->andWhere('d.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function existsByUbigeo(string $ubigeo, ?int $excludeId = null): bool
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DistrictEntity::class, 'd')
            ->where('d.ubigeo = :ubigeo')
            ->setParameter('ubigeo', $ubigeo);

        if ($excludeId) {
            $qb->andWhere('d.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult() > 0;
    }

    public function countByProvince(int $provinceId): int
    {
        return (int) $this->entityManager->createQueryBuilder()
            ->select('COUNT(d.id)')
            ->from(DistrictEntity::class, 'd')
            ->where('d.province = :provinceId')
            ->setParameter('provinceId', $provinceId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByProvinceId(int $provinceId): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('d')
            ->from(DistrictEntity::class, 'd')
            ->where('d.province = :provinceId')
            ->setParameter('provinceId', $provinceId);

        $entities = $qb->getQuery()->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }
}

