<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\user\UserCodeModel;
use itaxcix\Core\Interfaces\user\UserCodeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserCodeTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserCodeTypeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;

class DoctrineUserCodeRepository implements UserCodeRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private UserCodeTypeRepositoryInterface $userCodeTypeRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(EntityManagerInterface $entityManager, UserCodeTypeRepositoryInterface $userCodeTypeRepository, UserContactRepositoryInterface $userContactRepository){
        $this->entityManager = $entityManager;
        $this->userCodeTypeRepository = $userCodeTypeRepository;
        $this->userContactRepository = $userContactRepository;
    }

    private function toDomain(UserCodeEntity $entity): UserCodeModel {
        return new UserCodeModel(
            id: $entity->getId(),
            type: $this->userCodeTypeRepository->toDomain($entity->getType()),
            contact: $this->userContactRepository->toDomain($entity->getContact()),
            code: $entity->getCode(),
            expirationDate: $entity->getExpirationDate(),
            useDate: $entity->getUseDate(),
            used: $entity->isUsed()
        );
    }

    /**
     * @throws ORMException
     */
    public function saveUserCode(UserCodeModel $userCodeModel): UserCodeModel
    {
        if ($userCodeModel->getId()) {
            $entity = $this->entityManager->find(UserCodeEntity::class, $userCodeModel->getId());
        } else {
            $entity = new UserCodeEntity();
        }

        $entity->setType(
            $this->entityManager->getReference(
                UserCodeTypeEntity::class,
                $userCodeModel->getType()->getId()
            )
        );
        $entity->setContact(
            $this->entityManager->getReference(
                UserContactEntity::class,
                $userCodeModel->getContact()->getId()
            )
        );
        $entity->setCode($userCodeModel->getCode());
        $entity->setExpirationDate($userCodeModel->getExpirationDate());
        $entity->setUseDate($userCodeModel->getUseDate());
        $entity->setUsed($userCodeModel->isUsed());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findUserCodeByValueAndUser(string $value, int $userId): ?UserCodeModel
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('uc')
            ->from(UserCodeEntity::class, 'uc')
            ->innerJoin('uc.contact', 'c')
            ->innerJoin('c.user', 'u')
            ->where('uc.code = :code')
            ->andWhere('u.id = :userId')
            ->andWhere('uc.used = false')
            ->setParameter('code', $value)
            ->setParameter('userId', $userId)
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1);

        $entity = $qb->getQuery()->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserCodeByUserIdAndTypeId(int $userId, int $typeId): ?UserCodeModel
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb
            ->select('uc')
            ->from(UserCodeEntity::class, 'uc')
            ->innerJoin('uc.contact', 'c')
            ->innerJoin('c.user', 'u')
            ->innerJoin('uc.type', 't')
            ->where('u.id = :userId')
            ->andWhere('t.id = :typeId')
            ->setParameter('userId', $userId)
            ->setParameter('typeId', $typeId)
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1);

        $entity = $qb->getQuery()->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }
}