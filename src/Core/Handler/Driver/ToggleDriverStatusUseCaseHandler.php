<?php

namespace itaxcix\Core\Handler\Driver;

use Exception;
use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Driver\ToggleDriverStatusUseCase;
use itaxcix\Infrastructure\Cache\RedisService;
use itaxcix\Shared\DTO\useCases\Driver\DriverStatusResponseDTO;

class ToggleDriverStatusUseCaseHandler implements ToggleDriverStatusUseCase
{
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;
    private TucStatusRepositoryInterface $tucStatusRepository;
    private RedisService $redisService;

    public function __construct(
        DriverProfileRepositoryInterface $driverProfileRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        TucProcedureRepositoryInterface $tucProcedureRepository,
        TucStatusRepositoryInterface $tucStatusRepository,
        RedisService $redisService
    ) {
        $this->driverProfileRepository = $driverProfileRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->tucProcedureRepository = $tucProcedureRepository;
        $this->tucStatusRepository = $tucStatusRepository;
        $this->redisService = $redisService;
    }

    public function execute(int $driverId): ?DriverStatusResponseDTO
    {
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($driverId);
        $vehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($driverId);
        $tucStatus = $this->tucStatusRepository->findAllTucStatusByName('ACTIVO');

        if (!$tucStatus) {
            throw new InvalidArgumentException('Estado TUC no encontrado');
        }

        if (!$driverProfile) {
            throw new InvalidArgumentException('Conductor no válido o no encontrado');
        }

        if (!$vehicleUser) {
            throw new InvalidArgumentException('Usuario de vehículo no encontrado para el conductor');
        }

        $tucProcedures = $this->tucProcedureRepository->findTucProceduresByVehicleIdAndStatusId($vehicleUser->getVehicle()->getId(),$tucStatus->getId());

        if (empty($tucProcedures)) {
            throw new InvalidArgumentException('El conductor no cuenta con un TUC activo. Contáctese con el administrador del sistema.');
        }

        $driverProfile->setAvailable(!$driverProfile->isAvailable());
        $driverProfile = $this->driverProfileRepository->saveDriverProfile($driverProfile);

        // 👇 Publica evento en Redis
        $this->publishDriverStatusToRedis($driverId, $driverProfile->isAvailable());

        // Return the updated status
        return new DriverStatusResponseDTO($driverId, $driverProfile->isAvailable());
    }

    private function publishDriverStatusToRedis(int $driverId, bool $status): void
    {
        try {
            $payload = json_encode([
                'available' => $status,
                'driverId' => $driverId
            ]);

            $result = $this->redisService->getClient()->publish('driver_status_changed', $payload);
        } catch (Exception $e) {
            throw new InvalidArgumentException('Error al publicar el estado del conductor en Redis: ' . $e->getMessage());
        }
    }
}