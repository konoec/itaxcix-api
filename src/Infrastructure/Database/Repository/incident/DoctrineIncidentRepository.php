<?php

namespace itaxcix\Infrastructure\Database\Repository\incident;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use itaxcix\Core\Domain\incident\IncidentModel;
use itaxcix\Core\Interfaces\incident\IncidentRepositoryInterface;
use itaxcix\Core\Interfaces\incident\IncidentTypeRepositoryInterface;
use itaxcix\Core\Interfaces\travel\TravelRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentEntity;
use itaxcix\Infrastructure\Database\Entity\incident\IncidentTypeEntity;
use itaxcix\Infrastructure\Database\Entity\travel\TravelEntity;
use itaxcix\Infrastructure\Database\Entity\user\UserEntity;

class DoctrineIncidentRepository implements IncidentRepositoryInterface {
    private EntityManagerInterface $entityManager;
    private UserRepositoryInterface $userRepository;
    private TravelRepositoryInterface $travelRepository;
    private IncidentTypeRepositoryInterface $incidentTypeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepositoryInterface $userRepository,
        TravelRepositoryInterface $travelRepository,
        IncidentTypeRepositoryInterface $incidentTypeRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->travelRepository = $travelRepository;
        $this->incidentTypeRepository = $incidentTypeRepository;
    }

    public function toDomain(IncidentEntity $entity): IncidentModel {
        return new IncidentModel(
            $entity->getId(),
            $this->userRepository->toDomain($entity->getUser()),
            $this->travelRepository->toDomain($entity->getTravel()),
            $this->incidentTypeRepository->toDomain($entity->getType()),
            $entity->getComment(),
            $entity->isActive()
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveIncident(IncidentModel $incidentModel): IncidentModel {
        if ($incidentModel->getId()) {
            $entity = $this->entityManager->find(IncidentEntity::class, $incidentModel->getId());
        } else {
            $entity = new IncidentEntity();
        }
        if ($incidentModel->getUser()) {
            $entity->setUser($this->entityManager->getReference(UserEntity::class, $incidentModel->getUser()->getId()));
        }
        if ($incidentModel->getTravel()) {
            $entity->setTravel($this->entityManager->getReference(TravelEntity::class, $incidentModel->getTravel()->getId()));
        }
        if ($incidentModel->getType()) {
            $entity->setType($this->entityManager->getReference(IncidentTypeEntity::class, $incidentModel->getType()->getId()));
        }
        $entity->setComment($incidentModel->getComment());
        $entity->setActive($incidentModel->isActive());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
        return $this->toDomain($entity);
    }
}
