<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\RolePermissionModel;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RolePermissionCreateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionResponseDTO;

class RolePermissionCreateUseCaseHandler implements RolePermissionCreateUseCase
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

    public function execute(RolePermissionCreateRequestDTO $dto): ?RolePermissionResponseDTO
    {
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

        // Verificar si ya existe la asignación
        $existingRolePermission = $this->rolePermissionRepository->findByRoleAndPermission($dto->roleId, $dto->permissionId);
        if ($existingRolePermission) {
            throw new InvalidArgumentException('El permiso ya está asignado a este rol.');
        }

        // Crear nueva asignación
        $rolePermission = new RolePermissionModel(
            id: null,
            role: $role,
            permission: $permission,
            active: $dto->active
        );

        // Guardar la asignación
        $savedRolePermission = $this->rolePermissionRepository->saveRolePermission($rolePermission);

        // Crear y retornar el DTO de respuesta
        return new RolePermissionResponseDTO(
            id: $savedRolePermission->getId(),
            roleId: $savedRolePermission->getRole()->getId(),
            permissionId: $savedRolePermission->getPermission()->getId(),
            active: $savedRolePermission->isActive()
        );
    }
}
