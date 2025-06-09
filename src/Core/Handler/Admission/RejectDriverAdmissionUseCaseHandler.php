<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\UseCases\Admission\RejectDriverAdmissionUseCase;
use itaxcix\Shared\DTO\useCases\Admission\RejectDriverRequestDto;

class RejectDriverAdmissionUseCaseHandler implements RejectDriverAdmissionUseCase
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

    public function execute(RejectDriverRequestDto $dto): ?array
    {
        $driverStatus = $this->driverStatusRepository->findDriverStatusByName('RECHAZADO');

        if (!$driverStatus) {
            throw new InvalidArgumentException('No se encontró el estado de conductor RECHAZADO');
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
            'message' => 'El conductor ha sido rechazado.',
        ];
    }
}