<?php

namespace itaxcix\Core\Handler\Admission;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\UseCases\Admission\GetDriverDetailsUseCase;

class GetDriverDetailsUseCaseHandler implements GetDriverDetailsUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;

    public function __construct(DriverProfileRepositoryInterface $driverProfileRepository)
    {
        $this->driverProfileRepository = $driverProfileRepository;
    }

    public function execute(int $driverId): ?array
    {
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($driverId);

        if (!$driverProfile) {
            throw new InvalidArgumentException('El perfil del conductor no existe.');
        }

        if ($driverProfile->getStatus()->getName() !== 'PENDIENTE') {
            throw new InvalidArgumentException('El conductor no está en estado pendiente.');
        }

        // Buscar los usuarios con estado de usuario en pendiente
        // de la tabla persona saco nombre, apellido y documento
        // de la tabla usuarioVehiculo saco el idVehiculo y de ahí su placa
        // de la tabla usuario contacto saco el contacto
        // reutilizar la lógica de la consulta de los conductores pendientes
        // para las fechas usaremos la tuc a partir del idVehiculo
        // para el ruc usaremos el id tuc
        // tipo modalidad y estado a partir de id de tuc
    }
}