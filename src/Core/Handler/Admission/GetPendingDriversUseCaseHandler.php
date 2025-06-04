<?php

namespace itaxcix\Core\Handler\Admission;

use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\UseCases\Admission\GetPendingDriversUseCase;

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
        $driversProfiles = $this->driverProfileRepository->findDriversProfilesByStatusId($driverStatus->getId());

        // Buscar los usuarios con estado de usuario en pendiente
        // de la tabla persona saco nombre, apellido y documento
        // de la tabla usuarioVehiculo saco el idVehiculo y de ahí su placa
        // de la tabla usuario contacto saco el contacto
    }
}