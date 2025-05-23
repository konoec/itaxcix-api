<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\AuthLoginRequestDTO;
use itaxcix\Shared\DTO\useCases\AuthLoginResponseDTO;

interface LoginUseCase
{
    public function execute(AuthLoginRequestDTO $dto): ?AuthLoginResponseDTO;
}