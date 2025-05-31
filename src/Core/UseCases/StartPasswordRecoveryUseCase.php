<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\RecoveryStartRequestDTO;

interface StartPasswordRecoveryUseCase
{
    public function execute(RecoveryStartRequestDTO $dto): ?array;
}