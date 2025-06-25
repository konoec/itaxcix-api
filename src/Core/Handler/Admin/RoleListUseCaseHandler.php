<?php

namespace itaxcix\Core\Handler\Admin;

use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\RoleListUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleResponseDTO;

class RoleListUseCaseHandler implements RoleListUseCase
{
    private RoleRepositoryInterface $roleRepository;

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function execute(int $page, int $perPage): ?RoleListResponseDTO
    {
        // Obtener todos los roles
        $roles = $this->roleRepository->findAllRoles();

        // Calcular total y páginas
        $total = count($roles);
        $lastPage = ceil($total / $perPage);

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $paginatedRoles = array_slice($roles, $offset, $perPage);

        // Convertir los modelos a DTOs
        $roleDTOs = array_map(
            fn($role) => new RoleResponseDTO(
                id: $role->getId(),
                name: $role->getName(),
                active: $role->isActive(),
                web: $role->isWeb()
            ),
            $paginatedRoles
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
            items: $roleDTOs,
            meta: $meta
        );

        // Crear y retornar el DTO de respuesta
        return new RoleListResponseDTO(
            data: $paginatedResponse
        );
    }
}
