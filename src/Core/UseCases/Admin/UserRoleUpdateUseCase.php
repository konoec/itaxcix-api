<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\UserRoleUpdateRequestDTO;
use itaxcix\Shared\DTO\useCases\Admin\UserRoleResponseDTO;

interface UserRoleUpdateUseCase
{
    public function execute(UserRoleUpdateRequestDTO $dto): ?UserRoleResponseDTO;
}
