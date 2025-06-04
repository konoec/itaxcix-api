<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\ResendVerificationCodeRequestDTO;

interface ResendVerificationCodeUseCase
{
    public function execute(ResendVerificationCodeRequestDTO $dto): array;
}