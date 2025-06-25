<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RoleCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\RoleResponseDTO;

interface RoleCreateUseCase
{
    public function execute(RoleCreateRequestDTO $dto): ?RoleResponseDTO;
}
