<?php

namespace itaxcix\Core\UseCases;

use itaxcix\Shared\DTO\useCases\RegistrationRequestDTO;

interface UserRegistrationUseCase
{
    public function execute(RegistrationRequestDTO $dto): ?array;

}