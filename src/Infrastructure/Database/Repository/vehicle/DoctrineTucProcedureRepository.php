<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\vehicle\TucProcedureModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\location\DistrictRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\company\CompanyEntity;
use itaxcix\Infrastructure\Database\Entity\location\DistrictEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ProcedureTypeEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucModalityEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucProcedureEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucStatusEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\VehicleEntity;

class DoctrineTucProcedureRepository implements TucProcedureRepositoryInterface {

    private EntityManagerInterface $entityManager;
    private VehicleRepositoryInterface $vehicleRepository;
    private CompanyRepositoryInterface $companyRepository;
    private DistrictRepositoryInterface $districtRepository;
    private TucStatusRepositoryInterface $tucStatusRepository;
    private ProcedureTypeRepositoryInterface $procedureTypeRepository;
    private TucModalityRepositoryInterface $procedureModalityRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                VehicleRepositoryInterface $vehicleRepository,
                                CompanyRepositoryInterface $companyRepository,
                                DistrictRepositoryInterface $districtRepository,
                                TucStatusRepositoryInterface $tucStatusRepository,
                                ProcedureTypeRepositoryInterface $procedureTypeRepository,
                                TucModalityRepositoryInterface $procedureModalityRepository) {
        $this->entityManager = $entityManager;
        $this->vehicleRepository = $vehicleRepository;
        $this->companyRepository = $companyRepository;
        $this->districtRepository = $districtRepository;
        $this->tucStatusRepository = $tucStatusRepository;
        $this->procedureTypeRepository = $procedureTypeRepository;
        $this->procedureModalityRepository = $procedureModalityRepository;
    }

    public function toDomain(TucProcedureEntity $entity): TucProcedureModel
    {
        return new TucProcedureModel(
            id: $entity->getId(),
            vehicle: $this->vehicleRepository->toDomain($entity->getVehicle()),
            company: $this->companyRepository->toDomain($entity->getCompany()),
            district: $this->districtRepository->toDomain($entity->getDistrict()),
            status: $this->tucStatusRepository->toDomain($entity->getStatus()),
            type: $this->procedureTypeRepository->toDomain($entity->getType()),
            modality:  $this->procedureModalityRepository->toDomain($entity->getModality()),
            procedureDate: $entity->getProcedureDate(),
            issueDate: $entity->getProcedureDate(),
            expirationDate: $entity->getExpirationDate()
        );
    }

    /**
     * @throws ORMException
     */
    public function saveTucProcedure(TucProcedureModel $tucProcedureModel): TucProcedureModel
    {
        if ($tucProcedureModel->getId()) {
            $entity = $this->entityManager->find(TucProcedureEntity::class, $tucProcedureModel->getId());
        } else {
            $entity = new TucProcedureEntity();
        }

        $entity->setVehicle(
            $this->entityManager->getReference(
                VehicleEntity::class, $tucProcedureModel->getVehicle()->getId()
            )
        );
        $entity->setCompany(
            $this->entityManager->getReference(
                CompanyEntity::class, $tucProcedureModel->getCompany()->getId()
            )
        );
        $entity->setDistrict(
            $this->entityManager->getReference(
                DistrictEntity::class, $tucProcedureModel->getDistrict()->getId()
            )
        );
        $entity->setStatus(
            $this->entityManager->getReference(
                TucStatusEntity::class, $tucProcedureModel->getStatus()->getId()
            )
        );
        $entity->setType(
            $this->entityManager->getReference(
                ProcedureTypeEntity::class, $tucProcedureModel->getType()->getId()
            )
        );
        $entity->setModality(
            $this->entityManager->getReference(
                TucModalityEntity::class, $tucProcedureModel->getModality()->getId()
            )
        );
        $entity->setProcedureDate($tucProcedureModel->getProcedureDate());
        $entity->setIssueDate($tucProcedureModel->getIssueDate());
        $entity->setExpirationDate($tucProcedureModel->getExpirationDate());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findTucProcedureByVehicleId(int $vehicleId): ?TucProcedureModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('tp')
            ->from(TucProcedureEntity::class, 'tp')
            ->join('tp.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->setParameter('vehicleId', $vehicleId)
            ->orderBy('tp.id', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $this->toDomain($result);
    }

    public function findTucProceduresByVehicleIdAndStatusId(int $vehicleId, int $statusId): ?array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('tp')
            ->from(TucProcedureEntity::class, 'tp')
            ->join('tp.vehicle', 'v')
            ->join('tp.status', 's')
            ->where('v.id = :vehicleId')
            ->andWhere('s.id = :statusId')
            ->setParameter('vehicleId', $vehicleId)
            ->setParameter('statusId', $statusId);

        $results = $qb->getQuery()->getResult();

        if (empty($results)) {
            return null;
        }

        return array_map([$this, 'toDomain'], $results);
    }

    public function findTucProcedureWithMaxExpirationDateByVehicleId(int $vehicleId): ?TucProcedureModel
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('tp')
            ->from(TucProcedureEntity::class, 'tp')
            ->join('tp.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->orderBy('tp.expirationDate', 'DESC')
            ->setParameter('vehicleId', $vehicleId)
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result === null) {
            return null;
        }

        return $this->toDomain($result);
    }

    public function findAllTucProceduresByVehicleId(int $vehicleId): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('tp')
            ->from(TucProcedureEntity::class, 'tp')
            ->join('tp.vehicle', 'v')
            ->where('v.id = :vehicleId')
            ->orderBy('tp.expirationDate', 'DESC')
            ->setParameter('vehicleId', $vehicleId);

        $results = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $results);
    }

    public function findByCompanyId(int $companyId): array
    {
        $qb = $this->entityManager->createQueryBuilder()
            ->select('tp')
            ->from(TucProcedureEntity::class, 'tp')
            ->join('tp.company', 'c')
            ->where('c.id = :companyId')
            ->setParameter('companyId', $companyId)
            ->orderBy('tp.expirationDate', 'DESC');

        $results = $qb->getQuery()->getResult();

        return array_map([$this, 'toDomain'], $results);
    }
}