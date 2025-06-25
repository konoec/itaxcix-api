<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RolePermissionDeleteUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionDeleteRequestDTO;

class RolePermissionDeleteUseCaseHandler implements RolePermissionDeleteUseCase
{
    private RolePermissionRepositoryInterface $rolePermissionRepository;

    public function __construct(RolePermissionRepositoryInterface $rolePermissionRepository)
    {
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function execute(RolePermissionDeleteRequestDTO $dto): void
    {
        // Verificar si existe la asignación
        $rolePermission = $this->rolePermissionRepository->findRolePermissionById($dto->id);
        if (!$rolePermission) {
            throw new InvalidArgumentException('La asignación de permiso al rol no existe.');
        }

        // Eliminar la asignación
        $this->rolePermissionRepository->deleteRolePermission($rolePermission);
    }
}
