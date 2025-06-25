<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RolePermissionDeleteRequestDTO;

interface RolePermissionDeleteUseCase
{
    public function execute(RolePermissionDeleteRequestDTO $dto): void;
}
