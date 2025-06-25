<?php

namespace itaxcix\Core\UseCases\Admin;

use itaxcix\Shared\DTO\useCases\Admin\RoleDeleteRequestDTO;

interface RoleDeleteUseCase
{
    public function execute(RoleDeleteRequestDTO $dto): void;
}
