<?php

namespace itaxcix\Core\UseCases\Profile;

use itaxcix\Shared\DTO\useCases\Profile\VerifyEmailChangeRequestDTO;

interface VerifyEmailChangeUseCase
{
    public function execute(VerifyEmailChangeRequestDTO $dto): ?array;
}
