<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\BiometricValidationRequestDTO;

interface BiometricValidationUseCase
{
    public function execute(BiometricValidationRequestDTO $dto): ?array;
}