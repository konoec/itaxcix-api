<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RoleListResponseDTO;

interface RoleListUseCase
{
    public function execute(int $page, int $perPage): ?RoleListResponseDTO;
}
