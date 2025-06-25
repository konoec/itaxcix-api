<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\UserRoleDeleteRequestDTO;

interface UserRoleDeleteUseCase
{
    public function execute(UserRoleDeleteRequestDTO $dto): void;
}
