<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\PermissionUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\PermissionUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionResponseDTO;

class PermissionUpdateUseCaseHandler implements PermissionUpdateUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(PermissionUpdateRequestDTO $dto): ?PermissionResponseDTO
    {
        // Verificar si existe el permiso
        $permission = $this->permissionRepository->findPermissionById($dto->id);
        if (!$permission) {
            throw new InvalidArgumentException('El permiso no existe.');
        }

        // Verificar si el nuevo nombre ya existe en otro permiso
        $existingPermission = $this->permissionRepository->findPermissionByName($dto->name);
        if ($existingPermission && $existingPermission->getId() !== $dto->id) {
            throw new InvalidArgumentException('Ya existe otro permiso con ese nombre.');
        }

        // Actualizar los datos del permiso
        $permission->setName($dto->name);
        $permission->setActive($dto->active);
        $permission->setWeb($dto->web);

        // Guardar los cambios
        $updatedPermission = $this->permissionRepository->savePermission($permission);

        // Crear y retornar el DTO de respuesta
        return new PermissionResponseDTO(
            id: $updatedPermission->getId(),
            name: $updatedPermission->getName(),
            active: $updatedPermission->isActive(),
            web: $updatedPermission->isWeb()
        );
    }
}
