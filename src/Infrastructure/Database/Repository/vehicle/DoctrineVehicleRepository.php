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

    public function findReport(\itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO $dto): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('v, m, b, c, f, vc, cat')
            ->from(VehicleEntity::class, 'v')
            ->leftJoin('v.model', 'm')
            ->leftJoin('m.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.fuelType', 'f')
            ->leftJoin('v.vehicleClass', 'vc')
            ->leftJoin('v.category', 'cat');

        // Filtros directos
        if ($dto->licensePlate) {
            $qb->andWhere('v.licensePlate LIKE :licensePlate')
                ->setParameter('licensePlate', '%' . $dto->licensePlate . '%');
        }
        if ($dto->brandId) {
            $qb->andWhere('b.id = :brandId')
                ->setParameter('brandId', $dto->brandId);
        }
        if ($dto->modelId) {
            $qb->andWhere('m.id = :modelId')
                ->setParameter('modelId', $dto->modelId);
        }
        if ($dto->colorId) {
            $qb->andWhere('c.id = :colorId')
                ->setParameter('colorId', $dto->colorId);
        }
        if ($dto->manufactureYearFrom) {
            $qb->andWhere('v.manufactureYear >= :manufactureYearFrom')
                ->setParameter('manufactureYearFrom', $dto->manufactureYearFrom);
        }
        if ($dto->manufactureYearTo) {
            $qb->andWhere('v.manufactureYear <= :manufactureYearTo')
                ->setParameter('manufactureYearTo', $dto->manufactureYearTo);
        }
        if ($dto->seatCount) {
            $qb->andWhere('v.seatCount = :seatCount')
                ->setParameter('seatCount', $dto->seatCount);
        }
        if ($dto->passengerCount) {
            $qb->andWhere('v.passengerCount = :passengerCount')
                ->setParameter('passengerCount', $dto->passengerCount);
        }
        if ($dto->fuelTypeId) {
            $qb->andWhere('f.id = :fuelTypeId')
                ->setParameter('fuelTypeId', $dto->fuelTypeId);
        }
        if ($dto->vehicleClassId) {
            $qb->andWhere('vc.id = :vehicleClassId')
                ->setParameter('vehicleClassId', $dto->vehicleClassId);
        }
        if ($dto->categoryId) {
            $qb->andWhere('cat.id = :categoryId')
                ->setParameter('categoryId', $dto->categoryId);
        }
        if ($dto->active !== null) {
            $qb->andWhere('v.active = :active')
                ->setParameter('active', $dto->active);
        }

        // Filtros por relación con TUC - solo agregamos JOIN si hay filtros TUC
        if ($dto->companyId || $dto->districtId || $dto->statusId || $dto->procedureTypeId || $dto->modalityId) {
            $qb->leftJoin('itaxcix\\Infrastructure\\Database\\Entity\\vehicle\\TucProcedureEntity', 'tp', 'WITH', 'tp.vehicle = v');

            if ($dto->companyId) {
                $qb->andWhere('tp.company = :companyId')
                    ->setParameter('companyId', $dto->companyId);
            }
            if ($dto->districtId) {
                $qb->andWhere('tp.district = :districtId')
                    ->setParameter('districtId', $dto->districtId);
            }
            if ($dto->statusId) {
                $qb->andWhere('tp.status = :statusId')
                    ->setParameter('statusId', $dto->statusId);
            }
            if ($dto->procedureTypeId) {
                $qb->andWhere('tp.type = :procedureTypeId')
                    ->setParameter('procedureTypeId', $dto->procedureTypeId);
            }
            if ($dto->modalityId) {
                $qb->andWhere('tp.modality = :modalityId')
                    ->setParameter('modalityId', $dto->modalityId);
            }
        }

        // Ordenamiento
        $allowedSort = [
            'licensePlate' => 'v.licensePlate',
            'manufactureYear' => 'v.manufactureYear',
            'seatCount' => 'v.seatCount',
            'passengerCount' => 'v.passengerCount',
            'brandId' => 'b.id',
            'modelId' => 'm.id',
            'colorId' => 'c.id',
            'fuelTypeId' => 'f.id',
            'vehicleClassId' => 'vc.id',
            'categoryId' => 'cat.id'
        ];
        $sortBy = $allowedSort[$dto->sortBy] ?? 'v.licensePlate';
        $sortDirection = strtoupper($dto->sortDirection) === 'DESC' ? 'DESC' : 'ASC';
        $qb->orderBy($sortBy, $sortDirection)
            ->setFirstResult(($dto->page - 1) * $dto->perPage)
            ->setMaxResults($dto->perPage);

        $entities = $qb->getQuery()->getResult();
        $result = [];

        foreach ($entities as $row) {
            $entity = is_array($row) ? $row[0] : $row;

            if ($entity instanceof VehicleEntity) {
                $model = $entity->getModel();
                $brand = $model?->getBrand();
                $color = $entity->getColor();
                $fuelType = $entity->getFuelType();
                $vehicleClass = $entity->getVehicleClass();
                $category = $entity->getCategory();

                // Obtener TUC usando consulta directa para evitar dependencia circular
                $tucData = $this->getTucDataForVehicle($entity->getId());

                $result[] = [
                    'id' => $entity->getId(),
                    'licensePlate' => $entity->getLicensePlate(),
                    'brandName' => $brand?->getName(),
                    'modelName' => $model?->getName(),
                    'colorName' => $color?->getName(),
                    'manufactureYear' => $entity->getManufactureYear(),
                    'seatCount' => $entity->getSeatCount(),
                    'passengerCount' => $entity->getPassengerCount(),
                    'fuelTypeName' => $fuelType?->getName(),
                    'vehicleClassName' => $vehicleClass?->getName(),
                    'categoryName' => $category?->getName(),
                    'active' => $entity->isActive(),
                    'companyName' => $tucData['companyName'] ?? null,
                    'districtName' => $tucData['districtName'] ?? null,
                    'statusName' => $tucData['statusName'] ?? null,
                    'procedureTypeName' => $tucData['procedureTypeName'] ?? null,
                    'modalityName' => $tucData['modalityName'] ?? null
                ];
            }
        }
        return $result;
    }

    public function countReport(\itaxcix\Shared\DTO\useCases\VehicleReport\VehicleReportRequestDTO $dto): int
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT v.id)')
            ->from(VehicleEntity::class, 'v')
            ->leftJoin('v.model', 'm')
            ->leftJoin('m.brand', 'b')
            ->leftJoin('v.color', 'c')
            ->leftJoin('v.fuelType', 'f')
            ->leftJoin('v.vehicleClass', 'vc')
            ->leftJoin('v.category', 'cat');
        if ($dto->licensePlate) {
            $qb->andWhere('v.licensePlate LIKE :licensePlate')
                ->setParameter('licensePlate', '%' . $dto->licensePlate . '%');
        }
        if ($dto->brandId) {
            $qb->andWhere('b.id = :brandId')
                ->setParameter('brandId', $dto->brandId);
        }
        if ($dto->modelId) {
            $qb->andWhere('m.id = :modelId')
                ->setParameter('modelId', $dto->modelId);
        }
        if ($dto->colorId) {
            $qb->andWhere('c.id = :colorId')
                ->setParameter('colorId', $dto->colorId);
        }
        if ($dto->manufactureYearFrom) {
            $qb->andWhere('v.manufactureYear >= :manufactureYearFrom')
                ->setParameter('manufactureYearFrom', $dto->manufactureYearFrom);
        }
        if ($dto->manufactureYearTo) {
            $qb->andWhere('v.manufactureYear <= :manufactureYearTo')
                ->setParameter('manufactureYearTo', $dto->manufactureYearTo);
        }
        if ($dto->seatCount) {
            $qb->andWhere('v.seatCount = :seatCount')
                ->setParameter('seatCount', $dto->seatCount);
        }
        if ($dto->passengerCount) {
            $qb->andWhere('v.passengerCount = :passengerCount')
                ->setParameter('passengerCount', $dto->passengerCount);
        }
        if ($dto->fuelTypeId) {
            $qb->andWhere('f.id = :fuelTypeId')
                ->setParameter('fuelTypeId', $dto->fuelTypeId);
        }
        if ($dto->vehicleClassId) {
            $qb->andWhere('vc.id = :vehicleClassId')
                ->setParameter('vehicleClassId', $dto->vehicleClassId);
        }
        if ($dto->categoryId) {
            $qb->andWhere('cat.id = :categoryId')
                ->setParameter('categoryId', $dto->categoryId);
        }
        if ($dto->active !== null) {
            $qb->andWhere('v.active = :active')
                ->setParameter('active', $dto->active);
        }
        if ($dto->companyId || $dto->districtId || $dto->statusId || $dto->procedureTypeId || $dto->modalityId) {
            $qb->leftJoin('itaxcix\\Infrastructure\\Database\\Entity\\vehicle\\TucProcedureEntity', 'tp', 'WITH', 'tp.vehicle = v');
            if ($dto->companyId) {
                $qb->andWhere('tp.company = :companyId')
                    ->setParameter('companyId', $dto->companyId);
            }
            if ($dto->districtId) {
                $qb->andWhere('tp.district = :districtId')
                    ->setParameter('districtId', $dto->districtId);
            }
            if ($dto->statusId) {
                $qb->andWhere('tp.status = :statusId')
                    ->setParameter('statusId', $dto->statusId);
            }
            if ($dto->procedureTypeId) {
                $qb->andWhere('tp.type = :procedureTypeId')
                    ->setParameter('procedureTypeId', $dto->procedureTypeId);
            }
            if ($dto->modalityId) {
                $qb->andWhere('tp.modality = :modalityId')
                    ->setParameter('modalityId', $dto->modalityId);
            }
        }
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Obtiene datos TUC para un vehículo específico usando consulta directa
     * para evitar dependencias circulares
     */
    private function getTucDataForVehicle(int $vehicleId): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('c.name as companyName', 'd.name as districtName', 's.name as statusName', 't.name as procedureTypeName', 'm.name as modalityName')
            ->from('itaxcix\\Infrastructure\\Database\\Entity\\vehicle\\TucProcedureEntity', 'tp')
            ->leftJoin('tp.company', 'c')
            ->leftJoin('tp.district', 'd')
            ->leftJoin('tp.status', 's')
            ->leftJoin('tp.type', 't')
            ->leftJoin('tp.modality', 'm')
            ->where('tp.vehicle = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('tp.expirationDate', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result ?? [
            'companyName' => null,
            'districtName' => null,
            'statusName' => null,
            'procedureTypeName' => null,
            'modalityName' => null
        ];
    }

    public function findActiveByColorId(int $colorId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.color = :colorId')
            ->andWhere('v.active = :active')
            ->setParameter('colorId', $colorId)
            ->setParameter('active', true)
            ->getQuery();

        $entities = $query->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }

    public function findActiveByFuelTypeId(int $fuelTypeId): array
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(VehicleEntity::class, 'v')
            ->where('v.fuelType = :fuelTypeId')
            ->andWhere('v.active = :active')
            ->setParameter('fuelTypeId', $fuelTypeId)
            ->setParameter('active', true)
            ->getQuery();

        $entities = $query->getResult();

        return array_map([$this, 'toDomain'], $entities);
    }
}
