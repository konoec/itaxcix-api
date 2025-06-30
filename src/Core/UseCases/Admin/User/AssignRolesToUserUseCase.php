<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\User\AssignRolesToUserRequestDTO;
use itaxcix\Shared\DTO\Admin\User\UserWithRolesResponseDTO;
use InvalidArgumentException;

class AssignRolesToUserUseCase
{
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(AssignRolesToUserRequestDTO $request): UserWithRolesResponseDTO
    {
        // Verificar que el usuario existe
        $user = $this->userRepository->findById($request->userId);
        if ($user === null) {
            throw new InvalidArgumentException('Usuario no encontrado');
        }

        // Verificar que todos los roles existen
        $roles = [];
        foreach ($request->roleIds as $roleId) {
            $role = $this->roleRepository->findById($roleId);
            if ($role === null) {
                throw new InvalidArgumentException("Rol con ID {$roleId} no encontrado");
            }
            $roles[] = $role;
        }

        // Remover todas las asignaciones actuales del usuario
        $this->userRoleRepository->removeAllByUserId($request->userId);

        // Asignar los nuevos roles
        $assignedRoles = [];
        foreach ($roles as $role) {
            $userRole = $this->userRoleRepository->assignRoleToUser($user, $role);
            $assignedRoles[] = $role;
        }

        return new UserWithRolesResponseDTO(
            userId: $user->getId(),
            roles: $assignedRoles
        );
    }
}
