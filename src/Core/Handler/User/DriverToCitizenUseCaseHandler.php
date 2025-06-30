<?php

namespace itaxcix\Core\Handler\User;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\CitizenProfileModel;
use itaxcix\Core\Domain\user\UserRoleModel;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\User\DriverToCitizenUseCase;
use itaxcix\Shared\DTO\useCases\User\DriverToCitizenRequestDTO;
use itaxcix\Shared\DTO\useCases\User\DriverToCitizenResponseDTO;

class DriverToCitizenUseCaseHandler implements DriverToCitizenUseCase
{
    private UserRepositoryInterface $userRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        DriverProfileRepositoryInterface $driverProfileRepository,
        CitizenProfileRepositoryInterface $citizenProfileRepository,
        RoleRepositoryInterface $roleRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(DriverToCitizenRequestDTO $dto): DriverToCitizenResponseDTO
    {
        // 1. Verificar que el usuario existe
        $user = $this->userRepository->findUserById($dto->getUserId());
        if (!$user) {
            throw new InvalidArgumentException('El usuario no existe.');
        }

        // 2. Verificar que el usuario tiene perfil de conductor
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($dto->getUserId());
        if (!$driverProfile) {
            throw new InvalidArgumentException('El usuario debe tener un perfil de conductor para obtener perfil de ciudadano.');
        }

        // 3. Verificar que el usuario no tiene ya un perfil de ciudadano
        $existingCitizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($dto->getUserId());
        if ($existingCitizenProfile) {
            throw new InvalidArgumentException('El usuario ya tiene un perfil de ciudadano.');
        }

        // 4. Crear perfil de ciudadano
        $citizenProfile = new CitizenProfileModel(
            id: null,
            user: $user,
            averageRating: 0.00,
            ratingCount: 0
        );

        $savedCitizenProfile = $this->citizenProfileRepository->saveCitizenProfile($citizenProfile);

        // 5. Asignar rol de CIUDADANO
        $citizenRole = $this->roleRepository->findRoleByName('CIUDADANO');
        if (!$citizenRole) {
            throw new InvalidArgumentException('No se pudo encontrar el rol de ciudadano.');
        }

        // Verificar si ya tiene el rol de ciudadano
        $existingCitizenRole = $this->userRoleRepository->findByUserAndRole($dto->getUserId(), $citizenRole->getId());
        if (!$existingCitizenRole) {
            $userRole = new UserRoleModel(
                id: null,
                role: $citizenRole,
                user: $user,
                active: true
            );

            $this->userRoleRepository->saveUserRole($userRole);
        }

        return new DriverToCitizenResponseDTO(
            userId: $dto->getUserId(),
            status: 'ACTIVO',
            message: 'Perfil de ciudadano creado correctamente. Ya puedes usar ambos roles.',
            citizenProfileId: $savedCitizenProfile->getId()
        );
    }
}
