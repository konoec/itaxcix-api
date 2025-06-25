<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\VerifyPhoneChangeRequestDTO;

interface VerifyPhoneChangeUseCase
{
    public function execute(VerifyPhoneChangeRequestDTO $dto): ?array;
}
