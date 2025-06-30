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
        $entity = $this->entityManager->find(UserEntity::class, $id);
        return $entity ? $this->toDomain($entity) : null;
    }

    // Nuevos métodos para administración avanzada
    public function findById(int $id): ?UserModel
    {
        return $this->findUserById($id);
    }

    public function findAllPaginated(
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?int $roleId = null,
        ?int $statusId = null,
        ?bool $withWebAccess = null
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('u.status', 's');

        // Si necesitamos filtrar por rol, agregamos el join
        if ($roleId !== null || $withWebAccess !== null) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity', 'ur', 'WITH', 'ur.user = u.id AND ur.active = true')
               ->leftJoin('ur.role', 'r');
        }

        // Aplicar filtros
        if ($search !== null && !empty(trim($search))) {
            $qb->andWhere('(p.name LIKE :search OR p.lastName LIKE :search OR p.document LIKE :search)')
               ->setParameter('search', '%' . trim($search) . '%');
        }

        if ($statusId !== null) {
            $qb->andWhere('s.id = :statusId')
               ->setParameter('statusId', $statusId);
        }

        if ($roleId !== null) {
            $qb->andWhere('r.id = :roleId')
               ->setParameter('roleId', $roleId);
        }

        if ($withWebAccess !== null) {
            $qb->andWhere('r.web = :webAccess')
               ->setParameter('webAccess', $withWebAccess);
        }

        // Aplicar paginación
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit)
           ->orderBy('p.name', 'ASC')
           ->addOrderBy('p.lastName', 'ASC');

        $results = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $results);
    }

    public function countAll(
        ?string $search = null,
        ?int $roleId = null,
        ?int $statusId = null,
        ?bool $withWebAccess = null
    ): int {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT u.id)')
            ->from(UserEntity::class, 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('u.status', 's');

        // Si necesitamos filtrar por rol, agregamos el join
        if ($roleId !== null || $withWebAccess !== null) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity', 'ur', 'WITH', 'ur.user = u.id AND ur.active = true')
               ->leftJoin('ur.role', 'r');
        }

        // Aplicar los mismos filtros que en findAllPaginated
        if ($search !== null && !empty(trim($search))) {
            $qb->andWhere('(p.name LIKE :search OR p.lastName LIKE :search OR p.document LIKE :search)')
               ->setParameter('search', '%' . trim($search) . '%');
        }

        if ($statusId !== null) {
            $qb->andWhere('s.id = :statusId')
               ->setParameter('statusId', $statusId);
        }

        if ($roleId !== null) {
            $qb->andWhere('r.id = :roleId')
               ->setParameter('roleId', $roleId);
        }

        if ($withWebAccess !== null) {
            $qb->andWhere('r.web = :webAccess')
               ->setParameter('webAccess', $withWebAccess);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Método específico para filtros administrativos avanzados del sistema de transporte
     */
    public function findUsersPaginatedWithFilters(
        int $page,
        int $limit,
        array $filters
    ): array {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UserEntity::class, 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('u.status', 's');

        // Joins condicionales según los filtros necesarios
        $needsRoleJoin = isset($filters['roleId']) || isset($filters['userType']);
        $needsDriverJoin = isset($filters['driverStatus']) || $filters['userType'] === 'driver';
        $needsCitizenJoin = $filters['userType'] === 'citizen';
        $needsContactJoin = isset($filters['contactVerified']);
        $needsVehicleJoin = isset($filters['hasVehicle']);

        if ($needsRoleJoin) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity', 'ur', 'WITH', 'ur.user = u.id AND ur.active = true')
               ->leftJoin('ur.role', 'r');
        }

        if ($needsDriverJoin) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\DriverProfileEntity', 'dp', 'WITH', 'dp.user = u.id')
               ->leftJoin('dp.status', 'ds');
        }

        if ($needsCitizenJoin) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\CitizenProfileEntity', 'cp', 'WITH', 'cp.user = u.id');
        }

        if ($needsContactJoin) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\UserContactEntity', 'uc', 'WITH', 'uc.user = u.id AND uc.active = true');
        }

        if ($needsVehicleJoin) {
            $qb->leftJoin('itaxcix\Infrastructure\Database\Entity\vehicle\VehicleUserEntity', 'vu', 'WITH', 'vu.user = u.id AND vu.active = true')
               ->leftJoin('vu.vehicle', 'v');
        }

        // Aplicar filtros específicos
        if (!empty($filters['search'])) {
            $qb->andWhere('(p.name LIKE :search OR p.lastName LIKE :search OR p.document LIKE :search)')
               ->setParameter('search', '%' . trim($filters['search']) . '%');
        }

        if (isset($filters['statusId'])) {
            $qb->andWhere('s.id = :statusId')
               ->setParameter('statusId', $filters['statusId']);
        }

        if (isset($filters['roleId'])) {
            $qb->andWhere('r.id = :roleId')
               ->setParameter('roleId', $filters['roleId']);
        }

        // Filtro por tipo de usuario (específico para sistema de transporte)
        if ($filters['userType'] === 'driver') {
            $qb->andWhere('dp.id IS NOT NULL');
        } elseif ($filters['userType'] === 'citizen') {
            $qb->andWhere('cp.id IS NOT NULL AND dp.id IS NULL');
        } elseif ($filters['userType'] === 'admin') {
            $qb->andWhere('r.name LIKE :adminRole')
               ->setParameter('adminRole', '%ADMIN%');
        }

        // Filtro por estado de conductor (específico para sistema de transporte)
        if (!empty($filters['driverStatus'])) {
            $qb->andWhere('ds.name = :driverStatus')
               ->setParameter('driverStatus', $filters['driverStatus']);
        }

        // Filtro por vehículo asociado
        if (isset($filters['hasVehicle'])) {
            if ($filters['hasVehicle']) {
                $qb->andWhere('v.id IS NOT NULL');
            } else {
                $qb->andWhere('v.id IS NULL');
            }
        }

        // Filtro por contactos verificados
        if (isset($filters['contactVerified'])) {
            if ($filters['contactVerified']) {
                $qb->andWhere('uc.confirmed = true');
            } else {
                $qb->andWhere('(uc.confirmed = false OR uc.id IS NULL)');
            }
        }

        // Aplicar paginación y ordenamiento
        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit)
           ->orderBy('p.name', 'ASC')
           ->addOrderBy('p.lastName', 'ASC');

        $results = $qb->getQuery()->getResult();
        $users = array_map([$this, 'toDomain'], $results);

        // Contar total con los mismos filtros
        $totalQb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT u.id)')
            ->from(UserEntity::class, 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('u.status', 's');

        // Aplicar los mismos joins y filtros para el count
        if ($needsRoleJoin) {
            $totalQb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\UserRoleEntity', 'ur', 'WITH', 'ur.user = u.id AND ur.active = true')
                    ->leftJoin('ur.role', 'r');
        }

        if ($needsDriverJoin) {
            $totalQb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\DriverProfileEntity', 'dp', 'WITH', 'dp.user = u.id')
                    ->leftJoin('dp.status', 'ds');
        }

        if ($needsCitizenJoin) {
            $totalQb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\CitizenProfileEntity', 'cp', 'WITH', 'cp.user = u.id');
        }

        if ($needsContactJoin) {
            $totalQb->leftJoin('itaxcix\Infrastructure\Database\Entity\user\UserContactEntity', 'uc', 'WITH', 'uc.user = u.id AND uc.active = true');
        }

        if ($needsVehicleJoin) {
            $totalQb->leftJoin('itaxcix\Infrastructure\Database\Entity\vehicle\VehicleUserEntity', 'vu', 'WITH', 'vu.user = u.id AND vu.active = true')
                    ->leftJoin('vu.vehicle', 'v');
        }

        // Aplicar los mismos filtros que arriba
        if (!empty($filters['search'])) {
            $totalQb->andWhere('(p.name LIKE :search OR p.lastName LIKE :search OR p.document LIKE :search)')
                    ->setParameter('search', '%' . trim($filters['search']) . '%');
        }

        if (isset($filters['statusId'])) {
            $totalQb->andWhere('s.id = :statusId')
                    ->setParameter('statusId', $filters['statusId']);
        }

        if (isset($filters['roleId'])) {
            $totalQb->andWhere('r.id = :roleId')
                    ->setParameter('roleId', $filters['roleId']);
        }

        if ($filters['userType'] === 'driver') {
            $totalQb->andWhere('dp.id IS NOT NULL');
        } elseif ($filters['userType'] === 'citizen') {
            $totalQb->andWhere('cp.id IS NOT NULL AND dp.id IS NULL');
        } elseif ($filters['userType'] === 'admin') {
            $totalQb->andWhere('r.name LIKE :adminRole')
                    ->setParameter('adminRole', '%ADMIN%');
        }

        if (!empty($filters['driverStatus'])) {
            $totalQb->andWhere('ds.name = :driverStatus')
                    ->setParameter('driverStatus', $filters['driverStatus']);
        }

        if (isset($filters['hasVehicle'])) {
            if ($filters['hasVehicle']) {
                $totalQb->andWhere('v.id IS NOT NULL');
            } else {
                $totalQb->andWhere('v.id IS NULL');
            }
        }

        if (isset($filters['contactVerified'])) {
            if ($filters['contactVerified']) {
                $totalQb->andWhere('uc.confirmed = true');
            } else {
                $totalQb->andWhere('(uc.confirmed = false OR uc.id IS NULL)');
            }
        }

        $total = (int) $totalQb->getQuery()->getSingleScalarResult();

        return [
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($total / $limit)
        ];
    }

    /**
     * Actualizar el estado de un usuario
     */
    public function updateStatus(int $userId, int $statusId): bool
    {
        try {
            $user = $this->entityManager->find(UserEntity::class, $userId);
            if (!$user) {
                return false;
            }

            $status = $this->entityManager->getReference(UserStatusEntity::class, $statusId);
            $user->setStatus($status);

            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Actualizar la contraseña de un usuario
     */
    public function updatePassword(int $userId, string $hashedPassword): bool
    {
        try {
            $user = $this->entityManager->find(UserEntity::class, $userId);
            if (!$user) {
                return false;
            }

            $user->setPassword($hashedPassword);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function findReport(\itaxcix\Shared\DTO\useCases\UserReport\UserReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('u, p, s, dt')
            ->from(UserEntity::class, 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('u.status', 's')
            ->leftJoin('p.documentType', 'dt');

        // Filtros
        if ($dto->name) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $dto->name . '%');
        }
        if ($dto->lastName) {
            $qb->andWhere('p.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $dto->lastName . '%');
        }
        if ($dto->document) {
            $qb->andWhere('p.document LIKE :document')
                ->setParameter('document', '%' . $dto->document . '%');
        }
        if ($dto->documentTypeId) {
            $qb->andWhere('dt.id = :documentTypeId')
                ->setParameter('documentTypeId', $dto->documentTypeId);
        }
        if ($dto->statusId) {
            $qb->andWhere('s.id = :statusId')
                ->setParameter('statusId', $dto->statusId);
        }
        if ($dto->email) {
            $qb->andWhere('EXISTS (SELECT 1 FROM itaxcix\\Infrastructure\\Database\\Entity\\user\\UserContactEntity uc WHERE uc.user = u.id AND uc.type = 1 AND uc.value LIKE :email AND uc.active = true)')
                ->setParameter('email', '%' . $dto->email . '%');
        }
        if ($dto->phone) {
            $qb->andWhere('EXISTS (SELECT 1 FROM itaxcix\\Infrastructure\\Database\\Entity\\user\\UserContactEntity uc WHERE uc.user = u.id AND uc.type = 2 AND uc.value LIKE :phone AND uc.active = true)')
                ->setParameter('phone', '%' . $dto->phone . '%');
        }
        if ($dto->active !== null) {
            $qb->andWhere('p.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->validationStartDate) {
            $qb->andWhere('p.validationDate >= :validationStartDate')
                ->setParameter('validationStartDate', $dto->validationStartDate . ' 00:00:00');
        }
        if ($dto->validationEndDate) {
            $qb->andWhere('p.validationDate <= :validationEndDate')
                ->setParameter('validationEndDate', $dto->validationEndDate . ' 23:59:59');
        }
        $sortBy = in_array($dto->sortBy, ['name', 'lastName', 'document', 'email', 'phone', 'validationDate']) ? $dto->sortBy : 'name';
        $sortField = $sortBy === 'validationDate' ? 'p.validationDate' : ($sortBy === 'document' ? 'p.document' : 'p.' . $sortBy);
        $sortDirection = strtoupper($dto->sortDirection) === 'DESC' ? 'DESC' : 'ASC';
        $qb->orderBy($sortField, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];
        foreach ($entities as $entity) {
            if ($entity instanceof UserEntity) {
                $person = $entity->getPerson();
                $result[] = [
                    'id' => $entity->getId(),
                    'name' => $person?->getName() ?? '',
                    'lastName' => $person?->getLastName() ?? '',
                    'document' => $person?->getDocument() ?? '',
                    'documentType' => $person?->getDocumentType()?->getName() ?? '',
                    'status' => $entity->getStatus()?->getName() ?? '',
                    'email' => $this->getContactValue($entity, 1),
                    'phone' => $this->getContactValue($entity, 2),
                    'active' => $person?->isActive() ?? false,
                    'validationDate' => $person?->getValidationDate()?->format('Y-m-d H:i:s')
                ];
            }
        }
        return $result;
    }

    public function countReport(\itaxcix\Shared\DTO\useCases\UserReport\UserReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from(UserEntity::class, 'u')
            ->leftJoin('u.person', 'p')
            ->leftJoin('u.status', 's')
            ->leftJoin('p.documentType', 'dt');
        if ($dto->name) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $dto->name . '%');
        }
        if ($dto->lastName) {
            $qb->andWhere('p.lastName LIKE :lastName')
                ->setParameter('lastName', '%' . $dto->lastName . '%');
        }
        if ($dto->document) {
            $qb->andWhere('p.document LIKE :document')
                ->setParameter('document', '%' . $dto->document . '%');
        }
        if ($dto->documentTypeId) {
            $qb->andWhere('dt.id = :documentTypeId')
                ->setParameter('documentTypeId', $dto->documentTypeId);
        }
        if ($dto->statusId) {
            $qb->andWhere('s.id = :statusId')
                ->setParameter('statusId', $dto->statusId);
        }
        if ($dto->email) {
            $qb->andWhere('EXISTS (SELECT 1 FROM itaxcix\\Infrastructure\\Database\\Entity\\user\\UserContactEntity uc WHERE uc.user = u.id AND uc.type = 1 AND uc.value LIKE :email AND uc.active = true)')
                ->setParameter('email', '%' . $dto->email . '%');
        }
        if ($dto->phone) {
            $qb->andWhere('EXISTS (SELECT 1 FROM itaxcix\\Infrastructure\\Database\\Entity\\user\\UserContactEntity uc WHERE uc.user = u.id AND uc.type = 2 AND uc.value LIKE :phone AND uc.active = true)')
                ->setParameter('phone', '%' . $dto->phone . '%');
        }
        if ($dto->active !== null) {
            $qb->andWhere('p.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->validationStartDate) {
            $qb->andWhere('p.validationDate >= :validationStartDate')
                ->setParameter('validationStartDate', $dto->validationStartDate . ' 00:00:00');
        }
        if ($dto->validationEndDate) {
            $qb->andWhere('p.validationDate <= :validationEndDate')
                ->setParameter('validationEndDate', $dto->validationEndDate . ' 23:59:59');
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    private function getContactValue(UserEntity $user, int $type): ?string
    {
        foreach ($user->getContacts() as $contact) {
            if ($contact->getType() && $contact->getType()->getId() === $type && $contact->isActive()) {
                return $contact->getValue();
            }
        }
        return null;
    }
}
