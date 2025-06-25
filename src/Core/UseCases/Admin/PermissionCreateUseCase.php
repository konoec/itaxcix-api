<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\PermissionCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\PermissionResponseDTO;

interface PermissionCreateUseCase
{
    public function execute(PermissionCreateRequestDTO $dto): ?PermissionResponseDTO;
}
