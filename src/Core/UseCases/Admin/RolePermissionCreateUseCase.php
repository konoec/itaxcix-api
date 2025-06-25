<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RolePermissionCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RolePermissionResponseDTO;

interface RolePermissionCreateUseCase
{
    public function execute(RolePermissionCreateRequestDTO $dto): ?RolePermissionResponseDTO;
}
