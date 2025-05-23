<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity;

class DoctrineUserRoleRepository implements UserRoleRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(UserRoleModel $entity): UserRoleModel {
        return new UserRoleModel(
            $entity->getId(),
            $entity->getRole(),
            $entity->getUser(),
            $entity->isActive()
        );
    }

    public function findRolesByUserId(int $userId, bool $web): ?array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->where('ur.user = :userId')
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->setParameter('web', $web)
            ->getQuery();

        $result = $query->getResult();

        if (empty($result)) {
            return null;
        }

        return array_map(function ($item) {
            return $this->toDomain($item);
        }, $result);
    }
}