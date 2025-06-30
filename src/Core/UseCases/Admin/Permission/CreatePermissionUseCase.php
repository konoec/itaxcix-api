<?php

namespace itaxcix\Core\UseCases\Admin\Permission;

use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\Domain\user\PermissionModel;
use itaxcix\Shared\DTO\Admin\Permission\CreatePermissionRequestDTO;
use itaxcix\Shared\DTO\Admin\Permission\PermissionResponseDTO;

/**
 * CreatePermissionUseCase - Caso de uso para crear permisos
 *
 * Permite crear nuevos permisos en el sistema con validaciones
 * de unicidad y categorización adecuada.
 */
class CreatePermissionUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(CreatePermissionRequestDTO $requestDTO): PermissionResponseDTO
    {
        // Validar que el permiso no exista
        $existingPermission = $this->permissionRepository->findPermissionByName($requestDTO->name);
        if ($existingPermission) {
            throw new \InvalidArgumentException("Ya existe un permiso con el nombre: {$requestDTO->name}");
        }

        // Crear el modelo del permiso
        $permissionModel = new PermissionModel(
            id: null,
            name: $requestDTO->name,
            active: $requestDTO->active,
            web: $requestDTO->web
        );

        // Guardar en el repositorio
        $savedPermission = $this->permissionRepository->savePermission($permissionModel);

        // Retornar respuesta
        return new PermissionResponseDTO(
            id: $savedPermission->getId(),
            name: $savedPermission->getName(),
            active: $savedPermission->isActive(),
            web: $savedPermission->isWeb()
        );
    }
}
