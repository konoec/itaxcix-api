<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\BiometricValidationRequestDTO;

interface BiometricValidationUseCase
{
    public function execute(BiometricValidationRequestDTO $dto): ?array;
}