<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\PermissionDeleteRequestDTO;

interface PermissionDeleteUseCase
{
    public function execute(PermissionDeleteRequestDTO $dto): void;
}
