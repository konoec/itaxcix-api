<?php

namespace itaxcix\Core\Handler\User;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\DriverProfileModel;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Domain\vehicle\VehicleUserModel;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverStatusRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleUserRepositoryInterface;
use itaxcix\Core\UseCases\User\CitizenToDriverUseCase;
use itaxcix\Shared\DTO\useCases\User\CitizenToDriverRequestDTO;
use itaxcix\Shared\DTO\useCases\User\CitizenToDriverResponseDTO;

class CitizenToDriverUseCaseHandler implements CitizenToDriverUseCase
{
    private UserRepositoryInterface $userRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private DriverStatusRepositoryInterface $driverStatusRepository;
    private VehicleRepositoryInterface $vehicleRepository;
    private VehicleUserRepositoryInterface $vehicleUserRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        DriverStatusRepositoryInterface $driverStatusRepository,
        VehicleRepositoryInterface $vehicleRepository,
        VehicleUserRepositoryInterface $vehicleUserRepository,
        RoleRepositoryInterface $roleRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->driverStatusRepository = $driverStatusRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehicleUserRepository = $vehicleUserRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(CitizenToDriverRequestDTO $dto): CitizenToDriverResponseDTO
    {
        // 1. Verificar que el usuario existe
        $user = $this->userRepository->findUserById($dto->getUserId());
        if (!$user) {
            throw new InvalidArgumentException('El usuario no existe.');
        }

        // 2. Verificar que el usuario tiene perfil de ciudadano
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($dto->getUserId());
        if (!$citizenProfile) {
            throw new InvalidArgumentException('El usuario debe tener un perfil de ciudadano para solicitar ser conductor.');
        }

        // 3. Verificar que el usuario no tiene ya un perfil de conductor
        $existingDriverProfile = $this->driverProfileRepository->findDriverProfileByUserId($dto->getUserId());
        if ($existingDriverProfile) {
            throw new InvalidArgumentException('El usuario ya tiene un perfil de conductor.');
        }

        // 4. Verificar que el vehículo existe y está disponible
        $vehicle = $this->vehicleRepository->findVehicleById($dto->getVehicleId());
        if (!$vehicle) {
            throw new InvalidArgumentException('El vehículo especificado no existe.');
        }

        // 5. Verificar que el vehículo no está asignado a otro usuario
        $existingVehicleUser = $this->vehicleUserRepository->findVehicleUserByVehicleId($dto->getVehicleId());
        if ($existingVehicleUser) {
            throw new InvalidArgumentException('El vehículo ya está asignado a otro usuario.');
        }

        // 6. Obtener el estado "PENDIENTE" para el conductor
        $pendingStatus = $this->driverStatusRepository->findDriverStatusByName('PENDIENTE');
        if (!$pendingStatus) {
            throw new InvalidArgumentException('No se pudo encontrar el estado pendiente para conductores.');
        }

        // 7. Crear perfil de conductor en estado PENDIENTE
        $driverProfile = new DriverProfileModel(
            id: null,
            user: $user,
            status: $pendingStatus,
            averageRating: 0.00,
            ratingCount: 0
        );

        $savedDriverProfile = $this->driverProfileRepository->saveDriverProfile($driverProfile);

        // 8. Asociar vehículo al usuario
        $vehicleUser = new VehicleUserModel(
            id: null,
            user: $user,
            vehicle: $vehicle,
            active: true
        );

        $this->vehicleUserRepository->saveVehicleUser($vehicleUser);

        // 9. Asignar rol de CONDUCTOR (pero el perfil estará en PENDIENTE hasta aprobación)
        $driverRole = $this->roleRepository->findRoleByName('CONDUCTOR');
        if (!$driverRole) {
            throw new InvalidArgumentException('No se pudo encontrar el rol de conductor.');
        }

        // Verificar si ya tiene el rol de conductor
        $existingDriverRole = $this->userRoleRepository->findByUserAndRole($dto->getUserId(), $driverRole->getId());
        if (!$existingDriverRole) {
            $userRole = new UserRoleModel(
                id: null,
                role: $driverRole,
                user: $user,
                active: true
            );

            $this->userRoleRepository->saveUserRole($userRole);
        }

        return new CitizenToDriverResponseDTO(
            userId: $dto->getUserId(),
            status: 'PENDIENTE',
            message: 'Solicitud para ser conductor enviada correctamente. Esperando aprobación del administrador.',
            driverProfileId: $savedDriverProfile->getId()
        );
    }
}
