<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\UseCases\Admission\ApproveDriverAdmissionUseCase;
use itaxcix\Shared\DTO\useCases\Admission\ApproveDriverRequestDto;

class ApproveDriverAdmissionUseCaseHandler implements ApproveDriverAdmissionUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private DriverStatusRepositoryInterface $driverStatusRepository;
    private UserContactRepositoryInterface $userContactRepository;

    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        DriverStatusRepositoryInterface $driverStatusRepository,
        UserContactRepositoryInterface $userContactRepository
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
        $this->userContactRepository = $userContactRepository;
    }

    public function execute(ApproveDriverRequestDto $dto): ?array
    {
        $driverStatus = $this->driverStatusRepository->findDriverStatusByName('APROBADO');

        if (!$driverStatus) {
            throw new InvalidArgumentException('No se encontró el estado de conductor APROBADO');
        }

        $userContact = $this->userContactRepository->findUserContactByUserId($dto->driverId);

        if (!$userContact) {
            throw new InvalidArgumentException('El contacto del usuario no existe.');
        }

        if (!$userContact->isConfirmed()) {
            throw new InvalidArgumentException('El contacto del usuario no está verificado.');
        }

        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($dto->driverId);

        if (!$driverProfile) {
            throw new InvalidArgumentException('El perfil del conductor no existe.');
        }

        if ($driverProfile->getStatus()->getName() !== 'PENDIENTE') {
            throw new InvalidArgumentException('El conductor no está en estado pendiente.');
        }

        $newDriverProfie = new DriverProfileModel(
            id: $driverProfile->getId(),
            user: $driverProfile->getUser(),
            status: $driverStatus,
            averageRating: $driverProfile->getAverageRating(),
            ratingCount: $driverProfile->getRatingCount()
        );

        $updatedDriverProfile = $this->driverProfileRepository->saveDriverProfile($newDriverProfie);

        return [
            'message' => 'El conductor ha sido aprobado.',
        ];
    }
}