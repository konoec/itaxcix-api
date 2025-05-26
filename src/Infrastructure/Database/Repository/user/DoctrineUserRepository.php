<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineUserRepository implements UserRepositoryInterface {
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    private function toDomain(UserEntity $entity): UserModel {
        return new UserModel(
            id: $entity->getId(),
            password: $entity->getPassword(),
            person: $entity->getPerson(),
            status: $entity->getStatus()
        );
    }

    public function findUserByPersonDocument(string $document): ?UserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->innerJoin('u.person', 'p')
            ->innerJoin('u.status', 's')
            ->where('p.document = :document')
            ->andWhere('s.name = :statusName')
            ->setParameter('document', $document)
            ->setParameter('statusName', 'ACTIVO')
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAllUserByPersonDocument(string $document): ?UserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->innerJoin('u.person', 'p')
            ->innerJoin('u.status', 's')
            ->where('p.document = :document')
            ->setParameter('document', $document)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}