<?php

namespace itaxcix\Core\UseCases\Admin\Permission;

use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Permission\UpdatePermissionRequestDTO;
use itaxcix\Shared\DTO\Admin\Permission\PermissionResponseDTO;

/**
 * UpdatePermissionUseCase - Caso de uso para actualizar permisos
 *
 * Permite actualizar permisos existentes en el sistema con validaciones
 * de existencia y unicidad de nombre.
 */
class UpdatePermissionUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(UpdatePermissionRequestDTO $requestDTO): PermissionResponseDTO
    {
        // Validar que el permiso exista
        $existingPermission = $this->permissionRepository->findById($requestDTO->id);
        if (!$existingPermission) {
            throw new \InvalidArgumentException("No se encontró el permiso con ID: {$requestDTO->id}");
        }

        // Validar unicidad del nombre si cambió
        if ($existingPermission->getName() !== $requestDTO->name) {
            $permissionWithSameName = $this->permissionRepository->findPermissionByName($requestDTO->name);
            if ($permissionWithSameName && $permissionWithSameName->getId() !== $requestDTO->id) {
                throw new \InvalidArgumentException("Ya existe otro permiso con el nombre: {$requestDTO->name}");
            }
        }

        // Actualizar las propiedades del modelo
        $existingPermission->setName($requestDTO->name);
        $existingPermission->setActive($requestDTO->active);
        $existingPermission->setWeb($requestDTO->web);

        // Guardar cambios
        $updatedPermission = $this->permissionRepository->savePermission($existingPermission);

        // Retornar respuesta
        return new PermissionResponseDTO(
            id: $updatedPermission->getId(),
            name: $updatedPermission->getName(),
            active: $updatedPermission->isActive(),
            web: $updatedPermission->isWeb()
        );
    }
}
