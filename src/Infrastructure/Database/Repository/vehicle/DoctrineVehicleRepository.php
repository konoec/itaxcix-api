<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\vehicle\VehicleModel;
use itaxcix\Core\Interfaces\vehicle\ColorRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ModelRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ColorEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\FuelTypeEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ModelEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleCategoryEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleClassEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;

class DoctrineVehicleRepository implements VehicleRepositoryInterface {
    private EntityManagerInterface $entityManager;
    private ModelRepositoryInterface $modelRepository;
    private ColorRepositoryInterface $colorRepository;
    private FuelTypeRepositoryInterface $fuelTypeRepository;
    private VehicleClassRepositoryInterface $vehicleClassRepository;
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                ModelRepositoryInterface $modelRepository,
                                ColorRepositoryInterface $colorRepository,
                                FuelTypeRepositoryInterface $fuelTypeRepository,
                                VehicleClassRepositoryInterface $vehicleClassRepository,
                                VehicleCategoryRepositoryInterface $vehicleCategoryRepository) {
        $this->entityManager = $entityManager;
        $this->modelRepository = $modelRepository;
        $this->colorRepository = $colorRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->vehicleClassRepository = $vehicleClassRepository;
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;

    }

    public function toDomain(VehicleEntity $entity): VehicleModel {
        return new VehicleModel(
            id: $entity->getId(),
            licensePlate: $entity->getLicensePlate(),
            model: $this->modelRepository->toDomain($entity->getModel()),
            color: $this->colorRepository->toDomain($entity->getColor()),
            manufactureYear: $entity->getManufactureYear(),
            seatCount: $entity->getSeatCount(),
            passengerCount: $entity->getPassengerCount(),
            fuelType: $this->fuelTypeRepository->toDomain($entity->getFuelType()),
            vehicleClass: $this->vehicleClassRepository->toDomain($entity->getVehicleClass()),
            category: $this->vehicleCategoryRepository->toDomain($entity->getCategory()),
            active: $entity->isActive()
        );
    }

    public function findAllVehicleByPlate(string $plate): ?VehicleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.licensePlate = :plate')
            ->andWhere('v.active = :active')
            ->setParameter('plate', $plate)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveVehicle(VehicleModel $vehicleModel): VehicleModel
    {
        if ($vehicleModel->getId()){
            $entity = $this->entityManager->find(VehicleEntity::class, $vehicleModel->getId());
        } else {
            $entity = new VehicleEntity();
        }

        $entity->setModel(
            $this->entityManager->getReference(
                ModelEntity::class, $vehicleModel->getModel()->getId()
            )
        );
        $entity->setColor(
            $this->entityManager->getReference(
                ColorEntity::class, $vehicleModel->getColor()->getId()
            )
        );
        $entity->setFuelType(
            $this->entityManager->getReference(
                FuelTypeEntity::class, $vehicleModel->getFuelType()->getId()
            )
        );
        $entity->setVehicleClass(
            $this->entityManager->getReference(
                VehicleClassEntity::class, $vehicleModel->getVehicleClass()->getId()
            )
        );
        $entity->setCategory(
            $this->entityManager->getReference(
                VehicleCategoryEntity::class, $vehicleModel->getCategory()->getId()
            )
        );
        $entity->setLicensePlate($vehicleModel->getLicensePlate());
        $entity->setManufactureYear($vehicleModel->getManufactureYear());
        $entity->setSeatCount($vehicleModel->getSeatCount());
        $entity->setPassengerCount($vehicleModel->getPassengerCount());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findAllVehicleById(int $id): ?VehicleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }

    public function findVehicleById(int $id): ?VehicleModel
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.id = :id')
            ->andWhere('v.active = :active')
            ->setParameter('id', $id)
            ->setParameter('active', true)
            ->getQuery();

        $entity = $query->getOneOrNullResult();

        return $entity ? $this->toDomain($entity) : null;
    }
}