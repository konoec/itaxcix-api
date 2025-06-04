<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\VerificationCodeRequestDTO;

interface VerificationCodeUseCase
{
    public function execute(VerificationCodeRequestDTO $dto): ?array;
}