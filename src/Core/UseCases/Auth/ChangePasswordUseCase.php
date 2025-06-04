<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\PasswordChangeRequestDTO;

interface ChangePasswordUseCase
{
    public function execute(PasswordChangeRequestDTO $dto): ?array;

}