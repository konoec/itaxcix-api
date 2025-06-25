<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RolePermissionUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionResponseDTO;

class RolePermissionUpdateUseCaseHandler implements RolePermissionUpdateUseCase
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

    public function execute(RolePermissionUpdateRequestDTO $dto): ?RolePermissionResponseDTO
    {
        // Verificar si existe la asignación
        $rolePermission = $this->rolePermissionRepository->findRolePermissionById($dto->id);
        if (!$rolePermission) {
            throw new InvalidArgumentException('La asignación de permiso al rol no existe.');
        }

        // Verificar si el rol existe
        $role = $this->roleRepository->findRoleById($dto->roleId);
        if (!$role) {
            throw new InvalidArgumentException('El rol no existe.');
        }

        // Verificar si el permiso existe
        $permission = $this->permissionRepository->findPermissionById($dto->permissionId);
        if (!$permission) {
            throw new InvalidArgumentException('El permiso no existe.');
        }

        // Verificar si ya existe otra asignación con el mismo rol y permiso
        $existingRolePermission = $this->rolePermissionRepository->findByRoleAndPermission($dto->roleId, $dto->permissionId);
        if ($existingRolePermission && $existingRolePermission->getId() !== $dto->id) {
            throw new InvalidArgumentException('El permiso ya está asignado a este rol.');
        }

        // Actualizar la asignación
        $rolePermission->setRole($role);
        $rolePermission->setPermission($permission);
        $rolePermission->setActive($dto->active);

        // Guardar los cambios
        $updatedRolePermission = $this->rolePermissionRepository->saveRolePermission($rolePermission);

        // Crear y retornar el DTO de respuesta
        return new RolePermissionResponseDTO(
            id: $updatedRolePermission->getId(),
            roleId: $updatedRolePermission->getRole()->getId(),
            permissionId: $updatedRolePermission->getPermission()->getId(),
            active: $updatedRolePermission->isActive()
        );
    }
}
