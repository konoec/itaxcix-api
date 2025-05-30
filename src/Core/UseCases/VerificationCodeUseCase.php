<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\VerificationCodeRequestDTO;

interface VerificationCodeUseCase
{
    public function execute(VerificationCodeRequestDTO $dto): ?array;
}