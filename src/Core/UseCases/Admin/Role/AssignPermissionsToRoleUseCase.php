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

        // Verificar que todos los permisos existen y que coinciden con el tipo web del rol
        $permissions = [];
        foreach ($request->permissionIds as $permissionId) {
            $permission = $this->permissionRepository->findById($permissionId);
            if ($permission === null) {
                throw new InvalidArgumentException("Permiso con ID {$permissionId} no encontrado");
            }
            if ($role->isWeb() !== $permission->isWeb()) {
                throw new InvalidArgumentException(
                    $role->isWeb()
                        ? "No se puede asignar el permiso '{$permission->getName()}' porque no es un permiso web"
                        : "No se puede asignar el permiso '{$permission->getName()}' porque es un permiso web"
                );
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
