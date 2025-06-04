<?php

namespace itaxcix\Core\UseCases\Auth;

use itaxcix\Shared\DTO\useCases\Auth\RegistrationRequestDTO;

interface UserRegistrationUseCase
{
    public function execute(RegistrationRequestDTO $dto): ?array;

}