<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\UserRoleCreateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleResponseDTO;

interface UserRoleCreateUseCase
{
    public function execute(UserRoleCreateRequestDTO $dto): ?UserRoleResponseDTO;
}
