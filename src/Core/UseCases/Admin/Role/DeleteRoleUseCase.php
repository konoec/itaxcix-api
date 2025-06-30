<?php

namespace itaxcix\Core\UseCases\Admin\Role;

use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Role\DeleteRoleRequestDTO;
use InvalidArgumentException;

class DeleteRoleUseCase
{
    private RoleRepositoryInterface $roleRepository;
    private RolePermissionRepositoryInterface $rolePermissionRepository;
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        RolePermissionRepositoryInterface $rolePermissionRepository,
        UserRoleRepositoryInterface $userRoleRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(DeleteRoleRequestDTO $request): void
    {
        // Verificar que el rol existe
        $role = $this->roleRepository->findById($request->id);
        if ($role === null) {
            throw new InvalidArgumentException('Rol no encontrado');
        }

        // Verificar que no haya usuarios activos con este rol
        if ($this->userRoleRepository->hasActiveUsersByRoleId($request->id)) {
            throw new InvalidArgumentException('No se puede eliminar el rol porque tiene usuarios asignados');
        }

        // Desactivar el rol (soft delete)
        $role->setActive(false);
        $this->roleRepository->save($role);

        // Desactivar todas las asignaciones de permisos del rol
        $this->rolePermissionRepository->removeAllByRoleId($request->id);
    }
}
