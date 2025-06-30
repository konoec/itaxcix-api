<?php

namespace itaxcix\Core\UseCases\User;

use itaxcix\Shared\DTO\useCases\User\DriverToCitizenRequestDTO;
use itaxcix\Shared\DTO\useCases\User\DriverToCitizenResponseDTO;

interface DriverToCitizenUseCase
{
    public function execute(DriverToCitizenRequestDTO $dto): DriverToCitizenResponseDTO;
}
