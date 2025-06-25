<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RoleUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleResponseDTO;

interface RoleUpdateUseCase
{
    public function execute(RoleUpdateRequestDTO $dto): ?RoleResponseDTO;
}
