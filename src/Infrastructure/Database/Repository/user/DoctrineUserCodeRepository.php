<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeTypeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;

class DoctrineUserCodeRepository implements UserCodeRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    private function toDomain(UserCodeEntity $entity): UserCodeModel
    {
        return new UserCodeModel(
            id: $entity->getId(),
            type: $entity->getType(),
            contact: $entity->getContact(),
            code: $entity->getCode(),
            expirationDate: $entity->getExpirationDate(),
            useDate: $entity->getUseDate(),
            used: $entity->isUsed()
        );
    }

    public function findUserCodeByValueAndUser(string $code, int $userId): ?UserCodeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserCodeEntity::class, 'uc')
            ->join('uc.contact', 'c')
            ->where('uc.code = :code')
            ->andWhere('c.user = :userId')
            ->andWhere('uc.used = :used')
            ->andWhere('uc.expirationDate > :now')
            ->setParameter('code', $code)
            ->setParameter('userId', $userId)
            ->setParameter('used', false)
            ->setParameter('now', new DateTime())
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findLatestUnusedCodeByContact(int $contactId): ?UserCodeModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserCodeEntity::class, 'uc')
            ->where('uc.contact = :contactId')
            ->andWhere('uc.used = :used')
            ->andWhere('uc.expirationDate > :now')
            ->setParameter('contactId', $contactId)
            ->setParameter('used', false)
            ->setParameter('now', new DateTime())
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveUserCode(UserCodeModel $code): UserCodeModel
    {
        if ($code->getId()) {
            $entity = $this->entityManager->find(UserCodeEntity::class, $code->getId());
        } else {
            $entity = new UserCodeEntity();
        }

        $entity->setType(
            $this->entityManager->getReference(UserCodeTypeEntity::class, $code->getType()->getId())
        );
        $entity->setContact(
            $this->entityManager->getReference(UserContactEntity::class, $code->getContact()->getId())
        );
        $entity->setCode($code->getCode());
        $entity->setExpirationDate($code->getExpirationDate());
        $entity->setUseDate($code->getUseDate());
        $entity->setUsed($code->isUsed());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }
}
