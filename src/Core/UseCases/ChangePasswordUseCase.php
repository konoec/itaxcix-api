<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\PasswordChangeRequestDTO;

interface ChangePasswordUseCase
{
    public function execute(PasswordChangeRequestDTO $dto): ?array;

}