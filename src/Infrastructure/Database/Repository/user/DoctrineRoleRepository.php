<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\RoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;

class DoctrineRoleRepository implements RoleRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function toDomain(RoleEntity $entity): RoleModel {
        return new RoleModel(
            id: $entity->getId(),
            name: $entity->getName(),
            active: $entity->isActive()
        );
    }

    public function findRoleByName(string $name): ?RoleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.name = :name')
            ->andWhere('r.active = :active')
            ->setParameter('name', $name)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findRoleById(int $id): ?RoleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.id = :id')
            ->andWhere('r.active = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAllRoles(): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(RoleEntity::class, 'r')
            ->where('r.active = :active')
            ->setParameter('active', true)
            ->getQuery();

        $results = $query->getResult();

        return array_map([$this, 'toDomain'], $results);
    }

    public function saveRole(RoleModel $role): RoleModel
    {
        if ($role->getId()) {
            $entity = $this->entityManager->find(RoleEntity::class, $role->getId());
        } else {
            $entity = new RoleEntity();
        }

        $entity->setName($role->getName());
        $entity->setActive($role->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function deleteRole(RoleModel $role): void
    {
        $entity = $this->entityManager->find(RoleEntity::class, $role->getId());
        if ($entity) {
            $entity->setActive(false);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }
}