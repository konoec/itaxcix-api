<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use itaxcix\Shared\DTO\useCases\AuthLoginRequestDTO;
use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Shared\DTO\useCases\AuthLoginResponseDTO;

class LoginUseCaseHandler implements LoginUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private RolePermissionRepositoryInterface $rolePermissionRepository;
    private JwtService $jwtService;

    public function __construct(UserRepositoryInterface $userRepository, UserRoleRepositoryInterface $userRoleRepository, RolePermissionRepositoryInterface $rolePermissionRepository, JwtService $jwtService)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
        $this->jwtService = $jwtService;
    }

    public function execute(AuthLoginRequestDTO $dto): ?AuthLoginResponseDTO
    {
        // Buscar usuario por documento
        $user = $this->userRepository->findUserByPersonDocument($dto->documentValue);

        if (!$user) {
            return null;
        }

        // Verificar contraseña
        if (!password_verify($dto->password, $user->getPassword())) {
            return null;
        }

        // Obtener roles del usuario
        $roles = $this->userRoleRepository->findRolesByUserId($user->getId(), $dto->web);

        if (empty($roles)) {
            return null;
        }

        // Obtener todos los permisos de todos los roles
        $permissions = [];
        foreach ($roles as $role) {
            $perms = $this->rolePermissionRepository->findPermissionsByRoleId($role->getId(), $dto->web);
            $permissions = [...$permissions, ...array_map(fn($p) => $p->getName(), $perms)];
        }

        // Eliminar duplicados
        $permissions = array_unique($permissions);

        // Generar token
        $token = $this->jwtService->encode([
            'user_id' => $user->getId(),
            'roles' => array_map(fn($r) => $r->getName(), $roles),
            'permissions' => $permissions,
            'exp' => time() + 3600
        ]);

        return new AuthLoginResponseDTO(
            token: $token,
            userId: $user->getId(),
            documentValue: $user->getPerson()->getDocument(),
            roles: array_map(fn($r) => $r->getName(), $roles),
            permissions: $permissions
        );
    }
}