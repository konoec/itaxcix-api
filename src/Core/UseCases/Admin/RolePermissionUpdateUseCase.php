<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RolePermissionUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionResponseDTO;

interface RolePermissionUpdateUseCase
{
    public function execute(RolePermissionUpdateRequestDTO $dto): ?RolePermissionResponseDTO;
}
