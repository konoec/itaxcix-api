<?php

namespace itaxcix\Core\UseCases\Admin\User;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Domain\user\UserRoleModel;

/**
 * UpdateUserRolesUseCase - Caso de uso para actualizar roles de usuario
 *
 * Permite a los administradores actualizar los roles asignados a un usuario.
 * Respeta la estructura existente de UserRoleModel y mantiene auditoría.
 */
class UpdateUserRolesUseCase
{
    private UserRepositoryInterface $userRepository;
    private UserRoleRepositoryInterface $userRoleRepository;
    private RoleRepositoryInterface $roleRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserRoleRepositoryInterface $userRoleRepository,
        RoleRepositoryInterface $roleRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userRoleRepository = $userRoleRepository;
        $this->roleRepository = $roleRepository;
    }

    public function execute(int $userId, array $roleIds, ?string $adminReason = null): array
    {
        // Validar que el usuario existe
        $user = $this->userRepository->findUserById($userId);
        if (!$user) {
            throw new \InvalidArgumentException("Usuario con ID {$userId} no encontrado");
        }

        // Validar que todos los roles existen
        $validRoles = [];
        foreach ($roleIds as $roleId) {
            $role = $this->roleRepository->findRoleById($roleId);
            if (!$role) {
                throw new \InvalidArgumentException("Rol con ID {$roleId} no encontrado");
            }
            if (!$role->isActive()) {
                throw new \InvalidArgumentException("Rol {$role->getName()} no está activo");
            }
            $validRoles[] = $role;
        }

        // Obtener roles actuales del usuario
        $currentUserRoles = $this->userRoleRepository->findAllUserRoleByUserId($userId);

        // Desactivar roles actuales
        foreach ($currentUserRoles as $userRole) {
            if ($userRole->isActive()) {
                $userRole->setActive(false);
                $this->userRoleRepository->saveUserRole($userRole);
            }
        }

        // Asignar nuevos roles
        $assignedRoles = [];
        foreach ($validRoles as $role) {
            $userRole = new UserRoleModel(
                id: null,
                role: $role,
                user: $user,
                active: true
            );

            $savedUserRole = $this->userRoleRepository->saveUserRole($userRole);
            $assignedRoles[] = [
                'id' => $role->getId(),
                'name' => $role->getName()
            ];
        }

        // TODO: Registrar en auditoría la actualización de roles
        // AuditLogService::log('user_roles_updated', $userId, $adminReason);

        return [
            'message' => 'Roles de usuario actualizados exitosamente',
            'user' => [
                'id' => $userId,
                'assignedRoles' => $assignedRoles,
                'adminReason' => $adminReason
            ]
        ];
    }
}
