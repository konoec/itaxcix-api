<?php

namespace itaxcix\Core\Handler\Admin;

use itaxcix\Core\Interfaces\user\RolePermissionRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RolePermissionListUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionResponseDTO;

class RolePermissionListUseCaseHandler implements RolePermissionListUseCase
{
    private RolePermissionRepositoryInterface $rolePermissionRepository;

    public function __construct(RolePermissionRepositoryInterface $rolePermissionRepository)
    {
        $this->rolePermissionRepository = $rolePermissionRepository;
    }

    public function execute(int $page, int $perPage): ?RolePermissionListResponseDTO
    {
        // Obtener todas las asignaciones
        $rolePermissions = $this->rolePermissionRepository->findAllRolePermissions();

        // Calcular total y páginas
        $total = count($rolePermissions);
        $lastPage = ceil($total / $perPage);

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $paginatedRolePermissions = array_slice($rolePermissions, $offset, $perPage);

        // Convertir los modelos a DTOs
        $rolePermissionDTOs = array_map(
            fn($rolePermission) => new RolePermissionResponseDTO(
                id: $rolePermission->getId(),
                roleId: $rolePermission->getRole()->getId(),
                permissionId: $rolePermission->getPermission()->getId(),
                active: $rolePermission->isActive()
            ),
            $paginatedRolePermissions
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
            items: $rolePermissionDTOs,
            meta: $meta
        );

        // Crear y retornar el DTO de respuesta
        return new RolePermissionListResponseDTO(
            data: $paginatedResponse
        );
    }
}
