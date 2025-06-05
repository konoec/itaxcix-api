<?php

namespace itaxcix\Core\Handler\Auth;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\RolePermissionModel;
use itaxcix\Core\Domain\user\UserRoleModel;
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
    private JwtService $jwtService;

    public function __construct(UserRepositoryInterface $userRepository, UserRoleRepositoryInterface $userRoleRepository, RolePermissionRepositoryInterface $rolePermissionRepository, JwtService $jwtService, UserContactRepositoryInterface $userContactRepository, DriverProfileRepositoryInterface $driverProfileRepository)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
        $this->jwtService = $jwtService;
        $this->userContactRepository = $userContactRepository;
        $this->driverProfileRepository = $driverProfileRepository;
    }

    public function execute(AuthLoginRequestDTO $dto): ?AuthLoginResponseDTO
    {
        // Buscar usuario por documento
        $user = $this->userRepository->findUserByPersonDocument($dto->documentValue);

        if (!$user) {
            throw new InvalidArgumentException('No existe un usuario activo con ese documento.');
        }

        $driverProfile = $this->driverProfileRepository->findDriverProfileByUserId($user->getId());

        if ($driverProfile && $driverProfile->getStatus()->getName() !== 'APROBADO') {
            throw new InvalidArgumentException('El conductor no está aprobado.');
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
        $permissions = [];
        foreach ($roles as $role) {
            $perms = $this->rolePermissionRepository->findPermissionsByRoleId($role->getRole()->getId(), $dto->web);
            $permissions = [...$permissions, ...array_map(fn(RolePermissionModel $p) => $p->getPermission()->getName(), $perms)];
        }

        // Eliminar duplicados
        $permissions = array_unique($permissions);

        // Verificar si el usuario tiene permisos
        if (empty($permissions)) {
            throw new InvalidArgumentException('El usuario no tiene permisos asignados. Contacte al administrador.');
        }

        // Generar token
        $token = $this->jwtService->encode([
            'user_id' => $user->getId(),
            'roles' => array_map(fn(UserRoleModel $r) => $r->getRole()?->getName(), $roles),
            'permissions' => $permissions,
            'exp' => time() + 3600
        ]);

        return new AuthLoginResponseDTO(
            token: $token,
            userId: $user->getId(),
            documentValue: $user->getPerson()->getDocument(),
            firstName: $user->getPerson()->getName(),
            lastName: $user->getPerson()->getLastName(),
            availabilityStatus: $driverProfile?->isAvailable(),
            roles: array_map(fn(UserRoleModel $r) => $r->getRole()?->getName(), $roles),
            permissions: $permissions
        );
    }
}