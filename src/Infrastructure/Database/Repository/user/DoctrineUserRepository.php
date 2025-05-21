<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\person\PersonModel;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Domain\user\UserStatusModel;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineUserRepository implements UserRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function save(UserModel $user): void {
        // Aquí deberías convertir UserModel → UserEntity antes de guardar
        // Ejemplo simplificado:
        $doctrineUser = $this->entityManager->find(UserEntity::class, $user->getId());

        if (!$doctrineUser) {
            $doctrineUser = new UserEntity();
        }

        $doctrineUser->setAlias($user->getAlias());
        $doctrineUser->setPassword($user->getPassword());
        $doctrineUser->setPerson($user->getPerson()?->toEntity() ?? null);
        $doctrineUser->setStatus($user->getStatus()?->toEntity() ?? null);

        $this->entityManager->persist($doctrineUser);
        $this->entityManager->flush();
    }

    public function ofId(int $id): ?UserModel {
        $entity = $this->entityManager->find(UserEntity::class, $id);

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findByAlias(string $alias): ?UserModel {
        $entity = $this->entityManager->getRepository(UserEntity::class)
            ->findOneBy(['alias' => $alias]);

        return $entity ? $this->toDomain($entity) : null;
    }

    private function toDomain(UserEntity $entity): UserModel {
        return new UserModel(
            id: $entity->getId(),
            alias: $entity->getAlias(),
            password: $entity->getPassword(),
            person: $entity->getPerson() ? new PersonModel(...) : null,
            status: $entity->getStatus() ? new UserStatusModel(...) : null
        );
    }
}