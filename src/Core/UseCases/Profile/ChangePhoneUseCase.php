<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\ChangePhoneRequestDTO;

interface ChangePhoneUseCase
{
    public function execute(ChangePhoneRequestDTO $dto): ?array;
}
