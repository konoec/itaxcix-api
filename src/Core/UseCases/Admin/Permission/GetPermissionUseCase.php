<?php

namespace itaxcix\Core\UseCases\Admin\Permission;

use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Permission\PermissionResponseDTO;

/**
 * GetPermissionUseCase - Caso de uso para obtener un permiso específico
 *
 * Permite obtener los detalles de un permiso específico por su ID.
 */
class GetPermissionUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(int $permissionId): PermissionResponseDTO
    {
        // Buscar el permiso por ID
        $permission = $this->permissionRepository->findById($permissionId);
        if (!$permission) {
            throw new \InvalidArgumentException("No se encontró el permiso con ID: {$permissionId}");
        }

        // Retornar respuesta
        return new PermissionResponseDTO(
            id: $permission->getId(),
            name: $permission->getName(),
            active: $permission->isActive(),
            web: $permission->isWeb()
        );
    }
}
