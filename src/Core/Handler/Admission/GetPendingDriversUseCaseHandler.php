<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\UserModel;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;
use itaxcix\Shared\DTO\useCases\Admission\PendingDriverResponseDTO;

class GetPendingDriversUseCaseHandler implements GetPendingDriversUseCase
{
    public DriverProfileRepositoryInterface $driverProfileRepository;
    public DriverStatusRepositoryInterface $driverStatusRepository;

    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        DriverStatusRepositoryInterface $driverStatusRepository
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
    }

    public function execute(): ?array
    {
        $driverStatus = $this->driverStatusRepository->findDriverStatusByName('PENDIENTE');

        if ($driverStatus){
            throw new InvalidArgumentException('No se encontró el estado de conductor PENDIENTE');
        }

        $driversProfiles = $this->driverProfileRepository->findDriversProfilesByStatusId($driverStatus->getId());

        if (empty($driversProfiles)) {
            throw new ('No se encontraron conductores pendientes.');
        }

        $response = [];
        $persons = [];
        foreach ($driversProfiles as $profile) {
            $response[] = new PendingDriverResponseDTO(
                driverId: $profile->getId(),
                fullName: $profile->getUser()->getPerson()->getName() . ' ' . $profile->getUser()->getPerson()->getLastName(),
                documentValue:
            );
        }

        if (empty($users)) {
            throw new InvalidArgumentException('No se encontraron usuarios asociados a los conductores pendientes.');
        }

        // de la tabla persona saco nombre, apellido y documento
        // de la tabla usuarioVehiculo saco el idVehiculo y de ahí su placa
        // de la tabla usuario contacto saco el contacto
    }
}