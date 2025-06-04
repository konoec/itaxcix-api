<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\VerificationCodeRequestDTO;

interface VerifyRecoveryCodeUseCase
{
    public function execute(VerificationCodeRequestDTO $dto): ?array;
}