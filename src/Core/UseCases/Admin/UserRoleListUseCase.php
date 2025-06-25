<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\UserRoleListResponseDTO;

interface UserRoleListUseCase
{
    public function execute(int $page, int $perPage): ?UserRoleListResponseDTO;
}
