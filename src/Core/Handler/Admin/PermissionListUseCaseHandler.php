<?php

namespace itaxcix\Core\Handler\Admin;

use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\PermissionListUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionResponseDTO;

class PermissionListUseCaseHandler implements PermissionListUseCase
{
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(int $page, int $perPage): ?PermissionListResponseDTO
    {
        // Obtener permisos paginados
        $permissions = $this->permissionRepository->findAllPermissions();

        // Calcular total y páginas
        $total = count($permissions);
        $lastPage = ceil($total / $perPage);

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $paginatedPermissions = array_slice($permissions, $offset, $perPage);

        // Convertir los modelos a DTOs
        $permissionDTOs = array_map(
            fn($permission) => new PermissionResponseDTO(
                id: $permission->getId(),
                name: $permission->getName(),
                active: $permission->isActive(),
                web: $permission->isWeb()
            ),
            $paginatedPermissions
        );

        // Crear metadatos de paginación
        $meta = new PaginationMetaDTO(
            total: $total,
            perPage: $perPage,
            currentPage: $page,
            lastPage: $lastPage
        );

        // Crear respuesta paginada
        $paginatedResponse = new PaginationResponseDTO(
            items: $permissionDTOs,
            meta: $meta
        );

        // Crear y retornar el DTO de respuesta
        return new PermissionListResponseDTO(
            permissions: $paginatedResponse
        );
    }
}
