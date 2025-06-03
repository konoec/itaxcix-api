<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\ResendVerificationCodeRequestDTO;

interface ResendVerificationCodeUseCase
{
    public function execute(ResendVerificationCodeRequestDTO $dto): array;
}