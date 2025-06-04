<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\AuthLoginRequestDTO;
use itaxcix\Shared\DTO\useCases\Auth\AuthLoginResponseDTO;

interface LoginUseCase
{
    public function execute(AuthLoginRequestDTO $dto): ?AuthLoginResponseDTO;
}