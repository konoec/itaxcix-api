<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\RoleEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity;

class DoctrineUserRoleRepository implements UserRoleRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private RoleRepositoryInterface $roleRepository;
    private UserRepositoryInterface $userRepository;
    public function __construct(EntityManagerInterface $entityManager, RoleRepositoryInterface $roleRepository, UserRepositoryInterface $userRepository) {
        $this->entityManager = $entityManager;
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    private function toDomain(UserRoleEntity $entity): UserRoleModel {
        return new UserRoleModel(
            id: $entity->getId(),
            role: $this->roleRepository->toDomain($entity->getRole()),
            user: $this->userRepository->toDomain($entity->getUser()),
            active: $entity->isActive()
        );
    }

    public function findRolesByUserId(int $userId, bool $web): ?array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('ur')
            ->from(UserRoleEntity::class, 'ur')
            ->join('ur.role', 'r')
            ->where('ur.user = :userId')
            ->andWhere('ur.active = :active')
            ->andWhere('r.web = :web')
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

    /**
     * @throws ORMException
     */
    public function saveUserRole(UserRoleModel $userRoleModel): UserRoleModel
    {
        if ($userRoleModel->getId()) {
            $entity = $this->entityManager->find(UserRoleEntity::class, $userRoleModel->getId());
        } else {
            $entity = new UserRoleEntity();
        }

        $entity->setRole(
            $this->entityManager->getReference(
                RoleEntity::class,
                $userRoleModel->getRole()->getId()
            )
        );
        $entity->setUser(
            $this->entityManager->getReference(
                UserEntity::class,
                $userRoleModel->getUser()->getId()
            )
        );
        $entity->setActive($userRoleModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}