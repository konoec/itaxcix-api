<?php

namespace itaxcix\Core\Handler\Admin;

use itaxcix\Core\Interfaces\user\UserRoleRepositoryInterface;
use itaxcix\Core\UseCases\Admin\UserRoleListUseCase;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleListResponseDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleResponseDTO;

class UserRoleListUseCaseHandler implements UserRoleListUseCase
{
    private UserRoleRepositoryInterface $userRoleRepository;

    public function __construct(UserRoleRepositoryInterface $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    public function execute(int $page, int $perPage): ?UserRoleListResponseDTO
    {
        // Obtener todas las asignaciones
        $userRoles = $this->userRoleRepository->findAllUserRoles();

        // Calcular total y páginas
        $total = count($userRoles);
        $lastPage = ceil($total / $perPage);

        // Aplicar paginación
        $offset = ($page - 1) * $perPage;
        $paginatedUserRoles = array_slice($userRoles, $offset, $perPage);

        // Convertir los modelos a DTOs
        $userRoleDTOs = array_map(
            fn($userRole) => new UserRoleResponseDTO(
                id: $userRole->getId(),
                userId: $userRole->getUser()->getId(),
                roleId: $userRole->getRole()->getId(),
                active: $userRole->isActive()
            ),
            $paginatedUserRoles
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
            items: $userRoleDTOs,
            meta: $meta
        );

        // Crear y retornar el DTO de respuesta
        return new UserRoleListResponseDTO(
            data: $paginatedResponse
        );
    }
}
