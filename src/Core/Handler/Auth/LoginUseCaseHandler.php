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

        // Verificar perfiles de conductor y ciudadano
        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($user->getId());
        $citizenProfile = $this->citizenProfileRepository->findCitizenProfileByUserId($user->getId());

        // Solo considerar válido el perfil de conductor si está APROBADO
        $hasValidDriverProfile = $driverProfile && $driverProfile->getStatus()->getName() === 'APROBADO';

        // Si tiene perfil de conductor pero no está aprobado, actuar como si no lo tuviera
        if ($driverProfile && !$hasValidDriverProfile) {
            $driverProfile = null; // Ignorar el perfil de conductor no aprobado
        }

        // Verificar que tenga al menos un perfil válido
        if (!$citizenProfile && !$hasValidDriverProfile) {
            throw new InvalidArgumentException('El usuario no tiene perfiles válidos activos.');
        }

        // Determinar el rating promedio del perfil activo
        $averageRating = null;
        if ($hasValidDriverProfile) {
            $averageRating = $driverProfile->getAverageRating();
        } elseif ($citizenProfile) {
            $averageRating = $citizenProfile->getAverageRating();
        }

        // Verificar contacto confirmado
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

        // Filtrar roles basado en perfiles válidos (solo para móvil)
        $validRoles = [];
        if (!$dto->web) {
            foreach ($roles as $userRole) {
                $roleName = $userRole->getRole()->getName();

                // Solo incluir rol CONDUCTOR si tiene perfil aprobado
                if ($roleName === 'CONDUCTOR' && !$hasValidDriverProfile) {
                    continue; // Omitir este rol
                }

                // Solo incluir rol CIUDADANO si tiene perfil de ciudadano
                if ($roleName === 'CIUDADANO' && !$citizenProfile) {
                    continue; // Omitir este rol
                }

                // Para otros roles (como ADMINISTRADOR), incluir siempre
                $validRoles[] = $userRole;
            }
        } else {
            // Para web, usar todos los roles sin filtrar
            $validRoles = $roles;
        }

        // Verificar que tenga roles válidos después del filtrado
        if (empty($validRoles)) {
            throw new InvalidArgumentException('El usuario no tiene roles válidos para esta plataforma.');
        }

        // Obtener todos los permisos de todos los roles válidos
        $permissionsData = [];
        $permissionNames = [];

        foreach ($validRoles as $role) {
            $perms = $this->rolePermissionRepository->findPermissionsByRoleId($role->getRole()->getId(), $dto->web);
            foreach ($perms as $permission) {
                if (!isset($permissionsData[$permission->getId()])) {
                    $permissionsData[$permission->getId()] = [
                        'id' => $permission->getId(),
                        'name' => $permission->getName()
                    ];
                    $permissionNames[] = $permission->getName();
                }
            }
        }

        $permissions = array_values($permissionsData);

        if (empty($permissions)) {
            throw new InvalidArgumentException('El usuario no tiene permisos asignados. Contacte al administrador.');
        }

        // Determinar tipos de usuario basado en perfiles válidos
        $userTypes = [];

        if ($dto->web) {
            $userTypes[] = 'web';
            $primaryUserType = 'web';
        } else {
            // Solo agregar tipos para perfiles válidos
            if ($citizenProfile) {
                $userTypes[] = 'citizen';
            }

            if ($hasValidDriverProfile) {
                $userTypes[] = 'driver';
            }

            if (empty($userTypes)) {
                throw new InvalidArgumentException('El usuario no tiene perfiles válidos activos.');
            }

            // Tipo principal: driver si está aprobado, sino citizen
            $primaryUserType = $hasValidDriverProfile ? 'driver' : 'citizen';
        }

        // Construir roles para respuesta
        $rolesData = [];
        $roleNames = [];

        foreach ($validRoles as $userRole) {
            $role = $userRole->getRole();
            if (!isset($rolesData[$role->getId()])) {
                $rolesData[$role->getId()] = [
                    'id' => $role->getId(),
                    'name' => $role->getName()
                ];
                $roleNames[] = $role->getName();
            }
        }

        $rolesForResponse = array_values($rolesData);

        // Generar token
        $tokenPayload = [
            'userId' => $user->getId(),
            'userType' => $primaryUserType,
            'userTypes' => $userTypes,
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