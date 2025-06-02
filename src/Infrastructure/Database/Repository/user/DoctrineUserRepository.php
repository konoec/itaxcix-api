<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Interfaces\person\PersonRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserStatusRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\person\PersonEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserStatusEntity;

class DoctrineUserRepository implements UserRepositoryInterface {
    private EntityManagerInterface $entityManager;
    private UserStatusRepositoryInterface $userStatusRepository;
    private PersonRepositoryInterface $personRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                UserStatusRepositoryInterface $userStatusRepository,
                                PersonRepositoryInterface $personRepository) {
        $this->entityManager = $entityManager;
        $this->userStatusRepository = $userStatusRepository;
        $this->personRepository = $personRepository;
    }

    public function toDomain(UserEntity $entity): UserModel {
        return new UserModel(
            id: $entity->getId(),
            password: $entity->getPassword(),
            person: $this->personRepository->toDomain($entity->getPerson()),
            status: $this->userStatusRepository->toDomain($entity->getStatus())
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

    public function findAllUserByPersonId(int $personId): ?UserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->innerJoin('u.person', 'p')
            ->innerJoin('u.status', 's')
            ->where('p.id = :personId')
            ->setParameter('personId', $personId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function saveUser(UserModel $userModel): UserModel
    {
        if ($userModel->getId()) {
            $entity = $this->entityManager->find(UserEntity::class, $userModel->getId());
        } else {
            $entity = new UserEntity();
        }

        $entity->setPassword($userModel->getPassword());
        $entity->setPerson(
            $this->entityManager->getReference(
                PersonEntity::class,
                $userModel->getPerson()?->getId()
            )
        );
        $entity->setStatus(
            $this->entityManager->getReference(
                UserStatusEntity::class,
                $userModel->getStatus()?->getId()
            )
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findUserById(int $id): ?UserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->innerJoin('u.person', 'p')
            ->innerJoin('u.status', 's')
            ->where('u.id = :id')
            ->andWhere('s.name = :statusName')
            ->setParameter('id', $id)
            ->setParameter('statusName', 'ACTIVO')
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}