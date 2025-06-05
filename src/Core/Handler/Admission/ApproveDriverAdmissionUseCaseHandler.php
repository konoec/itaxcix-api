<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\UseCases\Admission\ApproveDriverAdmissionUseCase;
use itaxcix\Shared\DTO\useCases\Admission\ApproveDriverRequestDto;

class ApproveDriverAdmissionUseCaseHandler implements ApproveDriverAdmissionUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private DriverStatusRepositoryInterface $driverStatusRepository;

    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        DriverStatusRepositoryInterface $driverStatusRepository
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
    }

    public function execute(ApproveDriverRequestDto $dto): ?array
    {
        $driverStatus = $this->driverStatusRepository->findDriverStatusByName('APROBADO');

        if (!$driverStatus) {
            throw new InvalidArgumentException('No se encontró el estado de conductor APROBADO');
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
            available: $driverProfile->isAvailable(),
            status: $driverStatus
        );

        $updatedDriverProfile = $this->driverProfileRepository->saveDriverProfile($newDriverProfie);

        return [
            'message' => 'El conductor ha sido aprobado.',
        ];
    }
}