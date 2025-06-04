<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\RecoveryStartRequestDTO;

interface StartPasswordRecoveryUseCase
{
    public function execute(RecoveryStartRequestDTO $dto): ?array;
}