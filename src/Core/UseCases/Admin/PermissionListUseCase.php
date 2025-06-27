<?php

namespace itaxcix\Core\UseCases\Admin;

 use itaxcix\Shared\DTO\useCases\Admin\PermissionListResponseDTO;

interface PermissionListUseCase
{
    public function execute(int $page, int $perPage): ?PermissionListResponseDTO;
}
