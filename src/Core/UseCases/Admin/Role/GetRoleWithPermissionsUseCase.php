<?php

namespace itaxcix\Core\UseCases\Admin\Role;

use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Role\GetRoleWithPermissionsRequestDTO;
use itaxcix\Shared\DTO\Admin\Role\RoleWithPermissionsResponseDTO;
use InvalidArgumentException;

class GetRoleWithPermissionsUseCase
{
    private RoleRepositoryInterface $roleRepository;
    private RolePermissionRepositoryInterface $rolePermissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        RolePermissionRepositoryInterface $rolePermissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function execute(GetRoleWithPermissionsRequestDTO $request): RoleWithPermissionsResponseDTO
    {
        // Verificar que el rol existe
        $role = $this->roleRepository->findById($request->roleId);
        if ($role === null) {
            throw new InvalidArgumentException('Rol no encontrado');
        }

        // Obtener todos los permisos asignados al rol
        $permissions = $this->rolePermissionRepository->findPermissionsByRoleId($request->roleId);

        return new RoleWithPermissionsResponseDTO(
            id: $role->getId(),
            name: $role->getName(),
            active: $role->isActive(),
            web: $role->isWeb(),
            permissions: $permissions
        );
    }
}
