<?php

namespace itaxcix\Core\Handler\Auth;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\CitizenProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\DriverProfileRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserContactRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\Auth\LoginUseCase;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use itaxcix\Shared\DTO\useCases\Auth\AuthLoginRequestDTO;
use itaxcix\Shared\DTO\useCases\Auth\AuthLoginResponseDTO;

class LoginUseCaseHandler implements LoginUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private RolePermissionRepositoryInterface $rolePermissionRepository;
    private UserContactRepositoryInterface $userContactRepository;
    private DriverProfileRepositoryInterface $driverProfileRepository;
    private CitizenProfileRepositoryInterface $citizenProfileRepository;
    private JwtService $jwtService;

    public function __construct(UserRepositoryInterface $userRepository, UserRoleRepositoryInterface $userRoleRepository, RolePermissionRepositoryInterface $rolePermissionRepository, JwtService $jwtService, UserContactRepositoryInterface $userContactRepository, DriverProfileRepositoryInterface $driverProfileRepository, CitizenProfileRepositoryInterface $citizenProfileRepository)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
        $this->jwtService = $jwtService;
        $this->userContactRepository = $userContactRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->citizenProfileRepository = $citizenProfileRepository;
    }

    public function execute(AuthLoginRequestDTO $dto): ?AuthLoginResponseDTO
    {
        // Buscar usuario por documento
        $user = $this->userRepository->findUserByPersonDocument($dto->documentValue);

        if (!$user) {
            throw new InvalidArgumentException('No existe un usuario activo con ese documento.');
        }

        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($user->getId());
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($user->getId());

        if ($driverProfile && $driverProfile->getStatus()->getName() !== 'APROBADO') {
            throw new InvalidArgumentException('El conductor no está aprobado.');
        }

        $averageRating = null;

        if ($driverProfile){
            $averageRating = $driverProfile->getAverageRating();
        }

        if ($citizenProfile){
            $averageRating = $citizenProfile->getAverageRating();
        }

        $userContact = $this->userContactRepository->findUserContactByUserId($user->getId());

        if (!$userContact || !$userContact->isConfirmed()) {
            throw new InvalidArgumentException('El usuario no tiene un contacto confirmado.');
        }

        // Verificar contraseña
        if (!password_verify($dto->password, $user->getPassword())) {
            throw new InvalidArgumentException('Credenciales incorrectas.');
        }

        // Obtener roles del usuario
        $roles = $this->userRoleRepository->findRolesByUserId($user->getId(), $dto->web);

        if (empty($roles)) {
            throw new InvalidArgumentException('Error en datos. Contacte al administrador.');
        }

        // Obtener todos los permisos de todos los roles
        $permissionsData = [];
        $permissionNames = []; // Para mantener compatibilidad en el token

        foreach ($roles as $role) {
            $perms = $this->rolePermissionRepository->findPermissionsByRoleId($role->getRole()->getId(), $dto->web);
            foreach ($perms as $permission) {
                // Evitar duplicados por ID
                if (!isset($permissionsData[$permission->getId()])) {
                    $permissionsData[$permission->getId()] = [
                        'id' => $permission->getId(),
                        'name' => $permission->getName()
                    ];
                    $permissionNames[] = $permission->getName();
                }
            }
        }

        // Convertir a array indexado para la respuesta
        $permissions = array_values($permissionsData);

        // Verificar si el usuario tiene permisos
        if (empty($permissions)) {
            throw new InvalidArgumentException('El usuario no tiene permisos asignados. Contacte al administrador.');
        }

        // Determinar tipo de usuario basado en perfiles reales o plataforma web
        $userTypes = [];
        $hasApprovedDriverProfile = false;

        if ($dto->web) {
            // Si es acceso web, no importa driver/citizen
            $userTypes[] = 'web';
            $primaryUserType = 'web';
        } else {
            // Verificar perfil de ciudadano
            if ($citizenProfile) {
                $userTypes[] = 'citizen';
            }

            // Verificar perfil de conductor (solo si está aprobado)
            if ($driverProfile && $driverProfile->getStatus()->getName() === 'APROBADO') {
                $userTypes[] = 'driver';
                $hasApprovedDriverProfile = true;
            }

            // Si no tiene ningún perfil válido, error
            if (empty($userTypes)) {
                throw new InvalidArgumentException('El usuario no tiene perfiles válidos activos.');
            }

            // Para compatibilidad, el userType principal es driver si está aprobado, sino citizen
            $primaryUserType = $hasApprovedDriverProfile ? 'driver' : 'citizen';
        }

        // Construir roles con ID y nombre
        $rolesData = [];
        $roleNames = []; // Para mantener compatibilidad en el token

        foreach ($roles as $userRole) {
            $role = $userRole->getRole();
            // Evitar duplicados por ID
            if (!isset($rolesData[$role->getId()])) {
                $rolesData[$role->getId()] = [
                    'id' => $role->getId(),
                    'name' => $role->getName()
                ];
                $roleNames[] = $role->getName();
            }
        }

        // Convertir a array indexado para la respuesta
        $rolesForResponse = array_values($rolesData);

        // Generar token con userId y userTypes para validación de seguridad
        $tokenPayload = [
            'userId' => $user->getId(),
            'userType' => $primaryUserType, // ← Tipo principal para compatibilidad
            'userTypes' => $userTypes, // ← Todos los tipos disponibles para WebSocket
            // Datos legacy para el panel web (mantenemos solo nombres para compatibilidad)
            'user_id' => $user->getId(),
            'roles' => $roleNames,
            'permissions' => $permissionNames
        ];

        $token = $this->jwtService->encode($tokenPayload);

        return new AuthLoginResponseDTO(
            token: $token,
            userId: $user->getId(),
            documentValue: $user->getPerson()->getDocument(),
            firstName: $user->getPerson()->getName(),
            lastName: $user->getPerson()->getLastName(),
            roles: $rolesForResponse,
            permissions: $permissions,
            rating: $averageRating
        );
    }
}