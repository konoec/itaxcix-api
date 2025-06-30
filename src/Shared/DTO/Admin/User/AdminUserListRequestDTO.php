<?php

namespace itaxcix\Shared\DTO\Admin\User;

class AdminUserListRequestDTO
{
    public readonly int $page;
    public readonly int $limit;
    public readonly ?string $search;
    public readonly ?int $statusId;
    public readonly ?int $roleId;
    public readonly ?string $userType; // 'citizen', 'driver', 'admin'
    public readonly ?string $driverStatus; // 'PENDIENTE', 'APROBADO', 'RECHAZADO'
    public readonly ?bool $hasVehicle;
    public readonly ?bool $contactVerified;

    public function __construct(
        int $page = 1,
        int $limit = 20,
        ?string $search = null,
        ?int $statusId = null,
        ?int $roleId = null,
        ?string $userType = null,
        ?string $driverStatus = null,
        ?bool $hasVehicle = null,
        ?bool $contactVerified = null
    ) {
        $this->page = max(1, $page);
        $this->limit = max(1, min(100, $limit));
        $this->search = $search;
        $this->statusId = $statusId;
        $this->roleId = $roleId;
        $this->userType = $userType;
        $this->driverStatus = $driverStatus;
        $this->hasVehicle = $hasVehicle;
        $this->contactVerified = $contactVerified;
    }
}
