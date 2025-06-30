<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\vehicle\VehicleUserModel;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleUserEntity;

class DoctrineVehicleUserRepository implements VehicleUserRepositoryInterface {
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private VehicleRepositoryInterface $vehicleRepository;
    public function __construct(EntityManagerInterface $entityManager,
                                UserRepositoryInterface $userRepository,
                                VehicleRepositoryInterface $vehicleRepository) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    private function toDomain(VehicleUserEntity $entity): VehicleUserModel {
        return new VehicleUserModel(
            id: $entity->getId(),
            user: $this->userRepository->toDomain($entity->getUser()),
            vehicle: $this->vehicleRepository->toDomain($entity->getVehicle()),
            active: $entity->isActive()
        );
    }

    public function findVehicleUserByVehicleId(int $vehicleId): ?VehicleUserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vu')
            ->from(VehicleUserEntity::class, 'vu')
            ->innerJoin('vu.user', 'u')
            ->innerJoin('vu.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->andWhere('vu.active = :active')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws ORMException
     */
    public function saveVehicleUser(VehicleUserModel $vehicleUserModel): VehicleUserModel
    {
        // Solo aplicar validaciones si la relación se está activando
        if ($vehicleUserModel->isActive()) {
            // Validación 1: Verificar que el usuario no tenga otra relación activa con otro vehículo
            $existingActiveUserRelation = $this->findActiveVehicleUsersByUserId($vehicleUserModel->getUser()->getId());
            foreach ($existingActiveUserRelation as $activeRelation) {
                // Si existe una relación activa con un vehículo diferente, desactivarla o lanzar error
                if ($activeRelation->getVehicle()->getId() !== $vehicleUserModel->getVehicle()->getId()) {
                    throw new \InvalidArgumentException(
                        "El usuario ya tiene una relación activa con otro vehículo (ID: {$activeRelation->getVehicle()->getId()}). " .
                        "Solo puede tener una relación activa a la vez."
                    );
                }
            }

            // Validación 2: Verificar que el vehículo no tenga otra relación activa con otro usuario
            $existingActiveVehicleRelation = $this->findActiveVehicleUserByVehicleId($vehicleUserModel->getVehicle()->getId());
            if ($existingActiveVehicleRelation &&
                $existingActiveVehicleRelation->getUser()->getId() !== $vehicleUserModel->getUser()->getId()) {
                throw new \InvalidArgumentException(
                    "El vehículo ya tiene una relación activa con otro usuario (ID: {$existingActiveVehicleRelation->getUser()->getId()}). " .
                    "Solo puede tener una relación activa a la vez."
                );
            }
        }

        if ($vehicleUserModel->getId()){
            $entity = $this->entityManager->find(VehicleUserEntity::class, $vehicleUserModel->getId());
        } else {
            $entity = new VehicleUserEntity();
        }

        $entity->setUser(
            $this->entityManager->getReference(
                UserEntity::class, $vehicleUserModel->getUser()->getId()
            )
        );
        $entity->setVehicle(
            $this->entityManager->getReference(
                VehicleEntity::class, $vehicleUserModel->getVehicle()->getId()
            )
        );
        $entity->setActive($vehicleUserModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findVehicleUserByUserId(int $userId): ?VehicleUserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vu')
            ->from(VehicleUserEntity::class, 'vu')
            ->innerJoin('vu.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('vu.active = :active')
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findActiveVehicleUsersByUserId(int $userId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vu')
            ->from(VehicleUserEntity::class, 'vu')
            ->innerJoin('vu.user', 'u')
            ->innerJoin('vu.vehicle', 'v')
            ->where('u.id = :userId')
            ->andWhere('vu.active = :active')
            ->andWhere('v.active = :vehicleActive')
            ->setParameter('userId', $userId)
            ->setParameter('active', true)
            ->setParameter('vehicleActive', true)
            ->getQuery();

        $entities = $query->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function findActiveVehicleUserByVehicleId(int $vehicleId): ?VehicleUserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vu')
            ->from(VehicleUserEntity::class, 'vu')
            ->innerJoin('vu.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->andWhere('vu.active = :active')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findActiveVehicleUserByUserIdAndVehicleId(int $userId, int $vehicleId): ?VehicleUserModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('vu')
            ->from(VehicleUserEntity::class, 'vu')
            ->innerJoin('vu.user', 'u')
            ->innerJoin('vu.vehicle', 'v')
            ->where('u.id = :userId')
            ->andWhere('v.id = :vehicleId')
            ->andWhere('vu.active = :active')
            ->setParameter('userId', $userId)
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}