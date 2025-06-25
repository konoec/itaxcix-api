<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\PermissionDeleteUseCase;
use itaxcix\Shared\DTO\useCases\Admin\PermissionDeleteRequestDTO;

class PermissionDeleteUseCaseHandler implements PermissionDeleteUseCase
{
    private PermissionRepositoryInterface $permissionRepository;
    private RolePermissionRepositoryInterface $rolePermissionRepository;

    public function __construct(
        PermissionRepositoryInterface $permissionRepository,
        RolePermissionRepositoryInterface $rolePermissionRepository
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function execute(PermissionDeleteRequestDTO $dto): void
    {
        // Verificar si existe el permiso
        $permission = $this->permissionRepository->findPermissionById($dto->id);
        if (!$permission) {
            throw new InvalidArgumentException('El permiso no existe.');
        }

        // Verificar si el permiso está asignado a algún rol
        $hasRoles = $this->rolePermissionRepository->hasActiveRolesByPermissionId($dto->id);
        if ($hasRoles) {
            throw new InvalidArgumentException('No se puede eliminar el permiso porque está asignado a roles activos.');
        }

        // Eliminar el permiso
        $this->permissionRepository->deletePermission($permission);
    }
}
