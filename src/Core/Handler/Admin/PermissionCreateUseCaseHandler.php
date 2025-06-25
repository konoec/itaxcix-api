<?php

namespace itaxcix\Core\Handler\Admin;

use InvalidArgumentException;
use itaxcix\Core\Domain\user\PermissionModel;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\PermissionCreateUseCase;
use itaxcix\Shared\DTO\useCases\Admin\PermissionCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionResponseDTO;

class PermissionCreateUseCaseHandler implements PermissionCreateUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(PermissionCreateRequestDTO $dto): ?PermissionResponseDTO
    {
        // Verificar si ya existe un permiso con el mismo nombre
        $existingPermission = $this->permissionRepository->findPermissionByName($dto->name);
        if ($existingPermission) {
            throw new InvalidArgumentException('Ya existe un permiso con ese nombre.');
        }

        // Crear nuevo permiso
        $permission = new PermissionModel(
            id: null,
            name: $dto->name,
            active: $dto->active,
            web: $dto->web
        );

        // Guardar el permiso
        $savedPermission = $this->permissionRepository->savePermission($permission);

        // Crear y retornar el DTO de respuesta
        return new PermissionResponseDTO(
            id: $savedPermission->getId(),
            name: $savedPermission->getName(),
            active: $savedPermission->isActive(),
            web: $savedPermission->isWeb()
        );
    }
}
