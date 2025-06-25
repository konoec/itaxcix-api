<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\PermissionUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionResponseDTO;

interface PermissionUpdateUseCase
{
    public function execute(PermissionUpdateRequestDTO $dto): ?PermissionResponseDTO;
}
