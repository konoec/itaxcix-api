<?php

namespace itaxcix\Core\UseCases\User;

use itaxcix\Shared\DTO\useCases\User\CitizenToDriverRequestDTO;
use itaxcix\Shared\DTO\useCases\User\CitizenToDriverResponseDTO;

interface CitizenToDriverUseCase
{
    public function execute(CitizenToDriverRequestDTO $dto): CitizenToDriverResponseDTO;
}
