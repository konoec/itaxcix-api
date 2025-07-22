<?php

namespace itaxcix\Infrastructure\Database\Repository\vehicle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use itaxcix\Core\Domain\vehicle\ServiceRouteModel;
use itaxcix\Core\Interfaces\vehicle\ServiceRouteRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\ServiceTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\vehicle\ServiceRouteEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\ServiceTypeEntity;
use itaxcix\Infrastructure\Database\Entity\vehicle\TucProcedureEntity;

class DoctrineServiceRouteRepository implements ServiceRouteRepositoryInterface {

    private EntityManagerInterface $entityManager;
    private TucProcedureRepositoryInterface $tucProcedureRepository;
    private ServiceTypeRepositoryInterface $serviceTypeRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                TucProcedureRepositoryInterface $tucProcedureRepository,
                                ServiceTypeRepositoryInterface $serviceTypeRepository) {
        $this->entityManager = $entityManager;
        $this->tucProcedureRepository = $tucProcedureRepository;
        $this->serviceTypeRepository = $serviceTypeRepository;
    }

    public function toDomain(ServiceRouteEntity $entity): ServiceRouteModel
    {
        return new ServiceRouteModel(
            id: $entity->getId(),
            procedure: $this->tucProcedureRepository->toDomain($entity->getProcedure()),
            serviceType: $this->serviceTypeRepository->toDomain($entity->getServiceType()),
            text: $entity->getText(),
            active: $entity->isActive()
        );
    }

    /**
     * @throws ORMException
     */
    public function saveServiceRoute(ServiceRouteModel $serviceRouteModel): ServiceRouteModel
    {
        if ($serviceRouteModel->getId()) {
            $entity = $this->entityManager->find(ServiceRouteEntity::class, $serviceRouteModel->getId());
        } else {
            $entity = new ServiceRouteEntity();
        }

        $entity->setProcedure(
            $this->entityManager->getReference(
                TucProcedureEntity::class, $serviceRouteModel->getProcedure()->getId()
            )
        );
        $entity->setServiceType(
            $this->entityManager->getReference(
                ServiceTypeEntity::class, $serviceRouteModel->getServiceType()->getId()
            )
        );
        $entity->setText($serviceRouteModel->getText());
        $entity->setActive($serviceRouteModel->isActive());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->toDomain($entity);
    }

    public function findByServiceTypeId(int $serviceTypeId): ?array
    {
        $query = $this->entityManager->createQuery(
            'SELECT sr FROM itaxcix\Infrastructure\Database\Entity\vehicle\ServiceRouteEntity sr
             JOIN sr.serviceType st
             WHERE st.id = :serviceTypeId AND sr.active = true'
        );
        $query->setParameter('serviceTypeId', $serviceTypeId);

        $results = $query->getResult();

        if (empty($results)) {
            return null;
        }

        return array_map([$this, 'toDomain'], $results);
    }
}