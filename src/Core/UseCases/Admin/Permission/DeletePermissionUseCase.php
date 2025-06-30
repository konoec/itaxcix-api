<?php

namespace itaxcix\Core\UseCases\Admin\Permission;

use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;

/**
 * DeletePermissionUseCase - Caso de uso para eliminar permisos
 *
 * Permite eliminar permisos del sistema con validaciones de existencia.
 * Nota: Considera implementar soft delete si hay relaciones con roles.
 */
class DeletePermissionUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(int $permissionId): array
    {
        // Validar que el permiso exista
        $existingPermission = $this->permissionRepository->findById($permissionId);
        if (!$existingPermission) {
            throw new \InvalidArgumentException("No se encontró el permiso con ID: {$permissionId}");
        }

        // TODO: Verificar si el permiso está siendo usado por algún rol
        // antes de eliminarlo para evitar referencias rotas

        // Eliminar el permiso
        $this->permissionRepository->deletePermission($existingPermission);

        return [
            'message' => 'Permiso eliminado exitosamente',
            'deletedPermission' => [
                'id' => $existingPermission->getId(),
                'name' => $existingPermission->getName()
            ]
        ];
    }
}
