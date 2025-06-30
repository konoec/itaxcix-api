<?php

namespace itaxcix\Core\UseCases\Admin\Role;

use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Role\AssignPermissionsToRoleRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\RoleWithPermissionsResponseDTO;
use InvalidArgumentException;

class AssignPermissionsToRoleUseCase
{
    private RoleRepositoryInterface $roleRepository;
    private PermissionRepositoryInterface $permissionRepository;
    private RolePermissionRepositoryInterface $rolePermissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository,
        RolePermissionRepositoryInterface $rolePermissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function execute(AssignPermissionsToRoleRequestDTO $request): RoleWithPermissionsResponseDTO
    {
        // Verificar que el rol existe
        $role = $this->roleRepository->findById($request->roleId);
        if ($role === null) {
            throw new InvalidArgumentException('Rol no encontrado');
        }

        // Verificar que todos los permisos existen
        $permissions = [];
        foreach ($request->permissionIds as $permissionId) {
            $permission = $this->permissionRepository->findById($permissionId);
            if ($permission === null) {
                throw new InvalidArgumentException("Permiso con ID {$permissionId} no encontrado");
            }
            $permissions[] = $permission;
        }

        // Remover todas las asignaciones actuales del rol
        $this->rolePermissionRepository->removeAllByRoleId($request->roleId);

        // Asignar los nuevos permisos
        $assignedPermissions = [];
        foreach ($permissions as $permission) {
            $rolePermission = $this->rolePermissionRepository->assignPermissionToRole(
                $role,
                $permission
            );
            $assignedPermissions[] = $permission;
        }

        return new RoleWithPermissionsResponseDTO(
            id: $role->getId(),
            name: $role->getName(),
            active: $role->isActive(),
            web: $role->isWeb(),
            permissions: $assignedPermissions
        );
    }
}
