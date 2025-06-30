<?php

namespace itaxcix\Core\UseCases\Admin\Dashboard;

use itaxcix\Core\Interfaces\user\UserRepositoryInterface;
use itaxcix\Core\Interfaces\user\RoleRepositoryInterface;
use itaxcix\Core\Interfaces\user\PermissionRepositoryInterface;
use itaxcix\Shared\DTO\Admin\Dashboard\DashboardStatsResponseDTO;

class GetDashboardStatsUseCase
{
    private UserRepositoryInterface $userRepository;
    private RoleRepositoryInterface $roleRepository;
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function execute(): DashboardStatsResponseDTO
    {
        // Estadísticas generales
        $totalUsers = $this->userRepository->countAll();
        $totalRoles = $this->roleRepository->countAll();
        $totalPermissions = $this->permissionRepository->countAll();

        // Estadísticas específicas
        $activeUsers = $this->userRepository->countAll(statusId: 1); // Asumiendo que 1 es estado activo
        $webRoles = $this->roleRepository->countAll(webOnly: true);
        $webPermissions = $this->permissionRepository->countAll(webOnly: true);

        // Usuarios con acceso web
        $usersWithWebAccess = $this->userRepository->countAll(withWebAccess: true);

        return new DashboardStatsResponseDTO(
            totalUsers: $totalUsers,
            activeUsers: $activeUsers,
            totalRoles: $totalRoles,
            webRoles: $webRoles,
            totalPermissions: $totalPermissions,
            webPermissions: $webPermissions,
            usersWithWebAccess: $usersWithWebAccess,
            userActivityPercentage: $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0,
            webAccessPercentage: $totalUsers > 0 ? round(($usersWithWebAccess / $totalUsers) * 100, 2) : 0
        );
    }
}
