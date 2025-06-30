<?php

namespace itaxcix\Shared\DTO\Admin\Dashboard;

readonly class DashboardStatsResponseDTO
{
    public function __construct(
        public int $totalUsers,
        public int $activeUsers,
        public int $totalRoles,
        public int $webRoles,
        public int $totalPermissions,
        public int $webPermissions,
        public int $usersWithWebAccess,
        public float $userActivityPercentage,
        public float $webAccessPercentage
    ) {}
}
