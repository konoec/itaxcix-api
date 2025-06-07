<?php

namespace itaxcix\Core\Handler\Driver;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Driver\DriverTucStatusUseCase;
use itaxcix\Infrastructure\Cache\RedisService;
use itaxcix\Shared\DTO\useCases\Driver\DriverTucStatusResponseDTO;

class DriverTucStatusUseCaseHandler implements DriverTucStatusUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;
    private TucStatusRepositoryInterface $tucStatusRepository;
    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        TucProcedureRepositoryInterface $tucProcedureRepository,
        TucStatusRepositoryInterface $tucStatusRepository,
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
        $this->tucStatusRepository = $tucStatusRepository;
    }

    public function execute(int $driverId): ?DriverTucStatusResponseDTO
{
    $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($driverId);
    $tucStatus = $this->tucStatusRepository->findAllTucStatusByName('ACTIVO');

    if (!$tucStatus) {
        throw new InvalidArgumentException('Estado TUC no encontrado');
    }

    if (!$vehicleUser) {
        throw new InvalidArgumentException('Usuario de vehículo no encontrado para el conductor');
    }

    $tucProcedures = $this->tucProcedureRepository->findTucProceduresByVehicleIdAndStatusId(
        $vehicleUser->getVehicle()->getId(),
        $tucStatus->getId()
    );

    if (empty($tucProcedures)) {
        throw new InvalidArgumentException('El conductor no cuenta con un TUC activo. Contáctese con el administrador del sistema.');
    }

    // Si tiene TUC activa, retornar mensaje de éxito
    return new DriverTucStatusResponseDTO($driverId, true);
}
}