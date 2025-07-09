<?php

namespace itaxcix\Infrastructure\Database\Repository\user;

use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Core\Domain\user\UserContactModel;
use itaxcix\Core\Interfaces\user\ContactTypeRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserContactEntity;
use itaxcix\Infrastructure\Database\Entity\user\ContactTypeEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineUserContactRepository implements UserContactRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private ContactTypeRepositoryInterface $contactTypeRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ContactTypeRepositoryInterface $contactTypeRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->contactTypeRepository = $contactTypeRepository;
        $this->userRepository = $userRepository;
    }

    public function toDomain(object $entity): UserContactModel
    {
        if (!$entity instanceof UserContactEntity) {
            throw new \InvalidArgumentException('Entity must be instance of UserContactEntity');
        }

        return new UserContactModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            type: $this->contactTypeRepository->toDomain($entity->getType()),
            value: $entity->getValue(),
            confirmed: $entity->isConfirmed(),
            active: $entity->isActive()
        );
    }

    public function findAllUserContactByValue(string $value): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.value = :value')
            ->andWhere('uc.active = true')
            ->setParameter('value', $value)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserContactByTypeAndUser(int $typeId, int $userId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.type = :typeId')
            ->andWhere('uc.user = :userId')
            ->andWhere('uc.active = true')
            ->setParameter('typeId', $typeId)
            ->setParameter('userId', $userId)
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function saveUserContact(UserContactModel $contact): UserContactModel
    {
        try {
            if ($contact->getId()) {
                $entity = $this->entityManager->find(UserContactEntity::class, $contact->getId());
                if (!$entity) {
                    throw new \RuntimeException('UserContact not found for update');
                }
            } else {
                $entity = new UserContactEntity();
            }

            $entity->setValue($contact->getValue());
            $entity->setConfirmed($contact->isConfirmed());
            $entity->setActive($contact->isActive());

            // Establecer las relaciones
            $userEntity = $this->entityManager->getReference(UserEntity::class, $contact->getUser()->getId());
            $entity->setUser($userEntity);

            $typeEntity = $this->entityManager->getReference(ContactTypeEntity::class, $contact->getType()->getId());
            $entity->setType($typeEntity);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            return $this->toDomain($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error saving user contact: ' . $e->getMessage());
        }
    }

    public function findActiveContactByUserAndType(int $userId, int $contactTypeId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.type = :typeId')
            ->andWhere('uc.active = true')
            ->setParameter('userId', $userId)
            ->setParameter('typeId', $contactTypeId)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * Encuentra un contacto confirmado y activo por usuario y tipo
     * Este método se usa específicamente cuando necesitamos garantizar
     * que el contacto esté verificado para mostrar en perfiles
     */
    public function findConfirmedContactByUserAndType(int $userId, int $contactTypeId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.type = :typeId')
            ->andWhere('uc.active = true')
            ->andWhere('uc.confirmed = true')
            ->setParameter('userId', $userId)
            ->setParameter('typeId', $contactTypeId)
            ->orderBy('uc.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $results = $query->getResult();
        $entity = !empty($results) ? $results[0] : null;

        return $entity ? $this->toDomain($entity) : null;
    }

    public function deleteContact(int $contactId): void
    {
        try {
            $entity = $this->entityManager->find(UserContactEntity::class, $contactId);
            if ($entity) {
                $entity->setActive(false);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            throw new \RuntimeException('Error deleting contact: ' . $e->getMessage());
        }
    }

    public function findUserContactByUserId(int $userId): ?UserContactModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.active = true')
            ->setParameter('userId', $userId)
            ->setMaxResults(1)
            ->getQuery();

        $entity = $query->getOneOrNullResult();
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findUserContactByUserIdAndContactTypeId(int $userId, int $contactTypeId): ?UserContactModel
    {
        return $this->findActiveContactByUserAndType($userId, $contactTypeId);
    }

    // Métodos necesarios para administración avanzada
    public function findUserContactById(int $contactId): ?UserContactModel
    {
        $entity = $this->entityManager->find(UserContactEntity::class, $contactId);
        return $entity ? $this->toDomain($entity) : null;
    }

    public function findAllUserContactByUserId(int $userId): array
    {
        // Implementar lógica: contacto confirmado tiene prioridad,
        // si no hay confirmado entonces el último no confirmado
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.active = true')
            ->setParameter('userId', $userId)
            ->orderBy('uc.confirmed', 'DESC')  // Confirmados primero
            ->addOrderBy('uc.id', 'DESC')      // Más reciente segundo
            ->getQuery();

        $allContacts = $query->getResult();

        // Agrupar por tipo de contacto
        $contactsByType = [];
        foreach ($allContacts as $contact) {
            $typeId = $contact->getType()->getId();
            if (!isset($contactsByType[$typeId])) {
                $contactsByType[$typeId] = [];
            }
            $contactsByType[$typeId][] = $contact;
        }

        // Para cada tipo, aplicar la lógica de selección
        $selectedContacts = [];
        foreach ($contactsByType as $typeId => $contacts) {
            // Buscar contacto confirmado
            $confirmedContact = null;
            foreach ($contacts as $contact) {
                if ($contact->isConfirmed()) {
                    $confirmedContact = $contact;
                    break;
                }
            }

            if ($confirmedContact) {
                // Si hay contacto confirmado, usarlo
                $selectedContacts[] = $confirmedContact;
            } else {
                // Si no hay confirmado, usar el más reciente (primero en la lista ordenada)
                $selectedContacts[] = $contacts[0];
            }
        }

        return array_map([$this, 'toDomain'], $selectedContacts);
    }

    public function updateContactConfirmation(int $contactId, bool $confirmed): bool
    {
        try {
            $entity = $this->entityManager->find(UserContactEntity::class, $contactId);
            if (!$entity) {
                return false;
            }

            $entity->setConfirmed($confirmed);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findContactsByUserAndType(int $userId, int $typeId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('uc')
            ->from(UserContactEntity::class, 'uc')
            ->where('uc.user = :userId')
            ->andWhere('uc.type = :typeId')
            ->andWhere('uc.active = true')
            ->setParameter('userId', $userId)
            ->setParameter('typeId', $typeId)
            ->orderBy('uc.id', 'ASC')
            ->getQuery();

        $entities = $query->getResult();
        return array_map([$this, 'toDomain'], $entities);
    }
}
