<?php

namespace itaxcix\Core\Handler\Vehicle;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\Vehicle\DisassociateUserVehicleUseCase;

class DisassociateUserVehicleUseCaseHandler implements DisassociateUserVehicleUseCase
{
    private VehicleUserRepositoryInterface $vehicleUserRepository;

    public function __construct(VehicleUserRepositoryInterface $vehicleUserRepository)
    {
        $this->vehicleUserRepository = $vehicleUserRepository;
    }

    public function execute(int $userId): bool
    {
        // Buscar la relación activa del usuario
        $activeVehicleUser = $this->vehicleUserRepository->findVehicleUserByUserId($userId);

        if (!$activeVehicleUser) {
            throw new InvalidArgumentException('El usuario no tiene ningún vehículo asociado actualmente.');
        }

        // Desactivar la relación
        $activeVehicleUser->setActive(false);
        $this->vehicleUserRepository->saveVehicleUser($activeVehicleUser);

        return true;
    }
}
